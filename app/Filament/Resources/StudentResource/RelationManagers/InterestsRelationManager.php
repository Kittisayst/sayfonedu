<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterestsRelationManager extends RelationManager
{
    protected static string $relationship = 'interests';

    protected static ?string $title = 'ຄວາມສົນໃຈຂອງນັກຮຽນ';

    protected static ?string $icon = 'heroicon-o-fire';

    protected static ?string $modelLabel = 'ຄວາມສົນໃຈຂອງນັກຮຽນ';

    protected static ?string $pluralModelLabel = 'ຄວາມສົນໃຈຂອງນັກຮຽນ';

    protected static ?string $recordTitleAttribute = 'ຄວາມສົນໃຈຂອງນັກຮຽນ';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('interest_category')
                    ->label('ປະເພດຄວາມສົນໃຈ')
                    ->options([
                        'academic' => 'ດ້ານການສຶກສາ',
                        'sports' => 'ດ້ານກິລາ',
                        'arts' => 'ດ້ານສິລະປະ',
                        'music' => 'ດ້ານດົນຕີ',
                        'technology' => 'ດ້ານເຕັກໂນໂລຊີ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->required(),
                Forms\Components\TextInput::make('interest_name')
                    ->label('ຊື່ຄວາມສົນໃຈ')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('ລາຍລະອຽດ')
                    ->maxLength(1000),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('interest_category')
                    ->label('ປະເພດ')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'academic' => 'ດ້ານການສຶກສາ',
                        'sports' => 'ດ້ານກິລາ',
                        'arts' => 'ດ້ານສິລະປະ',
                        'music' => 'ດ້ານດົນຕີ',
                        'technology' => 'ດ້ານເຕັກໂນໂລຊີ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('interest_name')
                    ->label('ຊື່ຄວາມສົນໃຈ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('ລາຍລະອຽດ')
                    ->limit(500)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຄວາມສົນໃຈ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຂໍ້ມູນຄວາມສົນໃຈຂອງນັກຮຽນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ'),
            ]);
    }
}
