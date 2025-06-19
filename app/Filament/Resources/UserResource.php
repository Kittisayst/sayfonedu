<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Teacher;
use App\Models\StudentParent;
use App\Models\Student;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'ຈັດການຜູ້ໃຊ້';

    protected static ?int $navigationSort = 7;


    public static function getNavigationLabel(): string
    {
        return "ຜູ້ໃຊ້ລະບົບ";
    }

    public static function getModelLabel(): string
    {
        return "ຜູ້ໃຊ້ລະບົບ";
    }

    public static function getPluralModelLabel(): string
    {
        return 'ຜູ້ໃຊ້ລະບົບທັງໝົດ';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('ຂໍ້ມູນໂປຣໄຟລ໌')
                    ->schema([
                        FileUpload::make('profile_image')
                            ->label('ຮູບໂປຣໄຟລ໌')
                            ->image()
                            ->disk('public')
                            ->directory('profile-images')
                            ->uploadButtonPosition('left')
                            ->uploadProgressIndicatorPosition('center')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200')
                            ->deleteUploadedFileUsing(function ($file) {
                                Storage::disk('public')->delete($file);
                            })
                    ])->columns(1),
                Section::make("ຂໍ້ມູນບັນຊິ")
                    ->schema([
                        TextInput::make('username')
                            ->label('ຊື່ຜູ້ໃຊ້')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->label('ອີເມວ')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('ເບີດ')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('ລະຫັດຜ່ານ')
                            ->password()
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create'),
                    ])->columns(2),
                Section::make('ຂໍ້ມູນສະຖານະ')
                    ->schema([
                        Select::make('role_id')
                            ->label('ບົດບາດ')
                            ->relationship('role', 'role_name')
                            ->preload()
                            ->searchable()
                            ->required(),

                        Select::make('status')
                            ->label('ສະຖານະຜູ້ໃຊ້')
                            ->options([
                                'active' => 'ໃຊ້ງານ',
                                'inactive' => 'ບໍ່ໃຊ້ງານ',
                                'suspended' => 'ຖືກລະງັບ',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
                Section::make('ການເຊື່ອມຕໍ່ກັບຜູ້ໃຊ້')
                    ->schema([
                        Select::make('user_type')
                            ->label('ປະເພດຜູ້ໃຊ້')
                            ->options([
                                'teacher' => 'ຄູສອນ',
                                'parent' => 'ຜູ້ປົກຄອງ',
                                'student' => 'ນັກຮຽນ',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('related_id', null);
                            })
                            ->required(),

                        Select::make('related_id')
                            ->label('ເລືອກຜູ້ໃຊ້')
                            ->options(function (Forms\Get $get) {
                                $type = $get('user_type');
                                if (!$type) return [];

                                return match($type) {
                                    'teacher' => Teacher::query()
                                        ->select('teacher_id', 'first_name_lao', 'last_name_lao')
                                        ->get()
                                        ->mapWithKeys(fn($teacher) => [
                                            $teacher->teacher_id => "{$teacher->first_name_lao} {$teacher->last_name_lao}"
                                        ]),
                                    'parent' => StudentParent::query()
                                        ->select('parent_id', 'first_name_lao', 'last_name_lao')
                                        ->get()
                                        ->mapWithKeys(fn($parent) => [
                                            $parent->parent_id => "{$parent->first_name_lao} {$parent->last_name_lao}"
                                        ]),
                                    'student' => Student::query()
                                        ->select('student_id', 'first_name_lao', 'last_name_lao')
                                        ->get()
                                        ->mapWithKeys(fn($student) => [
                                            $student->student_id => "{$student->first_name_lao} {$student->last_name_lao}"
                                        ]),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn(Forms\Get $get) => filled($get('user_type'))),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('ຮູບ')
                    ->circular()
                    ->disk('public')
                    ->visibility('public')
                    ->defaultImageUrl(asset('images/default-profile.png')),

                TextColumn::make('username')
                    ->label('ຊື່ຜູ້ໃຊ້')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('ອີເມລ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('ເບີໂທ')
                    ->searchable(),

                TextColumn::make('user_type')
                    ->label('ປະເພດຜູ້ໃຊ້')
                    ->default('ຍັງບໍ່ໄດ້ລະບຸ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'teacher' => 'ຄູສອນ',
                        'parent' => 'ຜູ້ປົກຄອງ',
                        'student' => 'ນັກຮຽນ',
                        default => 'ຍັງບໍ່ໄດ້ລະບຸ',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'teacher' => 'success',
                        'parent' => 'info',
                        'student' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('related_id')
                    ->label('ຂໍ້ມູນຜູ້ໃຊ້ທີ່ກ່ຽວຂ້ອງ')
                    ->default('ຍັງບໍ່ໄດ້ລະບຸ')
                    ->formatStateUsing(function ($record) {
                        $related = match($record->user_type) {
                            'teacher' => Teacher::find($record->related_id),
                            'parent' => StudentParent::find($record->related_id),
                            'student' => Student::find($record->related_id),
                            default => null,
                        };

                        if (!$related) return 'ບໍ່ພົບຂໍ້ມູນຜູ້ໃຊ້';

                        $type = match($record->user_type) {
                            'teacher' => 'ຄູສອນ',
                            'parent' => 'ຜູ້ປົກຄອງ',
                            'student' => 'ນັກຮຽນ',
                            default => '',
                        };

                        return "{$type}: {$related->first_name_lao} {$related->last_name_lao}";
                    }),

                TextColumn::make('role.role_name')
                    ->label('ບົດບາດ')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'ໃຊ້ງານ',
                        'inactive' => 'ບໍ່ໃຊ້ງານ',
                        'suspended' => 'ຖືກລະງັບ',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('last_login')
                    ->label('ເຂົ້າລະບົບລ່າສຸດ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('ອັບເດດລ່າສຸດ')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginationPageOptions([10, 25, 50, 100, 'ທັງໝົດ'])
            ->filters([
                SelectFilter::make('role_id')
                    ->label('ບົດບາດ')
                    ->relationship('role', 'role_name'),

                SelectFilter::make('user_type')
                    ->label('ປະເພດຜູ້ໃຊ້')
                    ->options([
                        'teacher' => 'ຄູສອນ',
                        'parent' => 'ຜູ້ປົກຄອງ',
                        'student' => 'ນັກຮຽນ',
                    ]),

                SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'active' => 'ໃຊ້ງານ',
                        'inactive' => 'ບໍ່ໃຊ້ງານ',
                        'suspended' => 'ຖືກລະງັບ',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()->label('ລຶບ'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
