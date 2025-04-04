<?php

namespace App\Filament\Resources\ContactResource\Pages\Widgets;

use App\Models\WorkflowExecution;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class WorkflowTrendsWidget extends ChartWidget
{
    protected static ?string $heading = 'Workflow Activity';
    protected static ?string $maxHeight = '200px';
    protected static ?string $pollingInterval = null;

    public ?string $filter = 'month';
    
    public $record;

    protected function getData(): array
    {
        $startDate = now()->subMonths(3)->startOfDay();
        $endDate = now()->endOfDay();

        $executions = $this->record->workflowExecutions()
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                'status',
                'workflow_id'
            ])
            ->with('workflow:id,name')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('date', 'status', 'workflow_id')
            ->orderBy('date')
            ->get();

        $dates = collect();
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dates->push($current->format('Y-m-d'));
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
