<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\WorkflowExecution;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class WorkflowsByLifecycleWidget extends ChartWidget
{
    protected static ?string $heading = 'Workflow Activity by Lifecycle Category';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    public ?string $filter = 'month';

    protected function getData(): array
    {
        $startDate = now()->subMonths(3)->startOfDay();
        $endDate = now()->endOfDay();

        $executions = WorkflowExecution::query()
            ->join('contacts', 'contacts.id', '=', 'workflow_executions.contact_id')
            ->with('workflow:id,name')
            ->whereBetween('workflow_executions.created_at', [$startDate, $endDate])
            ->get([
                'workflow_executions.created_at',
                'workflow_executions.status',
                'workflow_executions.workflow_id',
                'contacts.lifecycle_stage'
            ])
            ->map(function ($execution) {
                return [
                    'date' => $execution->created_at->format('Y-m-d'),
                    'lifecycle_category' => $this->getLifecycleCategory($execution->lifecycle_stage),
                ];
            });

        $dates = collect();
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dates->push($current->format('Y-m-d'));
            $current->addDay();
        }

        // Group and count by date and category
        $executionsByCategory = $executions->groupBy('date')
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('lifecycle_category')
                    ->map(function ($categoryGroup) {
                        return $categoryGroup->count();
                    });
            });

        $datasets = [];
        $categories = $executions->pluck('lifecycle_category')->unique();

        foreach ($categories as $category) {
            // Get the base color for the lifecycle category
            $baseColor = match($category) {
                'Donor' => '75, 192, 192',  // Teal for donors
                'Neighbor' => '54, 162, 235',  // Blue for neighbors
                'Mom' => '153, 102, 255',  // Purple for moms
                'Gala' => '255, 159, 64',  // Orange for gala
                default => '201, 203, 207'  // Gray for others
            };

            $data = $dates->map(function ($date) use ($executionsByCategory, $category) {
                return $executionsByCategory[$date][$category] ?? 0;
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
