<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationGroup = 'ການສື່ສານ';
    protected static ?int $navigationSort = 6;
    protected static ?string $pluralModelLabel = 'ປະກາດຂ່າວສານ';
    protected static ?string $modelLabel = 'ປະກາດຂ່າວສານ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('ຫົວຂໍ້ປະກາດ')
                    ->required()
                    ->maxLength(255),

                Textarea::make('content')
                    ->label('ເນື້ອໃນປະກາດ')
                    ->required()
                    ->rows(6),

                DatePicker::make('start_date')
                    ->label('ວັນທີເລີ່ມສະແດງ')
                    ->nullable(),

                DatePicker::make('end_date')
                    ->label('ວັນທີສິ້ນສຸດສະແດງ')
                    ->nullable()
                    ->after('start_date'),

                Select::make('target_group')
                    ->label('ກຸ່ມເປົ້າໝາຍ')
                    ->options([
                        'all' => 'ທັງໝົດ',
                        'teachers' => 'ຄູສອນ',
                        'students' => 'ນັກຮຽນ',
                        'parents' => 'ຜູ້ປົກຄອງ',
                    ])
                    ->default('all')
                    ->required(),

                Toggle::make('is_pinned')
                    ->label('ປັກໝຸດໄວ້ເທິງສຸດ')
                    ->default(false),

                FileUpload::make('attachment')
                    ->label('ແນບໄຟລ໌')
                    ->directory('announcement-attachments')
                    ->maxSize(10240),

                Hidden::make('created_by')
                    ->default(fn() => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('ຫົວຂໍ້')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('target_group')
                    ->label('ກຸ່ມເປົ້າໝາຍ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'all' => 'ທັງໝົດ',
                        'teachers' => 'ຄູສອນ',
                        'students' => 'ນັກຮຽນ',
                        'parents' => 'ຜູ້ປົກຄອງ',
                        default => $state,
                    }),

                IconColumn::make('is_pinned')
                    ->label('ປັກໝຸດ')
                    ->boolean(),

                TextColumn::make('start_date')
                    ->label('ວັນທີເລີ່ມ')
                    ->date('d/m/Y'),

                TextColumn::make('end_date')
                    ->label('ວັນທີສິ້ນສຸດ')
                    ->date('d/m/Y'),

                TextColumn::make('creator.username')
                    ->label('ຜູ້ສ້າງ')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('ວັນທີສ້າງ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('target_group')
                    ->label('ກຸ່ມເປົ້າໝາຍ')
                    ->options([
                        'all' => 'ທັງໝົດ',
                        'teachers' => 'ຄູສອນ',
                        'students' => 'ນັກຮຽນ',
                        'parents' => 'ຜູ້ປົກຄອງ',
                    ]),

                SelectFilter::make('is_pinned')
                    ->label('ການປັກໝຸດ')
                    ->options([
                        '1' => 'ປັກໝຸດ',
                        '0' => 'ບໍ່ປັກໝຸດ',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
