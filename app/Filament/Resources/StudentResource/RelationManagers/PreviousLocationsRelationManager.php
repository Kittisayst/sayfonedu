<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreviousLocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'previousLocations';
    protected static ?string $title = 'ປະຫວັດທີ່ຢູ່ກ່ອນຫນ້າ';
    protected static ?string $icon = 'heroicon-o-map-pin';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('address')
                    ->label('ທີ່ຢູ່')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Select::make('province_id')
                    ->label('ແຂວງ')
                    ->relationship('province', 'province_name_lao')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('district_id', null);
                        $set('village_id', null);
                    }),

                Forms\Components\Select::make('district_id')
                    ->label('ເມືອງ')
                    ->options(fn(Forms\Get $get) => \App\Models\District::query()
                        ->where('province_id', $get('province_id'))
                        ->pluck('district_name_lao', 'district_id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('village_id', null)),

                Forms\Components\Select::make('village_id')
                    ->label('ບ້ານ')
                    ->options(fn(Forms\Get $get) => \App\Models\Village::query()
                        ->where('district_id', $get('district_id'))
                        ->pluck('village_name_lao', 'village_id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('village_name_lao')
                            ->label('ຊື່ບ້ານ (ພາສາລາວ)')
                            ->required(),
                        Forms\Components\TextInput::make('village_name_en')
                            ->label('ຊື່ບ້ານ (ພາສາອັງກິດ)'),
                        Forms\Components\Hidden::make('district_id')
                            ->default(function (Forms\Get $get) {
                                return $get('district_id');
                            }),
                    ])
                    ->createOptionUsing(function (array $data, Forms\Get $get) {
                        $village = \App\Models\Village::create([
                            'village_name_lao' => $data['village_name_lao'],
                            'village_name_en' => $data['village_name_en'] ?? null,
                            'district_id' => $get('district_id'),
                        ]);
                        return $village->village_id;
                    })
                    ->createOptionAction(
                        fn(Forms\Components\Actions\Action $action) => $action
                            ->modalHeading('ເພີ່ມບ້ານໃໝ່')
                            ->modalSubmitActionLabel('ບັນທຶກ')
                            ->modalCancelActionLabel('ຍົກເລີກ')
                    ),

                Forms\Components\TextInput::make('country')
                    ->label('ປະເທດ')
                    ->default('ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('from_date')
                    ->label('ວັນທີເລີ່ມຢູ່')
                    ->required()
                    ->default(now()),

                Forms\Components\DatePicker::make('to_date')
                    ->label('ວັນທີຍ້າຍອອກ')
                    ->after('from_date'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                Tables\Columns\TextColumn::make('address')
                    ->label('ທີ່ຢູ່')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('province.province_name_lao')
                    ->label('ແຂວງ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('district.district_name_lao')
                    ->label('ເມືອງ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('village.village_name_lao')
                    ->label('ບ້ານ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('from_date')
                    ->label('ວັນທີເລີ່ມຢູ່')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_date')
                    ->label('ວັນທີຍ້າຍອອກ')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມປະຫວັດທີ່ຢູ່ກ່ອນໜ້າ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມປະຫວັດທີ່ຢູ່ກ່ອນໜ້າ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂປະຫວັດທີ່ຢູ່ກ່ອນໜ້າ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
