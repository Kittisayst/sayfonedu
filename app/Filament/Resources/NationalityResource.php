<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NationalityResource\Pages;
use App\Filament\Resources\NationalityResource\RelationManagers;
use App\Models\Nationality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NationalityResource extends Resource
{
    protected static ?string $model = Nationality::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນພື້ນຖານ';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'ສັນຊາດ';

    protected static ?string $pluralModelLabel = 'ສັນຊາດທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nationality_name_lao')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ສັນຊາດ (ລາວ)'),
                Forms\Components\TextInput::make('nationality_name_en')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ສັນຊາດ (ອັງກິດ)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nationality_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ສັນຊາດ (ລາວ)'),
                Tables\Columns\TextColumn::make('nationality_name_en')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ສັນຊາດ (ອັງກິດ)'),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->sortable()
                    ->label('ຈຳນວນນັກຮຽນ'),
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
                //
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
            'index' => Pages\ListNationalities::route('/'),
            'create' => Pages\CreateNationality::route('/create'),
            'edit' => Pages\EditNationality::route('/{record}/edit'),
        ];
    }
}
