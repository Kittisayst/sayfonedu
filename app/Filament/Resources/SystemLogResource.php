<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemLogResource\Pages;
use App\Filament\Resources\SystemLogResource\RelationManagers;
use App\Models\SystemLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'ຕັ້ງຄ່າ ແລະ ບຳລຸງຮັກສາ';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'ບັນທຶກລະບົບ';

    protected static ?string $pluralModelLabel = 'ບັນທຶກລະບົບທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('log_level')
                    ->required()
                    ->options([
                        'emergency' => 'ສຸກເສີນ',
                        'alert' => 'ແຈ້ງເຕືອນ',
                        'critical' => 'ຮີບດ່ວນ',
                        'error' => 'ຜິດພາດ',
                        'warning' => 'ເຕືອນ',
                        'notice' => 'ແຈ້ງການ',
                        'info' => 'ຂໍ້ມູນ',
                        'debug' => 'ດີບັກ',
                    ])
                    ->label('ລະດັບການບັນທຶກ'),
                Forms\Components\TextInput::make('log_source')
                    ->required()
                    ->maxLength(255)
                    ->label('ແຫຼ່ງທີ່ມາຂອງການບັນທຶກ'),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ຂໍ້ຄວາມ'),
                Forms\Components\Textarea::make('context')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ຂໍ້ມູນເພີ່ມເຕີມ'),
                Forms\Components\TextInput::make('ip_address')
                    ->maxLength(45)
                    ->label('ທີ່ຢູ່ IP'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('ຜູ້ໃຊ້'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_level')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'emergency' => 'danger',
                        'alert' => 'danger',
                        'critical' => 'danger',
                        'error' => 'danger',
                        'warning' => 'warning',
                        'notice' => 'info',
                        'info' => 'info',
                        'debug' => 'gray',
                        default => 'gray',
                    })
                    ->label('ລະດັບການບັນທຶກ'),
                Tables\Columns\TextColumn::make('log_source')
                    ->searchable()
                    ->sortable()
                    ->label('ແຫຼ່ງທີ່ມາຂອງການບັນທຶກ'),
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->limit(50)
                    ->label('ຂໍ້ຄວາມ'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable()
                    ->sortable()
                    ->label('ທີ່ຢູ່ IP'),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('ຜູ້ໃຊ້'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('ວັນທີບັນທຶກ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_level')
                    ->options([
                        'emergency' => 'ສຸກເສີນ',
                        'alert' => 'ແຈ້ງເຕືອນ',
                        'critical' => 'ຮີບດ່ວນ',
                        'error' => 'ຜິດພາດ',
                        'warning' => 'ເຕືອນ',
                        'notice' => 'ແຈ້ງການ',
                        'info' => 'ຂໍ້ມູນ',
                        'debug' => 'ດີບັກ',
                    ])
                    ->label('ລະດັບການບັນທຶກ'),
                Tables\Filters\SelectFilter::make('log_source')
                    ->options([
                        'auth' => 'ການຢືນຢັນ',
                        'database' => 'ຖານຂໍ້ມູນ',
                        'file' => 'ໄຟລ໌',
                        'mail' => 'ອີເມວ',
                        'payment' => 'ການຊຳລະເງິນ',
                        'system' => 'ລະບົບ',
                    ])
                    ->label('ແຫຼ່ງທີ່ມາຂອງການບັນທຶກ'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSystemLogs::route('/'),
            'create' => Pages\CreateSystemLog::route('/create'),
            'edit' => Pages\EditSystemLog::route('/{record}/edit'),
        ];
    }
}
