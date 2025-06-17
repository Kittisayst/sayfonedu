<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentBehaviorResource\Pages;
use App\Filament\Resources\StudentBehaviorResource\RelationManagers;
use App\Models\StudentBehaviorRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentBehaviorResource extends Resource
{
    protected static ?string $model = StudentBehaviorRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນນັກຮຽນ';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'ພຶດຕິກຳນັກຮຽນ';

    protected static ?string $pluralModelLabel = 'ບັນທຶກພຶດຕິກຳທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'first_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ນັກຮຽນ'),
                Forms\Components\Select::make('record_type')
                    ->options([
                        'positive' => 'ດີ',
                        'negative' => 'ບໍ່ດີ',
                        'neutral' => 'ປົກກະຕິ',
                    ])
                    ->required()
                    ->label('ປະເພດການບັນທຶກ'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ລາຍລະອຽດ'),
                Forms\Components\Select::make('teacher_id')
                    ->relationship('teacher', 'first_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຄູ'),
                Forms\Components\DatePicker::make('record_date')
                    ->required()
                    ->label('ວັນທີບັນທຶກ'),
                Forms\Components\Textarea::make('action_taken')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ການດຳເນີນການ'),
                Forms\Components\Textarea::make('follow_up')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ການຕິດຕາມ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.first_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ນັກຮຽນ'),
                Tables\Columns\TextColumn::make('record_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'positive' => 'success',
                        'negative' => 'danger',
                        'neutral' => 'gray',
                    })
                    ->label('ປະເພດການບັນທຶກ'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('ລາຍລະອຽດ'),
                Tables\Columns\TextColumn::make('teacher.first_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຄູ'),
                Tables\Columns\TextColumn::make('record_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີບັນທຶກ'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('ວັນທີສ້າງ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('record_type')
                    ->options([
                        'positive' => 'ດີ',
                        'negative' => 'ບໍ່ດີ',
                        'neutral' => 'ປົກກະຕິ',
                    ])
                    ->label('ປະເພດການບັນທຶກ'),
                Tables\Filters\SelectFilter::make('teacher')
                    ->relationship('teacher', 'first_name_lao')
                    ->label('ຄູ'),
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
            'index' => Pages\ListStudentBehaviors::route('/'),
            'create' => Pages\CreateStudentBehavior::route('/create'),
            'edit' => Pages\EditStudentBehavior::route('/{record}/edit'),
        ];
    }
}
