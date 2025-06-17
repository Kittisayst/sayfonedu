<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassSubjectResource\Pages;
use App\Filament\Resources\ClassSubjectResource\RelationManagers;
use App\Models\ClassSubject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassSubjectResource extends Resource
{
    protected static ?string $model = ClassSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການການຮຽນການສອນ';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'ການມອບໝາຍວິຊາ';

    protected static ?string $pluralModelLabel = 'ການມອບໝາຍວິຊາທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('class_id')
                    ->relationship('schoolClass', 'class_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຫ້ອງຮຽນ'),
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'subject_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ວິຊາ'),
                Forms\Components\Select::make('teacher_id')
                    ->relationship('teacher', 'first_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຄູສອນ'),
                Forms\Components\TextInput::make('hours_per_week')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(40)
                    ->label('ຊົ່ວໂມງຕໍ່ອາທິດ'),
                Forms\Components\Select::make('day_of_week')
                    ->required()
                    ->options([
                        'monday' => 'ຈັນ',
                        'tuesday' => 'ອັງຄານ',
                        'wednesday' => 'ພຸດ',
                        'thursday' => 'ພະຫັດ',
                        'friday' => 'ສຸກ',
                        'saturday' => 'ເສົາ',
                        'sunday' => 'ອາທິດ'
                    ])
                    ->label('ວັນທີ່ສອນ'),
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->seconds(false)
                    ->label('ເວລາເລີ່ມຕົ້ນ'),
                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->seconds(false)
                    ->label('ເວລາສິ້ນສຸດ'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'ເປີດໃຊ້ງານ',
                        'inactive' => 'ປິດໃຊ້ງານ',
                        'pending' => 'ລໍຖ້າ'
                    ])
                    ->label('ສະຖານະ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schoolClass.class_name')
                    ->searchable()
                    ->sortable()
                    ->label('ຫ້ອງຮຽນ'),
                Tables\Columns\TextColumn::make('subject.subject_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ວິຊາ'),
                Tables\Columns\TextColumn::make('teacher.first_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຄູສອນ'),
                Tables\Columns\TextColumn::make('hours_per_week')
                    ->numeric()
                    ->sortable()
                    ->label('ຊົ່ວໂມງຕໍ່ອາທິດ'),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->searchable()
                    ->sortable()
                    ->label('ວັນທີ່ສອນ'),
                Tables\Columns\TextColumn::make('start_time')
                    ->time()
                    ->sortable()
                    ->label('ເວລາເລີ່ມຕົ້ນ'),
                Tables\Columns\TextColumn::make('end_time')
                    ->time()
                    ->sortable()
                    ->label('ເວລາສິ້ນສຸດ'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->label('ສະຖານະ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'ເປີດໃຊ້ງານ',
                        'inactive' => 'ປິດໃຊ້ງານ',
                        'pending' => 'ລໍຖ້າ'
                    ])
                    ->label('ສະຖານະ'),
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->options([
                        'monday' => 'ຈັນ',
                        'tuesday' => 'ອັງຄານ',
                        'wednesday' => 'ພຸດ',
                        'thursday' => 'ພະຫັດ',
                        'friday' => 'ສຸກ',
                        'saturday' => 'ເສົາ',
                        'sunday' => 'ອາທິດ'
                    ])
                    ->label('ວັນທີ່ສອນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListClassSubjects::route('/'),
            'create' => Pages\CreateClassSubject::route('/create'),
            'edit' => Pages\EditClassSubject::route('/{record}/edit'),
        ];
    }
}
