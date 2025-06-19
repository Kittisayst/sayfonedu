<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserActivityResource\Pages;
use App\Filament\Resources\UserActivityResource\RelationManagers;
use App\Models\UserActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserActivityResource extends Resource
{
    protected static ?string $model = UserActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຜູ້ໃຊ້';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'ຕິດຕາມຜູ້ໃຊ້ງານ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'username')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຜູ້ໃຊ້'),
                Forms\Components\Select::make('activity_type')
                    ->required()
                    ->options([
                        'login' => 'ເຂົ້າສູ່ລະບົບ',
                        'logout' => 'ອອກຈາກລະບົບ',
                        'create' => 'ສ້າງຂໍ້ມູນ',
                        'update' => 'ແກ້ໄຂຂໍ້ມູນ',
                        'delete' => 'ລຶບຂໍ້ມູນ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->label('ປະເພດກິດຈະກຳ'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ຄຳອະທິບາຍ'),
                Forms\Components\TextInput::make('ip_address')
                    ->required()
                    ->maxLength(45)
                    ->label('ທີ່ຢູ່ IP'),
                Forms\Components\TextInput::make('user_agent')
                    ->required()
                    ->maxLength(255)
                    ->label('User Agent'),
                Forms\Components\DateTimePicker::make('activity_time')
                    ->required()
                    ->label('ເວລາກິດຈະກຳ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->searchable()
                    ->sortable()
                    ->label('ຜູ້ໃຊ້'),
                Tables\Columns\TextColumn::make('activity_type')
                    ->searchable()
                    ->sortable()
                    ->label('ປະເພດກິດຈະກຳ'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->label('ຄຳອະທິບາຍ'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable()
                    ->label('ທີ່ຢູ່ IP'),
                Tables\Columns\TextColumn::make('activity_time')
                    ->dateTime()
                    ->sortable()
                    ->label('ເວລາກິດຈະກຳ'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ວັນທີສ້າງ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('activity_type')
                    ->options([
                        'login' => 'ເຂົ້າສູ່ລະບົບ',
                        'logout' => 'ອອກຈາກລະບົບ',
                        'create' => 'ສ້າງຂໍ້ມູນ',
                        'update' => 'ແກ້ໄຂຂໍ້ມູນ',
                        'delete' => 'ລຶບຂໍ້ມູນ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->label('ປະເພດກິດຈະກຳ'),
                Tables\Filters\Filter::make('activity_time')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('ຈາກວັນທີ'),
                        Forms\Components\DatePicker::make('until')
                            ->label('ຫາວັນທີ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('activity_time', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('activity_time', '<=', $date),
                            );
                    })
                    ->label('ເວລາກິດຈະກຳ'),
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
            'index' => Pages\ListUserActivities::route('/'),
            // 'create' => Pages\CreateUserActivity::route('/create'),
            // 'edit' => Pages\EditUserActivity::route('/{record}/edit'),
        ];
    }
}
