<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນພື້ນຖານ';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'ແຂວງ';

    protected static ?string $pluralModelLabel = 'ແຂວງທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('province_name_lao')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ແຂວງ (ລາວ)'),
                Forms\Components\TextInput::make('province_name_en')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ແຂວງ (ອັງກິດ)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ແຂວງ (ລາວ)'),
                Tables\Columns\TextColumn::make('province_name_en')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ແຂວງ (ອັງກິດ)'),
                Tables\Columns\TextColumn::make('districts_count')
                    ->counts('districts')
                    ->sortable()
                    ->label('ຈຳນວນເມືອງ'),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->sortable()
                    ->label('ຈຳນວນນັກຮຽນ'),
                Tables\Columns\TextColumn::make('teachers_count')
                    ->counts('teachers')
                    ->sortable()
                    ->label('ຈຳນວນຄູສອນ'),
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
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
}
