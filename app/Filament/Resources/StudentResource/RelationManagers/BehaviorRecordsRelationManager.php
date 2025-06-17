<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BehaviorRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'behaviorRecords';
    protected static ?string $title = 'ຂໍ້ມູນການບັນທຶກພຶດຕິກຳ';
    protected static ?string $icon = 'heroicon-o-hand-thumb-up';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('record_type')
                    ->label('ປະເພດການບັນທຶກ')
                    ->options([
                        'positive' => 'ພຶດຕິກຳດີ',
                        'negative' => 'ພຶດຕິກຳບໍ່ດີ',
                        'warning' => 'ການເຕືອນ',
                        'improvement' => 'ການປັບປຸງ',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('ລາຍລະອຽດການບັນທຶກ')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Select::make('teacher_id')
                    ->label('ຄູຜູ້ບັນທຶກ')
                    ->relationship('teacher', 'first_name_lao')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('record_date')
                    ->label('ວັນທີບັນທຶກ')
                    ->required()
                    ->default(now()),

                Forms\Components\Textarea::make('action_taken')
                    ->label('ມາດຕະການທີ່ໄດ້ປະຕິບັດ')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('follow_up')
                    ->label('ການຕິດຕາມ')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('record_type')
                    ->label('ປະເພດການບັນທຶກ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'positive' => 'ພຶດຕິກຳດີ',
                        'negative' => 'ພຶດຕິກຳບໍ່ດີ',
                        'warning' => 'ການເຕືອນ',
                        'improvement' => 'ການປັບປຸງ',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'positive' => 'success',
                        'negative' => 'danger',
                        'warning' => 'warning',
                        'improvement' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('ລາຍລະອຽດ')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('teacher.first_name_lao')
                    ->label('ຄູຜູ້ບັນທຶກ')
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
                    ->label('ເພີ່ມການບັນທຶກພຶດຕິກຳ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມການບັນທຶກພຶດຕິກຳ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂການບັນທຶກພຶດຕິກຳ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
