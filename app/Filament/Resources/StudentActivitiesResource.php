<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentActivitiesResource\Pages;
use App\Models\StudentActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentActivitiesResource extends Resource
{
    protected static ?string $model = StudentActivity::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນນັກຮຽນ';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'ເຂົ້າຮ່ວມກິດຈະກຳ';

    protected static ?string $pluralModelLabel = 'ກິດຈະກຳທັງໝົດ';

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
                Forms\Components\Select::make('activity_id')
                    ->relationship('activity', 'activity_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ກິດຈະກຳ'),
                Forms\Components\DatePicker::make('join_date')
                    ->required()
                    ->label('ວັນທີເຂົ້າຮ່ວມ'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'ເຂົ້າຮ່ວມ',
                        'inactive' => 'ບໍ່ເຂົ້າຮ່ວມ',
                        'completed' => 'ສຳເລັດ',
                    ])
                    ->required()
                    ->label('ສະຖານະ'),
                Forms\Components\TextInput::make('performance')
                    ->maxLength(255)
                    ->label('ຜົນການປະຕິບັດ'),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ໝາຍເຫດ'),
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
                Tables\Columns\TextColumn::make('activity.activity_name')
                    ->searchable()
                    ->sortable()
                    ->label('ກິດຈະກຳ'),
                Tables\Columns\TextColumn::make('join_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີເຂົ້າຮ່ວມ'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'completed' => 'info',
                    })
                    ->label('ສະຖານະ'),
                Tables\Columns\TextColumn::make('performance')
                    ->searchable()
                    ->label('ຜົນການປະຕິບັດ'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('ວັນທີສ້າງ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'ເຂົ້າຮ່ວມ',
                        'inactive' => 'ບໍ່ເຂົ້າຮ່ວມ',
                        'completed' => 'ສຳເລັດ',
                    ])
                    ->label('ສະຖານະ'),
                Tables\Filters\SelectFilter::make('activity')
                    ->relationship('activity', 'activity_name')
                    ->label('ກິດຈະກຳ'),
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
            'index' => Pages\ListStudentActivities::route('/'),
            'create' => Pages\CreateStudentActivities::route('/create'),
            'edit' => Pages\EditStudentActivities::route('/{record}/edit'),
        ];
    }
}
