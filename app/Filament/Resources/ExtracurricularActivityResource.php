<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtracurricularActivityResource\Pages;
use App\Filament\Resources\ExtracurricularActivityResource\RelationManagers;
use App\Models\ExtracurricularActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtracurricularActivityResource extends Resource
{
    protected static ?string $model = ExtracurricularActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການການຮຽນການສອນ';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'ກິດຈະກຳນັກຮຽນ';

    protected static ?string $pluralModelLabel = 'ກິດຈະກຳນັກຮຽນທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('activity_name')
                    ->required()
                    ->maxLength(255)
                    ->label('ຊື່ກິດຈະກຳ'),
                Forms\Components\Select::make('activity_type')
                    ->options([
                        'sport' => 'ກິລາ',
                        'music' => 'ດົນຕຣີ',
                        'art' => 'ສິລະປະ',
                        'academic' => 'ວິຊາການ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required()
                    ->label('ປະເພດກິດຈະກຳ'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ລາຍລະອຽດ'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('ວັນທີເລີ່ມ'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('ວັນທີສິ້ນສຸດ'),
                Forms\Components\TextInput::make('schedule')
                    ->maxLength(255)
                    ->label('ຕາຕະລາງການ'),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255)
                    ->label('ສະຖານທີ່'),
                Forms\Components\TextInput::make('max_participants')
                    ->numeric()
                    ->minValue(0)
                    ->label('ຈຳນວນຜູ້ເຂົ້າຮ່ວມສູງສຸດ'),
                Forms\Components\Select::make('coordinator_id')
                    ->relationship('coordinator', 'username')
                    ->searchable()
                    ->preload()
                    ->label('ຜູ້ປະສານງານ'),
                Forms\Components\Select::make('academic_year_id')
                    ->relationship('academicYear', 'year_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ປີການສຶກສາ'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->label('ເປີດໃຊ້ງານ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('activity_name')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ກິດຈະກຳ'),
                Tables\Columns\TextColumn::make('activity_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sports' => 'success',
                        'music' => 'info', 
                        'art' => 'warning',
                        'academic' => 'primary',
                        default => 'gray'
                    })
                    ->label('ປະເພດກິດຈະກຳ'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີເລີ່ມ'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີສິ້ນສຸດ'),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->label('ສະຖານທີ່'),
                Tables\Columns\TextColumn::make('max_participants')
                    ->numeric()
                    ->sortable()
                    ->label('ຈຳນວນຜູ້ເຂົ້າຮ່ວມສູງສຸດ'),
                Tables\Columns\TextColumn::make('coordinator.username')
                    ->searchable()
                    ->sortable()
                    ->label('ຜູ້ປະສານງານ'),
                Tables\Columns\TextColumn::make('academicYear.year_name')
                    ->searchable()
                    ->sortable()
                    ->label('ປີການສຶກສາ'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('ເປີດໃຊ້ງານ'),
            ])
            ->filters([
              
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
            'index' => Pages\ListExtracurricularActivities::route('/'),
            'create' => Pages\CreateExtracurricularActivity::route('/create'),
            'edit' => Pages\EditExtracurricularActivity::route('/{record}/edit'),
        ];
    }
}
