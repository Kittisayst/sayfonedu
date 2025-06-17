<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການການຮຽນການສອນ';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'ວິຊາຮຽນ';

    protected static ?string $pluralModelLabel = 'ວິຊາທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject_code')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->label('ລະຫັດວິຊາ'),
                Forms\Components\TextInput::make('subject_name_lao')
                    ->required()
                    ->maxLength(255)
                    ->label('ຊື່ວິຊາ (ລາວ)'),
                Forms\Components\TextInput::make('subject_name_en')
                    ->required()
                    ->maxLength(255)
                    ->label('ຊື່ວິຊາ (ອັງກິດ)'),
                Forms\Components\TextInput::make('credit_hours')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label('ຈຳນວນໜ່ວຍກິດ'),
                Forms\Components\Select::make('category')
                    ->required()
                    ->options([
                        'core' => 'ວິຊາພື້ນຖານ',
                        'elective' => 'ວິຊາເລືອກ',
                        'optional' => 'ວິຊາທົດແທນ'
                    ])
                    ->label('ໝວດໝູ່'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ຄຳອະທິບາຍ'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->label('ສະຖານະເປີດໃຊ້ງານ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject_code')
                    ->searchable()
                    ->sortable()
                    ->label('ລະຫັດວິຊາ'),
                Tables\Columns\TextColumn::make('subject_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ວິຊາ (ລາວ)'),
                Tables\Columns\TextColumn::make('subject_name_en')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ວິຊາ (ອັງກິດ)'),
                Tables\Columns\TextColumn::make('credit_hours')
                    ->numeric()
                    ->sortable()
                    ->label('ຈຳນວນໜ່ວຍກິດ'),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable()
                    ->label('ໝວດໝູ່'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('ສະຖານະ'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ວັນທີສ້າງ'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ວັນທີອັບເດດ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'core' => 'ວິຊາພື້ນຖານ',
                        'elective' => 'ວິຊາເລືອກ',
                        'optional' => 'ວິຊາທົດແທນ'
                    ])
                    ->label('ໝວດໝູ່'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('ສະຖານະເປີດໃຊ້ງານ'),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
