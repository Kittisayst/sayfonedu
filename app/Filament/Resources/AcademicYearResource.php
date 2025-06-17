<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Filament\Resources\AcademicYearResource\RelationManagers;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static ?string $navigationGroup = 'ຈັດການຊັ້ນຮຽນ ແລະ ຫ້ອງຮຽນ';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return 'ສົກຮຽນ';
    }

    public static function getModelLabel(): string
    {
        return 'ສົກຮຽນ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'ສົກຮຽນທັງໝົດ';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ຂໍ້ມູນສົກຮຽນ')
                    ->schema([
                        Forms\Components\TextInput::make('year_name')
                            ->label('ຊື່ສົກຮຽນ')
                            ->placeholder('2023-2024')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('ວັນທີເລີ່ມຕົ້ນ')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('ວັນທີສິ້ນສຸດ')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->afterOrEqual('start_date'),

                        Forms\Components\Toggle::make('is_current')
                            ->label('ເປັນສົກຮຽນປັດຈຸບັນ')
                            ->default(false)
                            ->inline(false),

                        Forms\Components\Select::make('status')
                            ->label('ສະຖານະ')
                            ->options([
                                'active' => 'ໃຊ້ງານ',
                                'inactive' => 'ບໍ່ໃຊ້ງານ',
                                'completed' => 'ສຳເລັດແລ້ວ',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year_name')
                    ->label('ຊື່ສົກຮຽນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('ວັນທີເລີ່ມຕົ້ນ')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('ວັນທີສິ້ນສຸດ')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('ສົກຮຽນປັດຈຸບັນ')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'ໃຊ້ງານ',
                        'inactive' => 'ບໍ່ໃຊ້ງານ',
                        'completed' => 'ສຳເລັດແລ້ວ',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂລ່າສຸດ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'active' => 'ໃຊ້ງານ',
                        'inactive' => 'ບໍ່ໃຊ້ງານ',
                        'completed' => 'ສຳເລັດແລ້ວ',
                    ]),
                Tables\Filters\TernaryFilter::make('is_current')
                    ->label('ສົກຮຽນປັດຈຸບັນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລຶບ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('ລຶບ'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
