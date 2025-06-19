<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers\RolesRelationManager;
use App\Models\Permission;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'ຈັດການຜູ້ໃຊ້';

    protected static ?string $navigationLabel = 'ສິດທິການໃຊ້ງານ';

    protected static ?string $modelLabel = 'ສິດທິການໃຊ້ງານ';
    
    protected static ?string $pluralModelLabel = 'ສິດທິການໃຊ້ງານ';

    protected static ?int $navigationSort = 7;
    
    protected static ?string $recordTitleAttribute = 'permission_name';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('permission_name')
                    ->label('ຊື່ສິດທິ')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                    
                Textarea::make('description')
                    ->label('ຄຳອະທິບາຍ')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('permission_id')
                    ->label('ລະຫັດ')
                    ->sortable(),
                    
                TextColumn::make('permission_name')
                    ->label('ຊື່ສິດທິ')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('ຄຳອະທິບາຍ')
                    ->limit(50)
                    ->searchable(),
                    
                TextColumn::make('roles_count')
                    ->label('ຈຳນວນບົດບາດ')
                    ->counts('roles'),
                    
                TextColumn::make('created_at')
                    ->label('ວັນທີສ້າງ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('ວັນທີອັບເດດ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->label('ແກ້ໄຂ'),
                \Filament\Tables\Actions\DeleteAction::make()->label('ລຶບອອກ'),
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
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
