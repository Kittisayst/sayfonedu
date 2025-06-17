<?php

namespace App\Filament\Resources\PermissionResource\RelationManagers;

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

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';
    protected static ?string $recordTitleAttribute = 'role_name';
    protected static ?string $title = 'ບົດບາດທີ່ໄດ້ຮັບສິດທິນີ້';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('role_name')
                    ->label('ຊື່ບົດບາດ')
                    ->required()
                    ->maxLength(50),

                Textarea::make('description')
                    ->label('ຄຳອະທິບາຍ')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role_name')
            ->columns([
                TextColumn::make('role_id')
                    ->label('ລະຫັດ')
                    ->sortable(),

                TextColumn::make('role_name')
                    ->label('ຊື່ບົດບາດ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('ຄຳອະທິບາຍ')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('ເພີ່ມບົດບາດ')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->label('ລຶບອອກ'),
            ]);
    }
}
