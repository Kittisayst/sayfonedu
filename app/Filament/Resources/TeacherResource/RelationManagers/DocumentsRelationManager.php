<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\TeacherDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;
use Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    // ຊື່ແຖບສະແດງຂໍ້ມູນເປັນພາສາລາວ
    protected static ?string $title = 'ເອກະສານ';

    // ປັບແຕ່ງໄອຄອນໃຫ້ເໝາະສົມ
    protected static ?string $icon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ປະເພດເອກະສານ - ໃຊ້ Select ສຳລັບເອກະສານມາດຕະຖານ
                Select::make('document_type')
                    ->label('ປະເພດເອກະສານ')
                    ->options([
                        'cv' => 'ຊີວະປະຫວັດ (CV)',
                        'certificate' => 'ໃບຢັ້ງຢືນ',
                        'degree' => 'ວຸດທິການສຶກສາ',
                        'id_card' => 'ບັດປະຈຳຕົວ',
                        'family_book' => 'ປຶ້ມສຳມະໂນຄົວ',
                        'contract' => 'ສັນຍາຈ້າງ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required()
                    ->searchable()
                    ->native(false),

                // ຊື່ເອກະສານ
                TextInput::make('document_name')
                    ->label('ຊື່ເອກະສານ')
                    ->required()
                    ->maxLength(255)
                    ->helperText('ກະລຸນາໃສ່ຊື່ທີ່ອະທິບາຍເຖິງເອກະສານນີ້')
                    ->placeholder('ເຊັ່ນ: ໃບປະກາດຈົບປີ 2023'),

                // ອັບໂຫລດໄຟລ໌
                FileUpload::make('file_path')
                    ->label('ອັບໂຫລດໄຟລ໌')
                    ->required()
                    ->directory('teacher-documents') // ບ່ອນເກັບໄຟລ໌ໃນ storage
                    ->visibility('private') // ຮັກສາຄວາມປອດໄພໄຟລ໌ສ່ວນຕົວ
                    ->maxSize(10240) // 10MB ຂະໜາດສູງສຸດ
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ])
                    // ຄຳອະທິບາຍປະເພດໄຟລ໌ທີ່ຮອງຮັບ
                    ->helperText('ຮອງຮັບ: PDF, JPEG, PNG, DOC, DOCX (ບໍ່ເກີນ 10MB)')
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

                // ຂະໜາດໄຟລ໌ (hidden field ທີ່ຈະຖືກເຕີມຂໍ້ມູນອັດຕະໂນມັດ)
                Forms\Components\Hidden::make('file_size'),
                Forms\Components\Hidden::make('file_type'),
                // ວັນທີອັບໂຫລດ
                DatePicker::make('upload_date')
                    ->label('ວັນທີອັບໂຫລດ')
                    ->default(now())
                    ->required()
                    ->displayFormat('d/m/Y'),

                // ຄຳອະທິບາຍເພີ່ມເຕີມ
                Textarea::make('description')
                    ->label('ຄຳອະທິບາຍເພີ່ມເຕີມ')
                    ->placeholder('ລາຍລະອຽດກ່ຽວກັບເອກະສານນີ້...')
                    ->rows(3)
                    ->columnSpan('full'),
            ])
            ->columns(2); // ຈັດແບບ 2 ຖັນໃນຟອມ
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_name')
            ->columns([
                TextColumn::make('document_type')
                    ->label('ປະເພດເອກະສານ')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cv' => 'info',
                        'certificate' => 'success',
                        'degree' => 'warning',
                        'contract' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cv' => 'ຊີວະປະຫວັດ',
                        'certificate' => 'ໃບຢັ້ງຢືນ',
                        'degree' => 'ວຸດທິການສຶກສາ',
                        'id_card' => 'ບັດປະຈຳຕົວ',
                        'family_book' => 'ປຶ້ມສຳມະໂນຄົວ',
                        'contract' => 'ສັນຍາຈ້າງ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    })
                    ->searchable(),

                TextColumn::make('document_name')
                    ->label('ຊື່ເອກະສານ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('file_size')
                    ->label('ຂະໜາດ')
                    ->formatStateUsing(fn($state) => number_format($state) . ' KB')
                    ->sortable(),

                IconColumn::make('file_type')
                    ->label('ປະເພດໄຟລ໌')
                    ->tooltip(fn($state) => strtoupper($state))
                    ->icon(fn(string $state): string => match (strtolower($state)) {
                        'pdf' => 'heroicon-o-document-text',
                        'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'heroicon-o-document-text',
                        'jpg', 'jpeg', 'png' => 'heroicon-o-photo',
                        'xls', 'xlsx' => 'heroicon-o-table-cells',
                        'ppt', 'pptx' => 'heroicon-o-presentation-chart-bar',
                        'zip', 'rar' => 'heroicon-o-archive-box',
                        default => 'heroicon-o-document',
                    })
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'pdf' => 'danger',
                        'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'info',
                        'jpg', 'jpeg', 'png' => 'success',
                        'xls', 'xlsx' => 'warning',
                        'ppt', 'pptx' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('upload_date')
                    ->label('ວັນທີອັບໂຫລດ')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                // ຕົວກອງປະເພດເອກະສານ
                SelectFilter::make('document_type')
                    ->label('ປະເພດເອກະສານ')
                    ->options([
                        'cv' => 'ຊີວະປະຫວັດ (CV)',
                        'certificate' => 'ໃບຢັ້ງຢືນ',
                        'degree' => 'ວຸດທິການສຶກສາ',
                        'id_card' => 'ບັດປະຈຳຕົວ',
                        'family_book' => 'ປຶ້ມສຳມະໂນຄົວ',
                        'contract' => 'ສັນຍາຈ້າງ',
                        'other' => 'ອື່ນໆ',
                    ]),

                // ຕົວກອງໄຟລ໌ຕາມປະເພດໄຟລ໌
                SelectFilter::make('file_type')
                    ->label('ປະເພດໄຟລ໌')
                    ->options([
                        'pdf' => 'PDF',
                        'doc' => 'DOC',
                        'docx' => 'DOCX',
                        'jpg' => 'JPG',
                        'jpeg' => 'JPEG',
                        'png' => 'PNG',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ເພີ່ມເອກະສານ')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('ເບິ່ງ')
                    ->icon('heroicon-o-eye'),

                // ສ້າງ Action ພິເສດເພື່ອດາວໂຫລດໄຟລ໌
                Action::make('download')
                    ->label('ດາວໂຫລດ')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ')
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->label('ລືບ')
                    ->icon('heroicon-o-trash')
                    ->before(function (TeacherDocument $record) {
                        Storage::disk('public')->delete($record->file_path);
                    }),
            ])
            ->defaultSort('upload_date', 'desc'); // ຮຽງຕາມວັນທີອັບໂຫລດລ່າສຸດ
    }
}