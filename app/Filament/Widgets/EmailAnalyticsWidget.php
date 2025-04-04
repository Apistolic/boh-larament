<?php

namespace App\Filament\Widgets;

use App\Models\EmailSend;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EmailAnalyticsWidget extends ChartWidget
{
    protected static ?string $heading = 'Email Analytics';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    public ?string $filter = '30days';

    protected function getData(): array
    {
        $startDate = match($this->filter) {
            '7days' => now()->subDays(7)->startOfDay(),
            '30days' => now()->subDays(30)->startOfDay(),
            '90days' => now()->subDays(90)->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };

        $dates = collect();
        $current = $startDate->copy();
        while ($current <= now()) {
            $dates->push($current->format('Y-m-d'));
            $current->addDay();
        }

        // Get daily counts
        $dailySends = EmailSend::query()
            ->whereBetween('sent_at', [$startDate, now()])
            ->selectRaw('DATE(sent_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $dailyOpens = EmailSend::query()
            ->join('email_opens', 'email_sends.id', '=', 'email_opens.email_send_id')
            ->whereBetween('email_opens.opened_at', [$startDate, now()])
            ->selectRaw('DATE(email_opens.opened_at) as date, COUNT(DISTINCT email_sends.id) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $dailyClicks = EmailSend::query()
            ->join('email_clicks', 'email_sends.id', '=', 'email_clicks.email_send_id')
            ->whereBetween('email_clicks.clicked_at', [$startDate, now()])
            ->selectRaw('DATE(email_clicks.clicked_at) as date, COUNT(DISTINCT email_sends.id) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('M j'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Emails Sent',
                    'data' => $dates->map(fn($date) => $dailySends[$date] ?? 0)->toArray(),
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Unique Opens',
                    'data' => $dates->map(fn($date) => $dailyOpens[$date] ?? 0)->toArray(),
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Unique Clicks',
                    'data' => $dates->map(fn($date) => $dailyClicks[$date] ?? 0)->toArray(),
                    'borderColor' => 'rgb(153, 102, 255)',
                    'backgroundColor' => 'rgba(153, 102, 255, 0.1)',
                    'fill' => true,
                ],
            ],
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
}
