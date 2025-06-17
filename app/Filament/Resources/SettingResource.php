<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'ຕັ້ງຄ່າ ແລະ ບຳລຸງຮັກສາ';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'ຕັ້ງຄ່າລະບົບ';

    protected static ?string $pluralModelLabel = 'ຕັ້ງຄ່າລະບົບທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('setting_key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('ລະຫັດການຕັ້ງຄ່າ'),
                Forms\Components\TextInput::make('setting_value')
                    ->required()
                    ->maxLength(255)
                    ->label('ຄ່າການຕັ້ງຄ່າ'),
                Forms\Components\TextInput::make('setting_group')
                    ->required()
                    ->maxLength(255)
                    ->label('ກຸ່ມການຕັ້ງຄ່າ'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ຄຳອະທິບາຍ'),
                Forms\Components\Toggle::make('is_system')
                    ->required()
                    ->label('ເປັນການຕັ້ງຄ່າລະບົບ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('setting_key')
                    ->searchable()
                    ->sortable()
                    ->label('ລະຫັດການຕັ້ງຄ່າ'),
                Tables\Columns\TextColumn::make('setting_value')
                    ->searchable()
                    ->sortable()
                    ->label('ຄ່າການຕັ້ງຄ່າ'),
                Tables\Columns\TextColumn::make('setting_group')
                    ->searchable()
                    ->sortable()
                    ->label('ກຸ່ມການຕັ້ງຄ່າ'),
                Tables\Columns\IconColumn::make('is_system')
                    ->boolean()
                    ->sortable()
                    ->label('ເປັນການຕັ້ງຄ່າລະບົບ'),
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
                Tables\Filters\SelectFilter::make('setting_group')
                    ->options([
                        'general' => 'ທົ່ວໄປ',
                        'email' => 'ອີເມວ',
                        'payment' => 'ການຊຳລະເງິນ',
                        'notification' => 'ການແຈ້ງເຕືອນ',
                    ])
                    ->label('ກຸ່ມການຕັ້ງຄ່າ'),
                Tables\Filters\TernaryFilter::make('is_system')
                    ->label('ເປັນການຕັ້ງຄ່າລະບົບ'),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
