<?php

namespace App\Filament\Resources\ContactResource\Pages\Widgets;

use App\Models\Touch;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TouchTrendsWidget extends ChartWidget
{
    protected static ?string $heading = 'Touch History';
    protected static ?string $maxHeight = '200px';
    protected static ?string $pollingInterval = null;

    public ?string $filter = '30days';
    public ?string $grouping = 'day';
    
    public $record;

    protected function getData(): array
    {
        $startDate = match($this->filter) {
            '7days' => now()->subDays(7)->startOfDay(),
            '30days' => now()->subDays(30)->startOfDay(),
            '90days' => now()->subDays(90)->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };
        $endDate = now()->endOfDay();
        
        $dateFormat = match($this->grouping) {
            'week' => 'DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY))',
            'month' => 'DATE_FORMAT(created_at, "%Y-%m-01")',
            default => 'DATE(created_at)', // day
        };
        
        $touches = $this->record->workflowTouches()
            ->select([
                DB::raw("$dateFormat as date"),
                DB::raw('COUNT(*) as count'),
                'type'
            ])
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy(DB::raw($dateFormat), 'type')
            ->orderBy('date')
            ->get();

        $dates = collect();
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $date = match($this->grouping) {
                'week' => $current->startOfWeek()->format('Y-m-d'),
                'month' => $current->startOfMonth()->format('Y-m-d'),
                default => $current->format('Y-m-d'),
            };
            
            if (!$dates->contains($date)) {
                $dates->push($date);
            }
            
            $current->addDay();
        }

        $datasets = [];
        $types = $touches->pluck('type')->unique();
        
        foreach ($types as $type) {
            $data = $dates->map(function ($date) use ($touches, $type) {
                return $touches
                    ->where('date', $date)
                    ->where('type', $type)
                    ->first()?->count ?? 0;
            })->toArray();

            // Skip empty datasets
            if (!array_sum($data)) {
                continue;
            }

            $color = match($type) {
                Touch::TYPE_EMAIL => '75, 192, 192',
                Touch::TYPE_SMS => '255, 205, 86',
                Touch::TYPE_CALL => '54, 162, 235',
                Touch::TYPE_LETTER => '153, 102, 255',
                default => '201, 203, 207'
            };

            $datasets[] = [
                'label' => ucfirst($type),
                'data' => $data,
                'borderColor' => "rgb($color)",
                'backgroundColor' => "rgba($color, 0.2)",
                'fill' => true,
            ];
        }

        $formatLabel = function($date) {
            $carbon = Carbon::parse($date);
            return match($this->grouping) {
                'week' => $carbon->format('M j') . ' - ' . $carbon->endOfWeek()->format('M j'),
                'month' => $carbon->format('M Y'),
                default => $carbon->format('M j'),
            };
        };

        return [
            'labels' => $dates->map($formatLabel)->toArray(),
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7days' => 'Last 7 days',
            '30days' => 'Last 30 days',
            '90days' => 'Last 90 days',
        ];
    }
    
    protected function getExtraFilters(): ?array
    {
        return [
            'grouping' => [
                'label' => 'Group By',
                'options' => [
                    'day' => 'Day',
                    'week' => 'Week',
                    'month' => 'Month',
                ],
            ],
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
