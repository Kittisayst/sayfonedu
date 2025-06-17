<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'ຈັດການການເງິນ';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'ສ່ວນຫຼຸດ';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('discount_name')
                    ->required()
                    ->maxLength(255)
                    ->label('ຊື່ສ່ວນຫຼຸດ'),
                Forms\Components\Select::make('discount_type')
                    ->required()
                    ->options([
                        'percentage' => 'ເປີເຊັນ',
                        'fixed' => 'ຈຳນວນເງິນຄົງທີ່'
                    ])
                    ->label('ປະເພດສ່ວນຫຼຸດ'),
                Forms\Components\TextInput::make('discount_value')
                    ->required()
                    ->numeric()
                    ->label('ຄ່າສ່ວນຫຼຸດ'),
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
                Tables\Columns\TextColumn::make('discount_name')
                    ->searchable()
                    ->label('ຊື່ສ່ວນຫຼຸດ'),
                Tables\Columns\TextColumn::make('discount_type')
                    ->searchable()
                    ->label('ປະເພດສ່ວນຫຼຸດ'),
                Tables\Columns\TextColumn::make('discount_value')
                    ->numeric()
                    ->sortable()
                    ->label('ຄ່າສ່ວນຫຼຸດ'),
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
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}
