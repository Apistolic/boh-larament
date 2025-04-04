<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\Touch;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TouchesByLifecycleWidget extends ChartWidget
{
    protected static ?string $heading = 'Touches by Lifecycle Category';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    public ?string $filter = 'month';

    protected function getData(): array
    {
        $startDate = now()->subMonths(3)->startOfDay();
        $endDate = now()->endOfDay();

        $touches = Touch::query()
            ->join('contacts', 'contacts.id', '=', 'touches.contact_id')
            ->whereBetween('touches.created_at', [$startDate, $endDate])
            ->get([
                'touches.created_at',
                'touches.type',
                'contacts.lifecycle_stage'
            ])
            ->map(function ($touch) {
                return [
                    'date' => $touch->created_at->format('Y-m-d'),
                    'type' => $touch->type,
                    'lifecycle_category' => $this->getLifecycleCategory($touch->lifecycle_stage),
                ];
            });

        $dates = collect();
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dates->push($current->format('Y-m-d'));
            $current->addDay();
        }

        // Group and count by date and category
        $touchesByCategory = $touches->groupBy('date')
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('lifecycle_category')
                    ->map(function ($categoryGroup) {
                        return $categoryGroup->count();
                    });
            });

        $datasets = [];
        $categories = $touches->pluck('lifecycle_category')->unique();

        foreach ($categories as $category) {
            // Get the base color for the lifecycle category
            $baseColor = match($category) {
                'Donor' => '75, 192, 192',  // Teal for donors
                'Neighbor' => '54, 162, 235',  // Blue for neighbors
                'Mom' => '153, 102, 255',  // Purple for moms
                'Gala' => '255, 159, 64',  // Orange for gala
                default => '201, 203, 207'  // Gray for others
            };

            $data = $dates->map(function ($date) use ($touchesByCategory, $category) {
                return $touchesByCategory[$date][$category] ?? 0;
            })->toArray();

            // Skip empty datasets
            if (!array_sum($data)) {
                continue;
            }

            $datasets[] = [
                'label' => $category,
                'data' => $data,
                'borderColor' => "rgba($baseColor, 0.8)",
                'backgroundColor' => "rgba($baseColor, 0.1)",
                'fill' => true,
            ];
        }

        return [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('M j'))->toArray(),
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'stacked' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'x' => [
                    'stacked' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getLifecycleCategory(string $stage): string
    {
        return match(true) {
            str_contains($stage, 'donor') => 'Donor',
            str_contains($stage, 'neighbor') => 'Neighbor',
            str_contains($stage, 'mom') => 'Mom',
            str_contains($stage, 'gala') => 'Gala',
            default => 'Other'
        };
    }
}
