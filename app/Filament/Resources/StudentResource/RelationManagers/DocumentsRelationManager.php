<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'ເອກະສານນັກຮຽນ';
    protected static ?string $icon = 'heroicon-o-document';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('document_type')
                ->label('ປະເພດເອກະສານ')
                ->options([
                    'birth_certificate' => 'ໃບເກີດ',
                    'id_card' => 'ບັດປະຈຳຕົວ',
                    'house_registration' => 'ທະບຽນບ້ານ',
                    'transfer_certificate' => 'ໃບຍ້າຍໂຮງຮຽນ',
                    'other' => 'ອື່ນໆ'
                ])
                ->required(),

            Forms\Components\TextInput::make('document_name')
                ->label('ຊື່ເອກະສານ')
                ->required()
                ->maxLength(255),

            Forms\Components\FileUpload::make('file_path')
                ->label('ເອກະສານ')
                ->disk('public')
                ->directory('student-documents')
                ->visibility('public')
                ->downloadable()
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif'])
                ->required()
                ->maxSize(10240) // 10MB
                ->columnSpanFull()
                ->afterStateUpdated(function ($state, $set) {
                    if ($state) {
                        try {
                            $size = $state->getSize();
                            $type = $state->getMimeType();
                            $set('file_size', $size);
                            $set('file_type', $type);
                        } catch (\Exception $e) {
                            $set('file_size', 0);
                            $set('file_type', '');
                        }
                    }
                })
                ->deleteUploadedFileUsing(function ($file) {
                    Storage::disk('public')->delete($file);
                }),
            Forms\Components\Hidden::make('file_size'),
            Forms\Components\Hidden::make('file_type'),
            Forms\Components\Textarea::make('description')
                ->label('ຄຳອະທິບາຍ')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_name')
            ->columns([
                Tables\Columns\TextColumn::make('document_type')
                    ->label('ປະເພດເອກະສານ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'birth_certificate' => 'ໃບເກີດ',
                        'id_card' => 'ບັດປະຈຳຕົວ',
                        'house_registration' => 'ສຳມະໂນຄົວ',
                        'transfer_certificate' => 'ໃບຍ້າຍໂຮງຮຽນ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('document_name')
                    ->label('ຊື່ເອກະສານ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('ປະເພດຟາຍ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('ຂະໜາດຟາຍ')
                    ->formatStateUsing(fn(?int $state) => $state ? number_format($state / 1024, 2) . ' KB' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('upload_date')
                    ->label('ວັນທີອັບໂຫຼດ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມເອກະສານ')
                    ->icon('heroicon-o-document-plus')
                    ->modalSubmitActionLabel('ບັນທຶກເອກະສານ')
                    ->modalCancelActionLabel('ຍົກເລີກ')
                    ->modalHeading('ເພີ່ມເອກະສານນັກຮຽນ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ')->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash')
                    ->before(function (StudentDocument $record) {
                        Storage::disk('public')->delete($record->file_path);
                    }),
                Tables\Actions\Action::make('download')
                    ->label('ດາວໂຫຼດ')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(StudentDocument $record): string => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
            ]);
    }
}
