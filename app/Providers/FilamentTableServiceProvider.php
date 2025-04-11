<?php

namespace App\Providers;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;

class FilamentTableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        TextColumn::configureUsing(function (TextColumn $column): void {
            $column->sortable();
        });

        Column::configureUsing(function (Column $column): void {
            $column->sortable();
        });
    }
}
