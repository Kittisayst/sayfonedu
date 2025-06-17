<?php

namespace App\Filament\Components;

use Filament\Tables\Columns\IconColumn;



trait FileTypeColumnIcon
{
    public static function make(string $name): IconColumn
    {
        return IconColumn::make($name)
            ->label('ປະເພດໄຟລ໌')
            ->tooltip(fn($state) => strtoupper($state))
            ->icon(fn(string $state): string => match (strtolower($state)) {
                'pdf' => 'heroicon-o-document-text',
                'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'heroicon-o-document-text', 
                'jpg', 'jpeg', 'png' => 'heroicon-o-photo',
                'xls', 'xlsx' => 'heroicon-o-table-cells',
                'ppt', 'pptx' => 'heroicon-o-presentation-chart-bar',
                'zip', 'rar' => 'heroicon-o-archive-box',
                default => 'heroicon-o-document',
            })
            ->color(fn(string $state): string => match (strtolower($state)) {
                'pdf' => 'danger',
                'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'info',
                'jpg', 'jpeg', 'png' => 'success', 
                'xls', 'xlsx' => 'warning',
                'ppt', 'pptx' => 'warning',
                default => 'gray',
            });
    }
}