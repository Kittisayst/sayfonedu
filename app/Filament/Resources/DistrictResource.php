<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນພື້ນຖານ';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'ເມືອງ';

    protected static ?string $pluralModelLabel = 'ເມືອງທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'province_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ແຂວງ'),
                Forms\Components\TextInput::make('district_name_lao')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ເມືອງ (ລາວ)'),
                Forms\Components\TextInput::make('district_name_en')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ເມືອງ (ອັງກິດ)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province.province_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ແຂວງ'),
                Tables\Columns\TextColumn::make('district_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ເມືອງ (ລາວ)'),
                Tables\Columns\TextColumn::make('district_name_en')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ເມືອງ (ອັງກິດ)'),
                Tables\Columns\TextColumn::make('villages_count')
                    ->counts('villages')
                    ->sortable()
                    ->label('ຈຳນວນບ້ານ'),
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
                Tables\Filters\SelectFilter::make('province')
                    ->relationship('province', 'province_name_lao')
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
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
