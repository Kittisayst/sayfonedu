<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpecialNeedsRelationManager extends RelationManager
{
    protected static string $relationship = 'specialNeeds';
    protected static ?string $title = 'ຂໍ້ມູນຄວາມຕ້ອງການພິເສດ';
    protected static ?string $icon = 'heroicon-o-at-symbol';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('need_type')
                    ->label('ປະເພດຄວາມຕ້ອງການພິເສດ')
                    ->options([
                        'learning' => 'ຄວາມຕ້ອງການດ້ານການຮຽນ',
                        'physical' => 'ຄວາມຕ້ອງການດ້ານຮ່າງກາຍ',
                        'emotional' => 'ຄວາມຕ້ອງການດ້ານອາລົມ',
                        'social' => 'ຄວາມຕ້ອງການດ້ານສັງຄົມ',
                        'medical' => 'ຄວາມຕ້ອງການດ້ານການແພດ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('ລາຍລະອຽດຄວາມຕ້ອງການ')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('recommendations')
                    ->label('ຄຳແນະນຳ')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('support_required')
                    ->label('ການຊ່ວຍເຫຼືອທີ່ຕ້ອງການ')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('external_support')
                    ->label('ການຊ່ວຍເຫຼືອຈາກພາຍນອກ')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('ວັນທີເລີ່ມ')
                    ->required()
                    ->default(now()),

                Forms\Components\DatePicker::make('end_date')
                    ->label('ວັນທີສິ້ນສຸດ')
                    ->after('start_date'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('need_type')
                    ->label('ປະເພດຄວາມຕ້ອງການ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'learning' => 'ຄວາມຕ້ອງການດ້ານການຮຽນ',
                        'physical' => 'ຄວາມຕ້ອງການດ້ານຮ່າງກາຍ',
                        'emotional' => 'ຄວາມຕ້ອງການດ້ານອາລົມ',
                        'social' => 'ຄວາມຕ້ອງການດ້ານສັງຄົມ',
                        'medical' => 'ຄວາມຕ້ອງການດ້ານການແພດ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'learning' => 'info',
                        'physical' => 'warning',
                        'emotional' => 'danger',
                        'social' => 'success',
                        'medical' => 'primary',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('ລາຍລະອຽດ')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('ວັນທີເລີ່ມ')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('ວັນທີສິ້ນສຸດ')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຄວາມຕ້ອງການພິເສດ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຄວາມຕ້ອງການພິເສດ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂຄວາມຕ້ອງການພິເສດ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
