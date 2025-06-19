<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageResource\Pages;
use App\Filament\Resources\VillageResource\RelationManagers;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillageResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນພື້ນຖານ';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'ບ້ານ';
    

    protected static ?string $pluralModelLabel = 'ບ້ານທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('district_id')
                    ->relationship('district', 'district_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ເມືອງ'),
                Forms\Components\TextInput::make('village_name_lao')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ບ້ານ (ລາວ)'),
                Forms\Components\TextInput::make('village_name_en')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ບ້ານ (ອັງກິດ)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('district.district_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ເມືອງ'),
                Tables\Columns\TextColumn::make('district.province.province_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ແຂວງ'),
                Tables\Columns\TextColumn::make('village_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ບ້ານ (ລາວ)'),
                Tables\Columns\TextColumn::make('village_name_en')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ບ້ານ (ອັງກິດ)'),
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
                Tables\Filters\SelectFilter::make('district')
                    ->relationship('district', 'district_name_lao')
                    ->searchable()
                    ->preload()
                    ->label('ເມືອງ'),
                Tables\Filters\SelectFilter::make('province')
                    ->relationship('district.province', 'province_name_lao')
                    ->searchable()
                    ->preload()
                    ->label('ແຂວງ'),
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
            'index' => Pages\ListVillages::route('/'),
            'create' => Pages\CreateVillage::route('/create'),
            'edit' => Pages\EditVillage::route('/{record}/edit'),
        ];
    }
}
