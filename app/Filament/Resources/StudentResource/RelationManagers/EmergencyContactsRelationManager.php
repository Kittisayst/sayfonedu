<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmergencyContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'emergencyContacts';

    protected static ?string $title = 'ຂໍ້ມູນການຕິດຕໍ່ສຸກເສີນ';

    protected static ?string $icon = 'heroicon-o-phone';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contact_name')
                    ->label('ຊື່ຜູ້ຕິດຕໍ່')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('relationship')
                    ->label('ສາຍພົວພັນ')
                    ->options([
                        'father' => 'ພໍ່',
                        'mother' => 'ແມ່',
                        'brother' => 'ອ້າຍ/ນ້ອງຊາຍ',
                        'sister' => 'ອ້າຍ/ນ້ອງຍິງ',
                        'grandfather' => 'ປູ່',
                        'grandmother' => 'ຍ່າ',
                        'uncle' => 'ອາ/ລຸງ',
                        'aunt' => 'ນາງ/ປ້າ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->required(),

                Forms\Components\TextInput::make('phone')
                    ->label('ເບີໂທລະສັບ')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('alternative_phone')
                    ->label('ເບີໂທລະສັບສຳຮອງ')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\Textarea::make('address')
                    ->label('ທີ່ຢູ່')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Select::make('priority')
                    ->label('ລຳດັບຄວາມສຳຄັນ')
                    ->options([
                        1 => 'ສຳຄັນທີ່ສຸດ',
                        2 => 'ສຳຄັນ',
                        3 => 'ປົກກະຕິ'
                    ])
                    ->required()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contact_name')
            ->columns([
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('ຊື່ຜູ້ຕິດຕໍ່')
                    ->searchable(),

                Tables\Columns\TextColumn::make('relationship')
                    ->label('ສາຍພົວພັນ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'father' => 'ພໍ່',
                        'mother' => 'ແມ່',
                        'brother' => 'ອ້າຍ/ນ້ອງຊາຍ',
                        'sister' => 'ອ້າຍ/ນ້ອງຍິງ',
                        'grandfather' => 'ປູ່',
                        'grandmother' => 'ຍ່າ',
                        'uncle' => 'ອາ/ລຸງ',
                        'aunt' => 'ນາງ/ປ້າ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('ເບີໂທລະສັບ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('alternative_phone')
                    ->label('ເບີໂທລະສັບສຳຮອງ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('ລຳດັບຄວາມສຳຄັນ')
                    ->formatStateUsing(fn(int $state): string => match ($state) {
                        1 => 'ສຳຄັນທີ່ສຸດ',
                        2 => 'ສຳຄັນ',
                        3 => 'ປົກກະຕິ',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຜູ້ຕິດຕໍ່ສຸກເສີນ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຜູ້ຕິດຕໍ່ສຸກເສີນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂຂໍ້ມູນຜູ້ຕິດຕໍ່ສຸກເສີນ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
