<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EthnicityResource\Pages;
use App\Filament\Resources\EthnicityResource\RelationManagers;
use App\Models\Ethnicity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EthnicityResource extends Resource
{
    protected static ?string $model = Ethnicity::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນພື້ນຖານ';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'ຊົນເຜົ່າ';

    protected static ?string $pluralModelLabel = 'ຊົນເຜົ່າທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ethnicity_name_lao')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ຊົນເຜົ່າ (ລາວ)'),
                Forms\Components\TextInput::make('ethnicity_name_en')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ຊື່ຊົນເຜົ່າ (ອັງກິດ)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ethnicity_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ຊົນເຜົ່າ (ລາວ)'),
                Tables\Columns\TextColumn::make('ethnicity_name_en')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ຊົນເຜົ່າ (ອັງກິດ)'),
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
            'index' => Pages\ListEthnicities::route('/'),
            'create' => Pages\CreateEthnicity::route('/create'),
            'edit' => Pages\EditEthnicity::route('/{record}/edit'),
        ];
    }
}
