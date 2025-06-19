<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentAchievementResource\Pages;
use App\Filament\Resources\StudentAchievementResource\RelationManagers;
use App\Models\StudentAchievement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentAchievementResource extends Resource
{
    protected static ?string $model = StudentAchievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນນັກຮຽນ';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'ຜົນງານນັກຮຽນ';

    protected static ?string $pluralModelLabel = 'ຜົນງານນັກຮຽນທັງໝົດ';

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
                Forms\Components\Select::make('achievement_type')
                    ->options([
                        'academic' => 'ວິຊາການ',
                        'sports' => 'ກິລາ',
                        'arts' => 'ສິລະປະ',
                        'leadership' => 'ຄວາມເປັນຜູ້ນຳ',
                        'community' => 'ສັງຄົມ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required()
                    ->label('ປະເພດຜົນງານ'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('ຫົວຂໍ້'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ລາຍລະອຽດ'),
                Forms\Components\DatePicker::make('award_date')
                    ->required()
                    ->label('ວັນທີຮັບລາງວັນ'),
                Forms\Components\TextInput::make('issuer')
                    ->required()
                    ->maxLength(255)
                    ->label('ຜູ້ມອບລາງວັນ'),
                Forms\Components\FileUpload::make('certificate_path')
                    ->directory('student-achievements')
                    ->maxSize(10240)
                    ->label('ໃບຢັ້ງຢືນ'),
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
                Tables\Columns\TextColumn::make('achievement_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'academic' => 'success',
                        'sports' => 'warning',
                        'arts' => 'info',
                        'leadership' => 'primary',
                        'community' => 'success',
                        default => 'gray',
                    })
                    ->label('ປະເພດຜົນງານ'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('ຫົວຂໍ້'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('ລາຍລະອຽດ'),
                Tables\Columns\TextColumn::make('award_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີຮັບລາງວັນ'),
                Tables\Columns\TextColumn::make('issuer')
                    ->searchable()
                    ->label('ຜູ້ມອບລາງວັນ'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('ວັນທີສ້າງ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('achievement_type')
                    ->options([
                        'academic' => 'ວິຊາການ',
                        'sports' => 'ກິລາ',
                        'arts' => 'ສິລະປະ',
                        'leadership' => 'ຄວາມເປັນຜູ້ນຳ',
                        'community' => 'ສັງຄົມ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->label('ປະເພດຜົນງານ'),
                Tables\Filters\SelectFilter::make('student')
                    ->relationship('student', 'first_name_lao')
                    ->label('ນັກຮຽນ'),
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
            'index' => Pages\ListStudentAchievements::route('/'),
            'create' => Pages\CreateStudentAchievement::route('/create'),
            'edit' => Pages\EditStudentAchievement::route('/{record}/edit'),
        ];
    }
}
