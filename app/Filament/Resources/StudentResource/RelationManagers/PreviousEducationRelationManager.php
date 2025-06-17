<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreviousEducationRelationManager extends RelationManager
{
    protected static string $relationship = 'previousEducation';

    protected static ?string $title = 'ຂໍ້ມູນການສຶກສາກ່ອນໜ້າ';

    protected static ?string $icon = 'heroicon-o-book-open';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('school_name')
                    ->label('ຊື່ໂຮງຮຽນ')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('education_level')
                    ->label('ຊັ້ນການສຶກສາ')
                    ->options([
                        'primary' => 'ປະຖົມສຶກສາ',
                        'lower_secondary' => 'ມັດທະຍົມຕົ້ນ',
                        'upper_secondary' => 'ມັດທະຍົມປາຍ',
                        'vocational' => 'ວິຊາຊີບ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->required(),

                Forms\Components\TextInput::make('from_year')
                    ->label('ປີທີ່ເລີ່ມ')
                    ->numeric()
                    ->required()
                    ->minValue(2000)
                    ->maxValue(now()->year),

                Forms\Components\TextInput::make('to_year')
                    ->label('ປີທີ່ຈົບ')
                    ->numeric()
                    ->required()
                    ->minValue(2000)
                    ->maxValue(now()->year),

                Forms\Components\TextInput::make('certificate')
                    ->label('ໃບຢັ້ງຢືນ')
                    ->maxLength(255),

                Forms\Components\TextInput::make('gpa')
                    ->label('GPA')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(4)
                    ->step(0.01),

                Forms\Components\Textarea::make('description')
                    ->label('ຄຳອະທິບາຍ')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('school_name')
            ->columns([
                Tables\Columns\TextColumn::make('school_name')
                    ->label('ຊື່ໂຮງຮຽນ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('education_level')
                    ->label('ຊັ້ນການສຶກສາ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'primary' => 'ປະຖົມສຶກສາ',
                        'lower_secondary' => 'ມັດທະຍົມຕົ້ນ',
                        'upper_secondary' => 'ມັດທະຍົມປາຍ',
                        'vocational' => 'ວິຊາຊີບ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('from_year')
                    ->label('ປີທີ່ເລີ່ມ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_year')
                    ->label('ປີທີ່ຈົບ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gpa')
                    ->label('GPA')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຂໍ້ມູນການສຶກສາກ່ອນໜ້າ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຂໍ້ມູນການສຶກສາກ່ອນໜ້າ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂຂໍ້ມູນການສຶກສາກ່ອນໜ້າ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
