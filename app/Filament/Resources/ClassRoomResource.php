<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Filament\Resources\ClassRoomResource\RelationManagers;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'ຈັດການຊັ້ນຮຽນ ແລະ ຫ້ອງຮຽນ';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'ຫ້ອງຮຽນ';
    }

    public static function getModelLabel(): string
    {
        return 'ຫ້ອງຮຽນ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'ຫ້ອງຮຽນທັງໝົດ';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ຂໍ້ມູນຫ້ອງຮຽນ')
                    ->schema([
                        Forms\Components\TextInput::make('class_name')
                            ->label('ຊື່ຫ້ອງຮຽນ')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('level_id')
                            ->label('ລະດັບສຶກສາ')
                            ->relationship('schoolLevel', 'level_name_lao')
                            ->searchable(['level_name_lao', 'level_name_en'])
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('ສົກຮຽນ')
                            ->relationship('academicYear', 'year_name')
                            ->required(),

                        Forms\Components\Select::make('homeroom_teacher_id')
                            ->label('ຄູປະຈຳຫ້ອງ')
                            ->relationship('homeroomTeacher', 'first_name_lao')
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->first_name_lao} {$record->last_name_lao}")
                            ->searchable(['first_name', 'last_name'])
                            ->preload(),

                        Forms\Components\TextInput::make('room_number')
                            ->label('ເລກຫ້ອງ')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('capacity')
                            ->label('ຈຳນວນນັກຮຽນສູງສຸດ')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50),

                        Forms\Components\Textarea::make('description')
                            ->label('ລາຍລະອຽດ')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('ສະຖານະ')
                            ->options([
                                'active' => 'ໃຊ້ງານ',
                                'inactive' => 'ບໍ່ໃຊ້ງານ',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('class_name')
                    ->label('ຊື່ຫ້ອງຮຽນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('schoolLevel.level_name_lao')
                    ->label('ລະດັບສຶກສາ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.year_name')
                    ->label('ສົກຮຽນ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('homeroomTeacher')
                    ->label('ຄູປະຈຳຫ້ອງ')
                    ->formatStateUsing(fn($state, $record) => $record->homeroomTeacher ? "{$record->homeroomTeacher->first_name_lao} {$record->homeroomTeacher->last_name_lao}" : 'ບໍ່ສະແດງ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('ເລກຫ້ອງ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('ຈຳນວນນັກຮຽນ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'ໃຊ້ງານ',
                        'inactive' => 'ບໍ່ໃຊ້ງານ',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂລ່າສຸດ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_level')
                    ->label('ຊັ້ນຮຽນ')
                    ->options([
                        'k' => 'ອານຸບານກຽມ',
                        'k1' => 'ອານຸບານ 1',
                        'k2' => 'ອານຸບານ 2',
                        'k3' => 'ອານຸບານ 3',
                        '1' => 'ຊັ້ນປະຖົມ 1',
                        '2' => 'ຊັ້ນປະຖົມ 2',
                        '3' => 'ຊັ້ນປະຖົມ 3',
                        '4' => 'ຊັ້ນປະຖົມ 4',
                        '5' => 'ຊັ້ນປະຖົມ 5',
                        'm1' => 'ຊັ້ນມັດທະຍົມ 1',
                        'm2' => 'ຊັ້ນມັດທະຍົມ 2',
                        'm3' => 'ຊັ້ນມັດທະຍົມ 3',
                        'm4' => 'ຊັ້ນມັດທະຍົມ 4',
                        'm5' => 'ຊັ້ນມັດທະຍົມ 5',
                        'm6' => 'ຊັ້ນມັດທະຍົມ 6',
                        'm7' => 'ຊັ້ນມັດທະຍົມ 7',
                    ]),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('ສົກຮຽນ')
                    ->relationship('academicYear', 'year_name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'active' => 'ໃຊ້ງານ',
                        'inactive' => 'ບໍ່ໃຊ້ງານ',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລຶບ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('ລຶບ'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}
