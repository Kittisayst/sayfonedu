<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ParentsRelationManager extends RelationManager
{
    protected static string $relationship = 'parents';

    protected static ?string $title = 'ຜູ້ປົກຄອງ';

    protected static ?string $icon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'first_name_lao';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('relationship')
                    ->label('ຄວາມສຳພັນ')
                    ->options([
                        'father' => 'ພໍ່',
                        'mother' => 'ແມ່',
                        'grandfather' => 'ປູ່/ພໍ່ເຖົ້າ',
                        'grandmother' => 'ຍ່າ/ແມ່ເຖົ້າ',
                        'uncle' => 'ລຸງ/ອາວ/ນ້າບ່າວ',
                        'aunt' => 'ປ້າ/ອາ/ນ້າສາວ',
                        'guardian' => 'ຜູ້ປົກຄອງອື່ນໆ'
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_primary_contact')
                    ->label('ເປັນຜູ້ຕິດຕໍ່ຫຼັກ')
                    ->default(false),
                Forms\Components\Toggle::make('has_custody')
                    ->label('ມີສິດປົກຄອງ')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name_lao')
            ->columns([
                Tables\Columns\ImageColumn::make('profile_image')
                    ->label('ຮູບ'),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('ຜູ້ປົກຄອງ')
                    ->searchable(['first_name_lao', 'last_name_lao', 'phone', 'occupation']),
                Tables\Columns\TextColumn::make('relationship')
                    ->label('ຄວາມສຳພັນ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'father' => 'ພໍ່',
                        'mother' => 'ແມ່',
                        'grandfather' => 'ປູ່/ພໍ່ເຖົ້າ',
                        'grandmother' => 'ຍ່າ/ແມ່ເຖົ້າ',
                        'uncle' => 'ລຸງ/ອາວ/ນ້າບ່າວ',
                        'aunt' => 'ປ້າ/ອາ/ນ້າສາວ',
                        'guardian' => 'ຜູ້ປົກຄອງອື່ນໆ',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_primary_contact')
                    ->label('ຜູ້ຕິດຕໍ່ຫຼັກ')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_custody')
                    ->label('ມີສິດປົກຄອງ')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຜູ້ປົກຄອງໃໝ່')
                    ->modalHeading('ເພີ່ມຜູ້ປົກຄອງໃໝ່')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name_lao')
                                    ->label('ຊື່ (ພາສາລາວ)')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name_lao')
                                    ->label('ນາມສະກຸນ (ພາສາລາວ)')
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->label('ເພດ')
                                    ->options([
                                        'male' => 'ຊາຍ',
                                        'female' => 'ຍິງ'
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('ເບີໂທ')
                                    ->tel()
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('relationship')
                            ->label('ຄວາມສຳພັນ')
                            ->options([
                                'father' => 'ພໍ່',
                                'mother' => 'ແມ່',
                                'grandfather' => 'ປູ່/ພໍ່ເຖົ້າ',
                                'grandmother' => 'ຍ່າ/ແມ່ເຖົ້າ',
                                'uncle' => 'ລຸງ/ອາວ/ນ້າບ່າວ',
                                'aunt' => 'ປ້າ/ອາ/ນ້າສາວ',
                                'guardian' => 'ຜູ້ປົກຄອງອື່ນໆ'
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_primary_contact')
                            ->label('ເປັນຜູ້ຕິດຕໍ່ຫຼັກ')
                            ->default(false),
                        Forms\Components\Toggle::make('has_custody')
                            ->label('ມີສິດປົກຄອງ')
                            ->default(true),
                    ]),
                Tables\Actions\AttachAction::make()
                    ->label('ເພີ່ມຜູ້ປົກຄອງທີ່ມີຢູ່ແລ້ວ')
                    ->modalHeading('ເພີ່ມຜູ້ປົກຄອງທີ່ມີຢູ່ແລ້ວ')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['first_name_lao', 'last_name_lao', 'phone', 'occupation'])
                    ->recordSelect(
                        fn (Forms\Components\Select $select) => $select
                            ->label('ຜູ້ປົກຄອງ')
                            ->placeholder('ເລືອກຜູ້ປົກຄອງ')
                            ->required()
                            ->searchable()
                    )
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['relationship'] = 'guardian';
                        $data['is_primary_contact'] = false;
                        $data['has_custody'] = true;
                        return $data;
                    })
                    ->requiresConfirmation(false)
                    ->modalSubmitActionLabel('ຢືນຢັນ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ'),
                Tables\Actions\DetachAction::make()
                    ->label('ລຶບອອກ'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->label('ລຶບອອກ'),
            ]);
    }

}