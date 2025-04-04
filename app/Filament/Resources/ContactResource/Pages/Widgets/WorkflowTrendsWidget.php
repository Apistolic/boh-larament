<?php

namespace App\Filament\Resources\ContactResource\Pages\Widgets;

use App\Filament\Resources\ContactResource\Pages\Widgets\Concerns\SharesContactFilters;
use App\Models\WorkflowExecution;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class WorkflowTrendsWidget extends ChartWidget
{
    use SharesContactFilters;

    protected static ?string $heading = 'Workflow Activity';
    protected static ?string $maxHeight = '200px';
    protected static ?string $pollingInterval = null;
    
    public $record;

    protected function getData(): array
    {
        $startDate = match($this->getFilter()) {
            '7days' => now()->subDays(7)->startOfDay(),
            '30days' => now()->subDays(30)->startOfDay(),
            '90days' => now()->subDays(90)->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };
        $endDate = now()->endOfDay();
        
        $dateFormat = match($this->getGrouping()) {
            'week' => 'DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY))',
            'month' => 'DATE_FORMAT(created_at, "%Y-%m-01")',
            default => 'DATE(created_at)', // day
        };
        
        $executions = $this->record->workflowExecutions()
            ->select([
                DB::raw("$dateFormat as date"),
                DB::raw('COUNT(*) as count'),
                'status',
                'workflow_id'
            ])
            ->with('workflow:id,name')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy(DB::raw($dateFormat), 'status', 'workflow_id')
            ->orderBy('date')
            ->get();

        $dates = collect();
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $date = match($this->getGrouping()) {
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
        $workflowGroups = $executions->groupBy(fn($execution) => $execution->workflow?->name ?? 'Unknown');
        
        foreach ($workflowGroups as $workflowName => $workflowExecutions) {
            $statuses = $workflowExecutions->pluck('status')->unique();
            
            foreach ($statuses as $status) {
                $data = $dates->map(function ($date) use ($workflowExecutions, $status) {
                    return $workflowExecutions
                        ->where('date', $date)
                        ->where('status', $status)
                        ->sum('count');
                })->toArray();

                // Skip empty datasets
                if (!array_sum($data)) {
                    continue;
                }

                $baseColor = match($status) {
                    'completed' => '75, 192, 192',  // Green
                    'failed' => '255, 99, 132',     // Red
                    'pending' => '255, 205, 86',    // Yellow
                    'in_progress' => '54, 162, 235', // Blue
                    default => '201, 203, 207'      // Gray
                };

                $datasets[] = [
                    'label' => "$workflowName ($status)",
                    'data' => $data,
                    'borderColor' => "rgb($baseColor)",
                    'backgroundColor' => "rgba($baseColor, 0.2)",
                    'fill' => true,
                ];
            }
        }

        $formatLabel = function($date) {
            $carbon = Carbon::parse($date);
            return match($this->getGrouping()) {
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
