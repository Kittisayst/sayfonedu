<?php

namespace App\Filament\Resources;

use App\Filament\Components\FileTypeColumnIcon;
use App\Filament\Resources\TeacherDocumentResource\Pages;
use App\Filament\Resources\TeacherDocumentResource\RelationManagers;
use App\Models\TeacherDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherDocumentResource extends Resource
{
    protected static ?string $model = TeacherDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ການຈັດການຂໍ້ມູນຄູສອນ';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'ເອກະສານຄູ';

    protected static ?string $pluralModelLabel = 'ເອກະສານຄູທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->relationship('teacher', 'first_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຄູ'),
                Forms\Components\Select::make('document_type')
                    ->options([
                        'certificate' => 'ໃບຢັ້ງຢືນ',
                        'diploma' => 'ປະລິນຍາ',
                        'cv' => 'ປະຫວັດສ່ວນຕົວ',
                        'contract' => 'ສັນຍາ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required()
                    ->label('ປະເພດເອກະສານ'),
                Forms\Components\TextInput::make('document_name')
                    ->required()
                    ->maxLength(255)
                    ->label('ຊື່ເອກະສານ'),
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->directory('teacher-documents')
                    ->maxSize(10240)
                    ->label('ໄຟລ໌'),
                Forms\Components\TextInput::make('file_size')
                    ->numeric()
                    ->label('ຂະໜາດໄຟລ໌ (KB)'),
                Forms\Components\TextInput::make('file_type')
                    ->maxLength(50)
                    ->label('ປະເພດໄຟລ໌'),
                Forms\Components\DatePicker::make('upload_date')
                    ->required()
                    ->label('ວັນທີອັບໂຫຼດ'),
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
                Tables\Columns\TextColumn::make('teacher.first_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຄູ'),
                Tables\Columns\TextColumn::make('document_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'certificate' => 'success',
                        'diploma' => 'info',
                        'cv' => 'warning',
                        'contract' => 'primary',
                        default => 'gray',
                    })
                    ->label('ປະເພດເອກະສານ'),
                Tables\Columns\TextColumn::make('document_name')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ເອກະສານ'),
                Tables\Columns\TextColumn::make('file_size')
                    ->numeric()
                    ->sortable()
                    ->label('ຂະໜາດໄຟລ໌ (KB)'),
                FileTypeColumnIcon::make('file_type')
                    ->label('ປະເພດໄຟລ໌'),
                Tables\Columns\TextColumn::make('upload_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີອັບໂຫຼດ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_type')
                    ->options([
                        'certificate' => 'ໃບຢັ້ງຢືນ',
                        'diploma' => 'ປະລິນຍາ',
                        'cv' => 'ປະຫວັດສ່ວນຕົວ',
                        'contract' => 'ສັນຍາ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->label('ປະເພດເອກະສານ'),
                Tables\Filters\SelectFilter::make('teacher')
                    ->relationship('teacher', 'first_name_lao')
                    ->label('ຄູ'),
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
            'index' => Pages\ListTeacherDocuments::route('/'),
            'create' => Pages\CreateTeacherDocument::route('/create'),
            'edit' => Pages\EditTeacherDocument::route('/{record}/edit'),
        ];
    }
}
