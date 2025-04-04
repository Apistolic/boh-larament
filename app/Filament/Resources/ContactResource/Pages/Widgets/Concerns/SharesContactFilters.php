<?php

namespace App\Filament\Resources\ContactResource\Pages\Widgets\Concerns;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Cache;

trait SharesContactFilters
{
    public static ?string $contactFilter = '30days';
    public static ?string $grouping = 'day';

    public function updatedFilter($value)
    {
        static::$contactFilter = $value;
        Cache::put('contact_widget_filter', $value, now()->addDay());
        $this->refreshOtherWidgets();
    }

    public function updatedGrouping($value)
    {
        static::$grouping = $value;
        Cache::put('contact_widget_grouping', $value, now()->addDay());
        $this->refreshOtherWidgets();
    }

    public function refreshOtherWidgets(): void
    {
        $this->dispatch('contact-filters-updated');
    }

    public function mount(): void
    {
        static::$contactFilter = Cache::get('contact_widget_filter', '30days');
        static::$grouping = Cache::get('contact_widget_grouping', 'day');
    }

    protected function getFilter(): string
    {
        return static::$contactFilter;
    }

    protected function getGrouping(): string
    {
        return static::$grouping;
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

    public function getListeners(): array
    {
        return [
            'contact-filters-updated' => '$refresh',
        ];
    }
}
