<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'ກິດຈະກຳນອກຫຼັກສູດຂອງນັກຮຽນ';

    protected static ?string $icon = 'heroicon-o-globe-americas';

    protected static ?string $modelLabel = 'ກິດຈະກຳນອກຫຼັກສູດຂອງນັກຮຽນ';
    
    

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('activity_name')
                    ->label('ຊື່ກິດຈະກຳ')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('activity_type')
                    ->label('ປະເພດກິດຈະກຳ')
                    ->options([
                        'sports' => 'ກິລາ',
                        'arts' => 'ສິລະປະ',
                        'music' => 'ດົນຕີ',
                        'dance' => 'ນັດລຳ',
                        'debate' => 'ການໂຕ້ວາທີ',
                        'volunteer' => 'ອາສາສະໝັກ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('ລາຍລະອຽດ')
                    ->maxLength(1000),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('ວັນທີເລີ່ມ')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('ວັນທີສິ້ນສຸດ')
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('schedule')
                    ->label('ເວລາຈັດຕັ້ງ')
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->label('ສະຖານທີ່')
                    ->maxLength(255),
                Forms\Components\TextInput::make('max_participants')
                    ->label('ຈຳນວນຜູ້ເຂົ້າຮ່ວມສູງສຸດ')
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('activity_name')
                    ->label('ຊື່ກິດຈະກຳ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('ປະເພດ')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sports' => 'ກິລາ',
                        'arts' => 'ສິລະປະ',
                        'music' => 'ດົນຕີ',
                        'dance' => 'ນັດລຳ',
                        'debate' => 'ການໂຕ້ວາທີ',
                        'volunteer' => 'ອາສາສະໝັກ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('ວັນທີເລີ່ມ')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('ວັນທີສິ້ນສຸດ')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('ສະຖານທີ່')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('ຈຳນວນຜູ້ເຂົ້າຮ່ວມ')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມກິດຈະກຳ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຂໍ້ມູນກິດຈະກຳນອກຫຼັກສູດ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ'),
            ]);
    }
}
