<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\AcademicYear;
use App\Models\PaymentImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'ການຊຳລະເງິນ';
    protected static ?string $modelLabel = 'ການຊຳລະເງິນ';
    protected static ?string $pluralModelLabel = 'ການຊຳລະເງິນ';
    protected static ?string $navigationGroup = 'ການເງິນ';
    protected static ?int $navigationSort = 1;

    /**
     * ✅ ຟອມສຳລັບສ້າງ/ແກ້ໄຂການຊຳລະ - ໃຊ້ Model methods
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ຂໍ້ມູນພື້ນຖານ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ນັກຮຽນ')
                                    ->relationship('student', 'first_name_lao')
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->student_code} - {$record->getFullName()}")
                                    ->searchable(['student_code', 'first_name_lao', 'last_name_lao'])
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('ສົກຮຽນ')
                                    ->relationship('academicYear', 'year_name')
                                    ->default(fn() => AcademicYear::where('is_current', true)->first()?->academic_year_id)
                                    ->required()
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('receipt_number')
                                    ->label('ເລກໃບບິນ')
                                    ->required()
                                    ->default(fn() => Payment::generateReceiptNumber())
                                    ->unique(ignoreRecord: true),

                                Forms\Components\DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now()),

                                Forms\Components\Select::make('payment_status')
                                    ->label('ສະຖານະ')
                                    ->options(Payment::getStatusOptions())
                                    ->default('pending')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('ຈຳນວນເງິນ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('cash')
                                    ->label('ເງິນສົດ (ກີບ)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(1000),

                                Forms\Components\TextInput::make('transfer')
                                    ->label('ເງິນໂອນ (ກີບ)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(1000),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('food_money')
                                    ->label('ຄ່າອາຫານ (ກີບ)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(1000),

                                Forms\Components\TextInput::make('late_fee')
                                    ->label('ຄ່າປັບຊ້າ (ກີບ)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(1000),

                                Forms\Components\Select::make('discount_id')
                                    ->label('ສ່ວນຫຼຸດ')
                                    ->relationship('discount', 'discount_name')
                                    ->nullable()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('ຈຳນວນສ່ວນຫຼຸດ (ກີບ)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(1000)
                                    ->readonly(),

                                Forms\Components\TextInput::make('total_amount')
                                    ->label('ລວມທັງໝົດ (ກີບ)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->step(1000),
                            ]),
                    ]),

                Forms\Components\Section::make('ເດືອນທີ່ຈ່າຍ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // ✅ ໃຊ້ Payment::getMonthOptions() ຈາກ Model
                                Forms\Components\CheckboxList::make('tuition_months')
                                    ->label('ເດືອນຄ່າຮຽນ')
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->required()
                                    ->columnSpan(1),

                                // ✅ ໃຊ້ Payment::getMonthOptions() ຈາກ Model
                                Forms\Components\CheckboxList::make('food_months')
                                    ->label('ເດືອນຄ່າອາຫານ')
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->columnSpan(1),
                            ]),
                    ]),

                Forms\Components\Section::make('ຂໍ້ມູນເພີ່ມເຕີມ')
                    ->schema([
                        Forms\Components\Select::make('received_by')
                            ->label('ຜູ້ຮັບເງິນ')
                            ->relationship('receiver', 'username')
                            ->default(auth()->id())
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('note')
                            ->label('ໝາຍເຫດ')
                            ->rows(3)
                            ->maxLength(500),

                        Forms\Components\FileUpload::make('payment_images')
                            ->label('ຮູບໃບບິນ/ໃບໂອນ')
                            ->disk('public')
                            ->directory('payment_receipts')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120)
                            ->imagePreviewHeight('150')
                            ->multiple()
                            ->maxFiles(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    /**
     * ✅ ຕາຕະລາງສະແດງຂໍ້ມູນການຊຳລະ - ໃຊ້ Model methods
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('ເລກໃບບິນ')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('student.student_code')
                    ->label('ລະຫັດນັກຮຽນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student_name')
                    ->label('ຊື່ນັກຮຽນ')
                    ->getStateUsing(fn(Payment $record): string => $record->student?->getFullName() ?? 'N/A')
                    ->searchable(['student.first_name_lao', 'student.last_name_lao'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('ວັນທີຊຳລະ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('ຈຳນວນເງິນ')
                    ->money('LAK')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('LAK')
                            ->label('ລວມທັງໝົດ'),
                    ]),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('ສະຖານະ')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'ລໍຖ້າຢືນຢັນ',
                        'confirmed' => 'ຢືນຢັນແລ້ວ',
                        'cancelled' => 'ຍົກເລີກ',
                        'refunded' => 'ຄືນເງິນ',
                        default => $state,
                    }),

                // ✅ ໃຊ້ Model method getTuitionMonthsDisplay()
                Tables\Columns\TextColumn::make('tuition_months_display')
                    ->label('ເດືອນຄ່າຮຽນ')
                    ->getStateUsing(fn(Payment $record): string => $record->getTuitionMonthsAsNumbers())
                    ->wrap()
                    ->toggleable(),

                // ✅ ໃຊ້ Model method getFoodMonthsDisplay()
                Tables\Columns\TextColumn::make('food_months_display')
                    ->label('ເດືອນຄ່າອາຫານ')
                    ->getStateUsing(fn(Payment $record): string => $record->getFoodMonthsAsNumbers())
                    ->wrap(),

                Tables\Columns\TextColumn::make('receiver.username')
                    ->label('ຜູ້ຮັບເງິນ')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('ເບິ່ງ'),

                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('ແກ້ໄຂ')
                        ->visible(fn(Payment $record): bool => $record->canBeEdited()),

                    Tables\Actions\Action::make('confirm')
                        ->label('ຢືນຢັນ')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Payment $record): bool => $record->isPending())
                        ->action(fn(Payment $record) => $record->update(['payment_status' => 'confirmed'])),

                    Tables\Actions\Action::make('cancel')
                        ->label('ຍົກເລີກ')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Payment $record): bool => $record->isPending())
                        ->action(fn(Payment $record) => $record->update(['payment_status' => 'cancelled'])),

                    Tables\Actions\Action::make('print_receipt')
                        ->label('ພິມໃບບິນ')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn(Payment $record): string => route('print.receipt', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make()
                        ->label('ລຶບ')
                        ->visible(fn(Payment $record): bool => $record->canBeDeleted()),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_multiple')
                        ->label('ຢືນຢັນຫຼາຍລາຍການ')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->where('payment_status', 'pending')
                                ->each(fn(Payment $record) => $record->update(['payment_status' => 'confirmed']));
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('ລຶບຫຼາຍລາຍການ')
                        ->visible(fn(): bool => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->defaultSort('payment_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    /**
     * ✅ ຂໍ້ມູນລະອຽດສຳລັບການເບິ່ງ - ໃຊ້ Model methods
     */
    /**
     * ✅ ປັບປຸງ infolist ໃນ PaymentResource.php ສຳລັບການສະແດງຮູບພາບ
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('ຂໍ້ມູນການຊຳລະ')
                    ->schema([
                        TextEntry::make('receipt_number')
                            ->label('ເລກໃບບິນ')
                            ->weight(FontWeight::Bold)
                            ->copyable(),

                        TextEntry::make('payment_date')
                            ->label('ວັນທີຊຳລະ')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('payment_status')
                            ->label('ສະຖານະ')
                            ->badge()
                            ->color(fn(Payment $record): string => $record->getStatusBadgeColor())
                            ->formatStateUsing(fn(Payment $record): string => $record->getStatusLabel()),
                    ])
                    ->columns(3),

                Section::make('ຂໍ້ມູນນັກຮຽນ')
                    ->schema([
                        TextEntry::make('student.student_code')
                            ->label('ລະຫັດນັກຮຽນ'),

                        TextEntry::make('student.full_name')
                            ->label('ຊື່ນັກຮຽນ')
                            ->getStateUsing(fn(Payment $record): string => $record->student?->getFullName() ?? 'N/A'),

                        TextEntry::make('academicYear.year_name')
                            ->label('ສົກຮຽນ'),
                    ])
                    ->columns(3),

                Section::make('ລາຍລະອຽດເງິນ')
                    ->schema([
                        TextEntry::make('cash')
                            ->label('ເງິນສົດ')
                            ->money('LAK'),

                        TextEntry::make('transfer')
                            ->label('ເງິນໂອນ')
                            ->money('LAK'),

                        TextEntry::make('food_money')
                            ->label('ຄ່າອາຫານ')
                            ->money('LAK'),

                        TextEntry::make('late_fee')
                            ->label('ຄ່າປັບຊ້າ')
                            ->money('LAK'),

                        TextEntry::make('discount_amount')
                            ->label('ສ່ວນຫຼຸດ')
                            ->money('LAK'),

                        TextEntry::make('total_amount')
                            ->label('ລວມທັງໝົດ')
                            ->money('LAK')
                            ->weight(FontWeight::Bold)
                            ->color(Color::Green),
                    ])
                    ->columns(3),

                Section::make('ເດືອນທີ່ຊຳລະ')
                    ->schema([
                        TextEntry::make('tuition_months')
                            ->label('ເດືອນຄ່າຮຽນ')
                            ->getStateUsing(fn(Payment $record): string => $record->getTuitionMonthsDisplay()),

                        TextEntry::make('food_months')
                            ->label('ເດືອນຄ່າອາຫານ')
                            ->getStateUsing(fn(Payment $record): string => $record->getFoodMonthsDisplay()),
                    ])
                    ->columns(2),

                Section::make('ຂໍ້ມູນເພີ່ມເຕີມ')
                    ->schema([
                        TextEntry::make('receiver.username')
                            ->label('ຜູ້ຮັບເງິນ'),

                        TextEntry::make('note')
                            ->label('ໝາຍເຫດ')
                            ->placeholder('ບໍ່ມີໝາຍເຫດ'),
                    ])
                    ->columns(2),

                // ✅ ສ່ວນສະແດງຮູບພາບທີ່ຖືກຕ້ອງ
                Section::make('ຮູບໃບບິນ/ໃບໂອນ')
                    ->schema([
                        TextEntry::make('payment_images')
                            ->label('')
                            ->getStateUsing(function (Payment $record): string {
                                if ($record->images->isEmpty()) {
                                    return 'ບໍ່ມີຮູບພາບ';
                                }

                                $imagesHtml = '';
                                foreach ($record->images as $image) {
                                    $imageUrl = Storage::disk('public')->url($image->image_path);
                                    $imageType = $image->getImageTypeLabel();

                                    $imagesHtml .= "
                                    <div class='inline-block m-2 p-2 border rounded-lg bg-gray-50'>
                                        <div class='text-xs text-gray-600 mb-1'>{$imageType}</div>
                                        <img src='{$imageUrl}' 
                                             alt='Payment Image' 
                                             class='w-32 h-32 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity'
                                             onclick='window.open(\"{$imageUrl}\", \"_blank\")'
                                             style='max-width: 200px; max-height: 200px;'
                                        />
                                        <div class='text-xs text-gray-500 mt-1'>{$image->getFormattedFileSizeAttribute()}</div>
                                    </div>
                                ";
                                }

                                return $imagesHtml;
                            })
                            ->html() // ✅ ສຳຄັນ: ໃຊ້ html() ເພື່ອສະແດງ HTML
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(Payment $record): bool => $record->images->isNotEmpty())
                    ->collapsible(),
            ]);
    }

    /**
     * ✅ ວິທີທີ 2: ໃຊ້ Custom View Component (ແນະນຳ)
     */
    public static function infolistAlternative(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ... ສ່ວນອື່ນໆ ເຄີຍ ...

                Section::make('ຮູບໃບບິນ/ໃບໂອນ')
                    ->schema([
                        // ✅ ໃຊ້ ImageEntry ທີ່ຖືກຕ້ອງ
                        \Filament\Infolists\Components\ViewEntry::make('payment_images')
                            ->label('')
                            ->view('filament.components.payment-images')
                            ->viewData(fn(Payment $record) => [
                                'images' => $record->images,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(Payment $record): bool => $record->images->isNotEmpty())
                    ->collapsible(),
            ]);
    }

    /**
     * ✅ ວິທີທີ 3: ໃຊ້ RepeatableEntry (ສຳລັບຫຼາຍຮູບ)
     */
    public static function infolistWithRepeatable(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ... ສ່ວນອື່ນໆ ...

                Section::make('ຮູບໃບບິນ/ໃບໂອນ')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('images')
                            ->label('')
                            ->schema([
                                TextEntry::make('image_type')
                                    ->label('ປະເພດ')
                                    ->getStateUsing(fn(PaymentImage $record): string => $record->getImageTypeLabel())
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'ໃບໂອນເງິນ' => 'info',
                                        'ໃບບິນ' => 'success',
                                        default => 'gray'
                                    }),

                                TextEntry::make('image_preview')
                                    ->label('ຮູບພາບ')
                                    ->getStateUsing(function (PaymentImage $record): string {
                                        $imageUrl = Storage::disk('public')->url($record->image_path);
                                        return "
                                        <img src='{$imageUrl}' 
                                             alt='Payment Image' 
                                             class='w-48 h-32 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity'
                                             onclick='window.open(\"{$imageUrl}\", \"_blank\")'
                                        />
                                    ";
                                    })
                                    ->html(),

                                TextEntry::make('file_info')
                                    ->label('ຂໍ້ມູນໄຟລ໌')
                                    ->getStateUsing(function (PaymentImage $record): string {
                                        return "ຂະໜາດ: {$record->getFormattedFileSizeAttribute()}<br>
                                            ອັບໂຫຼດ: {$record->upload_date->format('d/m/Y H:i')}";
                                    })
                                    ->html(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(Payment $record): bool => $record->images->isNotEmpty())
                    ->collapsible(),
            ]);
    }

    /**
     * ກຳນົດໜ້າຕ່າງໆ
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\PaymentPage::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    /**
     * Navigation Badge ສະແດງຈຳນວນ pending payments
     */
    public static function getNavigationBadge(): ?string
    {
        return Payment::getPendingCount();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }

    /**
     * Global Search
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'receipt_number',
            'student.student_code',
            'student.first_name_lao',
            'student.last_name_lao',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'ນັກຮຽນ' => $record->student?->getFullName(),
            'ວັນທີ' => $record->payment_date->format('d/m/Y'),
            'ຈຳນວນ' => $record->getFormattedTotal(),
        ];
    }
}