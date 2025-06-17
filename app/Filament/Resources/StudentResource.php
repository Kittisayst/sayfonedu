<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນນັກຮຽນ';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'ນັກຮຽນ';

    protected static ?string $recordTitleAttribute = 'student_code';

    protected static ?string $pluralModelLabel = 'ນັກຮຽນທົງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('ຂໍ້ມູນນັກຮຽນ')
                    ->tabs([
                        // ແທັບຂໍ້ມູນພື້ນຖານ
                        Forms\Components\Tabs\Tab::make('ຂໍ້ມູນພື້ນຖານ')
                            ->schema([
                                Forms\Components\FileUpload::make('profile_image')
                                    ->label('ຮູບໂປຣໄຟລ໌')
                                    ->image()
                                    ->directory('student-profiles'),
                                Forms\Components\TextInput::make('student_code')
                                    ->label('ລະຫັດນັກຮຽນ')
                                    ->required()
                                    ->maxLength(255),
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
                                        Forms\Components\TextInput::make('first_name_en')
                                            ->label('ຊື່ (ພາສາອັງກິດ)'),
                                        Forms\Components\TextInput::make('last_name_en')
                                            ->label('ນາມສະກຸນ (ພາສາອັງກິດ)'),
                                    ]),
                                Forms\Components\TextInput::make('nickname')
                                    ->label('ຊື່ຫຼິ້ນ'),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('gender')
                                            ->label('ເພດ')
                                            ->options([
                                                'male' => 'ຊາຍ',
                                                'female' => 'ຍິງ'
                                            ])
                                            ->required(),
                                        Forms\Components\DatePicker::make('date_of_birth')
                                            ->label('ວັນເດືອນປີເກີດ')
                                            ->required(),
                                    ]),
                            ]),

                        // ແທັບຂໍ້ມູນທີ່ຢູ່
                        Forms\Components\Tabs\Tab::make('ຂໍ້ມູນທີ່ຢູ່')
                            ->schema([
                                Forms\Components\Select::make('province_id')
                                    ->label('ແຂວງ')
                                    ->relationship('province', 'province_name_lao')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set) => $set('district_id', null)),
                                Forms\Components\Select::make('district_id')
                                    ->label('ເມືອງ')
                                    ->options(function (callable $get) {
                                        $provinceId = $get('province_id');
                                        if (!$provinceId) {
                                            return [];
                                        }
                                        return \App\Models\District::where('province_id', $provinceId)
                                            ->pluck('district_name_lao', 'district_id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set) => $set('village_id', null)),
                                Forms\Components\Select::make('village_id')
                                    ->label('ບ້ານ')
                                    ->options(function (callable $get) {
                                        $districtId = $get('district_id');
                                        if (!$districtId) {
                                            return [];
                                        }
                                        return \App\Models\Village::where('district_id', $districtId)
                                            ->pluck('village_name_lao', 'village_id');
                                    })
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
                                        fn (Forms\Components\Actions\Action $action) => $action
                                            ->modalHeading('ເພີ່ມບ້ານໃໝ່')
                                            ->modalSubmitActionLabel('ບັນທຶກ')
                                            ->modalCancelActionLabel('ຍົກເລີກ')
                                    ),
                                Forms\Components\Textarea::make('current_address')
                                    ->label('ທີ່ຢູ່ປັດຈຸບັນ'),
                            ]),

                        // ແທັບຂໍ້ມູນອື່ນໆ
                        Forms\Components\Tabs\Tab::make('ຂໍ້ມູນອື່ນໆ')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('nationality_id')
                                            ->label('ສັນຊາດ')
                                            ->relationship('nationality', 'nationality_name_lao')
                                            ->required(),
                                        Forms\Components\Select::make('religion_id')
                                            ->label('ສາສະໜາ')
                                            ->relationship('religion', 'religion_name_lao')
                                            ->required(),
                                        Forms\Components\Select::make('ethnicity_id')
                                            ->label('ຊົນເຜົ່າ')
                                            ->relationship('ethnicity', 'ethnicity_name_lao')
                                            ->required(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('blood_type')
                                            ->label('ກຸ່ມເລືອດ')
                                            ->options([
                                                'A' => 'A',
                                                'B' => 'B',
                                                'AB' => 'AB',
                                                'O' => 'O'
                                            ]),
                                        Forms\Components\DatePicker::make('admission_date')
                                            ->label('ວັນທີເຂົ້າຮຽນ')
                                            ->required(),
                                    ]),
                                Forms\Components\Select::make('status')
                                    ->label('ສະຖານະ')
                                    ->options([
                                        'active' => 'ກຳລັງຮຽນ',
                                        'graduated' => 'ຈົບການສຶກສາ',
                                        'suspended' => 'ພັກການຮຽນຊົ່ວຄາວ',
                                        'withdrawn' => 'ຖອນຕົວ'
                                    ])
                                    ->required(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_image')
                    ->label('ຮູບ')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-student-profile.png')),
                Tables\Columns\TextColumn::make('student_code')
                    ->label('ລະຫັດ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name_lao')
                    ->label('ຊື່')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name_lao')
                    ->label('ນາມສະກຸນ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('ເພດ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'male' => 'ຊາຍ',
                        'female' => 'ຍິງ',
                        'other' => 'ອື່ນໆ',
                        default => 'male',
                    }),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('ວັນເດືອນປີເກີດ')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'ກຳລັງຮຽນ',
                        'graduated' => 'ຈົບການສຶກສາ',
                        'suspended' => 'ພັກການຮຽນຊົ່ວຄາວ',
                        'withdrawn' => 'ຖອນຕົວ'
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'graduated' => 'info',
                        'suspended' => 'warning',
                        'withdrawn' => 'danger',
                    }),
            ])

            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'active' => 'ກຳລັງຮຽນ',
                        'graduated' => 'ຈົບການສຶກສາ',
                        'suspended' => 'ພັກການຮຽນຊົ່ວຄາວ',
                        'withdrawn' => 'ຖອນຕົວ'
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('ເພດ')
                    ->options([
                        'male' => 'ຊາຍ',
                        'female' => 'ຍິງ',
                        'other' => 'ອື່ນໆ',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('ສະແດງ')->icon('heroicon-o-eye')->color('info'),
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()->label('ລືບ'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ParentsRelationManager::class,
            RelationManagers\SiblingsRelationManager::class,
            RelationManagers\PreviousLocationsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\InterestsRelationManager::class,
            RelationManagers\EmergencyContactsRelationManager::class,
            RelationManagers\HealthRecordsRelationManager::class,
            RelationManagers\SpecialNeedsRelationManager::class,
            RelationManagers\PreviousEducationRelationManager::class,
            RelationManagers\AchievementsRelationManager::class,
            RelationManagers\BehaviorRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

}
