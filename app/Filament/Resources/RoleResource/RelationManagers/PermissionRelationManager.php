<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionRelationManager extends RelationManager
{
    protected static string $relationship = 'permission';
    protected static ?string $recordTitleAttribute = 'permission_name';
    protected static ?string $title = 'ສິດທິຕ່າງໆ';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('permission_name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('ຄຳອະທິບາຍໜ້າທີ່ຂອງສິດທິ')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('permission_id')
            ->columns([
                TextColumn::make('permission_id')
                ->label('ລະຫັດ')
                ->sortable(),
                TextColumn::make('permission_name')
                ->label('ຊື່ສິດທິ')
                ->sortable()
                ->searchable(),
                Textcolumn::make('description')
                ->label('ຄຳອະທິບາຍໜ້າທີ່ຂອງສິດທິ')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('ເພີ່ມສິດທິໃໝ່')->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label('ລຶບອອກ'),
            ]);
    }
}
