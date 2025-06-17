<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HealthRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'healthRecords';

    protected static ?string $title = 'ຂໍ້ມູນສຸຂະພາບ';

    protected static ?string $icon = 'heroicon-o-heart';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('health_condition')
                    ->label('ອາການສຸຂະພາບ')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('medications')
                    ->label('ຢາທີ່ໃຊ້')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('allergies')
                    ->label('ອາການແພ້')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('special_needs')
                    ->label('ຄວາມຕ້ອງການພິເສດ')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('doctor_name')
                    ->label('ຊື່ແພດ')
                    ->maxLength(255),

                Forms\Components\TextInput::make('doctor_phone')
                    ->label('ເບີໂທລະສັບແພດ')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\DatePicker::make('record_date')
                    ->label('ວັນທີບັນທຶກ')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('health_condition')
            ->columns([
                Tables\Columns\TextColumn::make('health_condition')
                    ->label('ອາການສຸຂະພາບ')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('medications')
                    ->label('ຢາທີ່ໃຊ້')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('allergies')
                    ->label('ອາການແພ້')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_name')
                    ->label('ຊື່ແພດ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('record_date')
                    ->label('ວັນທີບັນທຶກ')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຂໍ້ມູນສຸຂະພາບ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຂໍ້ມູນສຸຂະພາບນັກຮຽນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂຂໍ້ມູນສຸຂະພາບ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
