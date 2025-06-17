<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentAchievement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Storage;

class AchievementsRelationManager extends RelationManager
{
    protected static string $relationship = 'achievements';

    protected static ?string $title = 'ຂໍ້ມູນຜົນງານ/ລາງວັນ';

    protected static ?string $icon = 'heroicon-o-star';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('achievement_type')
                    ->label('ປະເພດຜົນງານ/ລາງວັນ')
                    ->options([
                        'academic' => 'ດ້ານການສຶກສາ',
                        'sports' => 'ດ້ານກິລາ',
                        'arts' => 'ດ້ານສິລະປະ',
                        'social' => 'ດ້ານສັງຄົມ',
                        'other' => 'ອື່ນໆ'
                    ])
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('ຊື່ຜົນງານ/ລາງວັນ')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('ລາຍລະອຽດ')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('award_date')
                    ->label('ວັນທີໄດ້ຮັບ')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('issuer')
                    ->label('ຜູ້ມອບລາງວັນ')
                    ->maxLength(255),

                Forms\Components\FileUpload::make('certificate_path')
                    ->label('ໃບຢັ້ງຢືນ')
                    ->disk('public')
                    ->directory('achievement-certificates')
                    ->visibility('public')
                    ->downloadable()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(10240) // 10MB
                    ->deleteUploadedFileUsing(function ($file) {
                        Storage::disk('public')->delete($file);
                    })
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('achievement_type')
                    ->label('ປະເພດ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'academic' => 'ດ້ານການສຶກສາ',
                        'sports' => 'ດ້ານກິລາ',
                        'arts' => 'ດ້ານສິລະປະ',
                        'social' => 'ດ້ານສັງຄົມ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('ຊື່ຜົນງານ/ລາງວັນ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('award_date')
                    ->label('ວັນທີໄດ້ຮັບ')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('issuer')
                    ->label('ຜູ້ມອບລາງວັນ')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມຜົນງານ/ລາງວັນ')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມຜົນງານ/ລາງວັນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil')
                    ->modalSubmitActionLabel('ບັນທຶກ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ແກ້ໄຂຜົນງານ/ລາງວັນ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash')
                    ->before(function (StudentAchievement $record) {
                        Storage::disk('public')->delete($record->certificate_path);
                    }),
            ]);
    }
}
