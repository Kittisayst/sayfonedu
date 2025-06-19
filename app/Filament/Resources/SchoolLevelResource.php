<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolLevelResource\Pages;
use App\Filament\Resources\SchoolLevelResource\RelationManagers;
use App\Models\SchoolLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolLevelResource extends Resource
{
    protected static ?string $model = SchoolLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນພື້ນຖານ';

    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return 'ລະດັບການສຶກສາ';
    }

    public static function getModelLabel(): string
    {
        return 'ລະດັບການສຶກສາ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'ລະດັບການສຶກສາທັງໝົດ';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('level_name_lao')
                    ->label('ຊື່ລະດັບການສຶກສາ (ພາສາລາວ)')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('level_name_en')
                    ->label('ຊື່ລະດັບການສຶກສາ (ພາສາອັງກິດ)')
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('sort_order')
                    ->label('ລຳດັບສຳລັບການສະແດງຜົນ')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('ສະຖານະການໃຊ້ງານ')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check')
                    ->offIcon('heroicon-o-x-mark'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level_id')
                    ->label('ລະຫັດ')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('level_name_lao')
                    ->label('ຊື່ ພາສາລາວ')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('level_name_en')
                    ->label('ຊື່ ພາສາອັງກິດ')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ລຳດັບ')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('ສະຖານະ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultPaginationPageOption(25)
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('ສະຖານະການໃຊ້ງານ')
                    ->options([
                        '1' => 'ໃຊ້ງານ',
                        '0' => 'ບໍ່ໃຊ້ງານ',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSchoolLevels::route('/'),
            'create' => Pages\CreateSchoolLevel::route('/create'),
            'edit' => Pages\EditSchoolLevel::route('/{record}/edit'),
        ];
    }
}
