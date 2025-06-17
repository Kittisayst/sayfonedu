<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers\DocumentsRelationManager;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'ການຈັດການຂໍ້ມູນຄູສອນ';
    protected static ?string $navigationLabel = 'ຄູສອນ';
    protected static ?string $pluralModelLabel = 'ຄູສອນທັງໝົດ';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ຮູບໂປຣໄຟລ໌')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_image')
                            ->label('ຮູບໂປຣໄຟລ໌')
                            ->image()
                            ->directory('teacher-profiles'),
                    ]),
                Forms\Components\Section::make('ຂໍ້ມູນພື້ນຖານ')
                    ->schema([
                        Forms\Components\TextInput::make('teacher_code')
                            ->label('ລະຫັດຄູສອນ')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name_lao')
                                    ->label('ຊື່ (ພາສາລາວ)')
                                    ->required(),

                                Forms\Components\TextInput::make('last_name_lao')
                                    ->label('ນາມສະກຸນ (ພາສາລາວ)')
                                    ->required(),

                                Forms\Components\TextInput::make('first_name_en')
                                    ->label('First Name')
                                    ->required(),

                                Forms\Components\TextInput::make('last_name_en')
                                    ->label('Last Name')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('ວັນເດືອນປີເກີດ')
                                    ->required(),

                                Forms\Components\TextInput::make('national_id')
                                    ->label('ເລກບັດປະຈຳຕົວ')
                                    ->required(),
                                Forms\Components\Radio::make('gender')
                                    ->label('ເພດ')
                                    ->options([
                                        'male' => 'ຊາຍ',
                                        'female' => 'ຍິງ',
                                    ])
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('ຂໍ້ມູນການຕິດຕໍ່')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('ເບີໂທ')
                                    ->tel()
                                    ->required(),

                                Forms\Components\TextInput::make('alternative_phone')
                                    ->label('ເບີໂທສຳຮອງ')
                                    ->tel(),

                                Forms\Components\TextInput::make('email')
                                    ->label('ອີເມວ')
                                    ->email(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('province_id')
                                    ->label('ແຂວງ')
                                    ->relationship('province', 'province_name_lao')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('district_id', null)),

                                Forms\Components\Select::make('district_id')
                                    ->label('ເມືອງ')
                                    ->relationship('district', 'district_name_lao', function (Builder $query, callable $get) {
                                        $provinceId = $get('province_id');
                                        if ($provinceId) {
                                            $query->where('province_id', $provinceId);
                                        }
                                    })
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('village_id', null)),

                                Forms\Components\Select::make('village_id')
                                    ->label('ບ້ານ')
                                    ->relationship('village', 'village_name_lao', function (Builder $query, callable $get) {
                                        $districtId = $get('district_id');
                                        if ($districtId) {
                                            $query->where('district_id', $districtId);
                                        }
                                    })
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('ທີ່ຢູ່')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('ຂໍ້ມູນການສຶກສາ ແລະ ການເຮັດວຽກ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('highest_education')
                                    ->label('ລະດັບການສຶກສາສູງສຸດ')
                                    ->options([
                                        'high_school' => 'ມັດທະຍົມປາຍ',
                                        'diploma' => 'ຊັ້ນສູງ',
                                        'bachelor' => 'ປະລິນຍາຕີ',
                                        'master' => 'ປະລິນຍາໂທ',
                                        'phd' => 'ປະລິນຍາເອກ',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('specialization')
                                    ->label('ສາຂາວິຊາ')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('employment_date')
                                    ->label('ວັນທີເລີ່ມເຮັດວຽກ')
                                    ->required(),

                                Forms\Components\Select::make('contract_type')
                                    ->label('ປະເພດສັນຍາ')
                                    ->options([
                                        'full_time' => 'ເຕັມເວລາ',
                                        'part_time' => 'ບາງເວລາ',
                                        'temporary' => 'ຊົ່ວຄາວ',
                                    ])
                                    ->required(),

                                Forms\Components\Radio::make('status')
                                    ->label('ສະຖານະ')
                                    ->options([
                                        'active' => 'ກຳລັງເຮັດວຽກ',
                                        'inactive' => 'ຢຸດເຮັດວຽກຊົ່ວຄາວ',
                                        'resigned' => 'ລາອອກ',
                                    ])
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_image')
                    ->label('ຮູບ')
                    ->circular(),

                Tables\Columns\TextColumn::make('teacher_code')
                    ->label('ລະຫັດຄູສອນ')
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

                Tables\Columns\TextColumn::make('phone')
                    ->label('ເບີໂທ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('specialization')
                    ->label('ສາຂາວິຊາ')
                    ->searchable(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'active' => 'ກຳລັງເຮັດວຽກ',
                        'inactive' => 'ຢຸດເຮັດວຽກຊົ່ວຄາວ',
                        'resigned' => 'ລາອອກ',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'active' => 'ກຳລັງເຮັດວຽກ',
                        'inactive' => 'ຢຸດເຮັດວຽກຊົ່ວຄາວ',
                        'resigned' => 'ລາອອກ',
                    ]),

                Tables\Filters\SelectFilter::make('contract_type')
                    ->label('ປະເພດສັນຍາ')
                    ->options([
                        'full_time' => 'ເຕັມເວລາ',
                        'part_time' => 'ບາງເວລາ',
                        'temporary' => 'ຊົ່ວຄາວ',
                    ]),
            ])
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([10, 25, 50, 100])
            
            ->actions([
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()->label('ລຶບ'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}