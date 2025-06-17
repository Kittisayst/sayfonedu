<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentInterestResource\Pages;
use App\Filament\Resources\StudentInterestResource\RelationManagers;
use App\Models\StudentInterest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentInterestResource extends Resource
{
    protected static ?string $model = StudentInterest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນນັກຮຽນ';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'ຄວາມສົນໃຈນັກຮຽນ';

    protected static ?string $pluralModelLabel = 'ຄວາມສົນໃຈນັກຮຽນທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'first_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ນັກຮຽນ'),
                Forms\Components\Select::make('interest_category')
                    ->options([
                        'academic' => 'ວິຊາການ',
                        'sports' => 'ກິລາ',
                        'arts' => 'ສິລະປະ',
                        'music' => 'ດົນຕຣີ',
                        'technology' => 'ເຕັກໂນໂລຢີ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required()
                    ->label('ໝວດໝູ່ຄວາມສົນໃຈ'),
                Forms\Components\TextInput::make('interest_name')
                    ->required()
                    ->maxLength(255)
                    ->label('ຊື່ຄວາມສົນໃຈ'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ລາຍລະອຽດ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.first_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ນັກຮຽນ'),
                Tables\Columns\TextColumn::make('interest_category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'academic' => 'success',
                        'sports' => 'warning',
                        'arts' => 'info',
                        'music' => 'primary',
                        'technology' => 'danger',
                        default => 'gray',
                    })
                    ->label('ໝວດໝູ່ຄວາມສົນໃຈ'),
                Tables\Columns\TextColumn::make('interest_name')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ຄວາມສົນໃຈ'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('ລາຍລະອຽດ'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('ວັນທີສ້າງ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('interest_category')
                    ->options([
                        'academic' => 'ວິຊາການ',
                        'sports' => 'ກິລາ',
                        'arts' => 'ສິລະປະ',
                        'music' => 'ດົນຕຣີ',
                        'technology' => 'ເຕັກໂນໂລຢີ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->label('ໝວດໝູ່ຄວາມສົນໃຈ'),
                Tables\Filters\SelectFilter::make('student')
                    ->relationship('student', 'first_name_lao')
                    ->label('ນັກຮຽນ'),
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
            'index' => Pages\ListStudentInterests::route('/'),
            'create' => Pages\CreateStudentInterest::route('/create'),
            'edit' => Pages\EditStudentInterest::route('/{record}/edit'),
        ];
    }
}
