<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Discount;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Colors\Color;

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
     * ຟອມສຳລັບສ້າງ/ແກ້ໄຂການຊຳລະ
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
                                    ->default(fn() => 'PAY-' . now()->format('YmdHis'))
                                    ->unique(ignoreRecord: true),

                                Forms\Components\DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now()),

                                Forms\Components\Select::make('payment_status')
                                    ->label('ສະຖານະ')
                                    ->options([
                                        'pending' => 'ລໍຖ້າຢືນຢັນ',
                                        'confirmed' => 'ຢືນຢັນແລ້ວ',
                                        'cancelled' => 'ຍົກເລີກ',
                                        'refunded' => 'ຄືນເງິນ',
                                    ])
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
                                Forms\Components\CheckboxList::make('tuition_months')
                                    ->label('ເດືອນຄ່າຮຽນ')
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->required()
                                    ->columnSpan(1),

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
                            ->relationship('receiver', 'name')
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
                            // ->relationship('images', 'image_path')
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
     * ຕາຕະລາງສະແດງຂໍ້ມູນການຊຳລະ
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

                Tables\Columns\TextColumn::make('tuition_months_count')
                    ->label('ເດືອນຄ່າຮຽນ')
                    ->getStateUsing(
                        fn(Payment $record): string =>
                        count(json_decode($record->tuition_months, true) ?? []) . ' ເດືອນ'
                    )
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('receiver.name')
                    ->label('ຜູ້ຮັບເງິນ')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('ສະຖານະ')
                    ->options([
                        'pending' => 'ລໍຖ້າຢືນຢັນ',
                        'confirmed' => 'ຢືນຢັນແລ້ວ',
                        'cancelled' => 'ຍົກເລີກ',
                        'refunded' => 'ຄືນເງິນ',
                    ])
                    ->multiple(),

                SelectFilter::make('academic_year')
                    ->label('ສົກຮຽນ')
                    ->relationship('academicYear', 'year_name')
                    ->preload(),

                Filter::make('payment_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('ຈາກວັນທີ'),
                        DatePicker::make('until')
                            ->label('ຫາວັນທີ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),

                Filter::make('amount_range')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')
                            ->label('ຈຳນວນເງິນຕ່ຳສຸດ')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_amount')
                            ->label('ຈຳນວນເງິນສູງສຸດ')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn(Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn(Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('ເບິ່ງ'),

                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('ແກ້ໄຂ')
                        ->visible(
                            fn(Payment $record): bool =>
                            $record->payment_status === 'pending' || auth()->user()->hasRole('admin')
                        ),

                    Tables\Actions\Action::make('confirm')
                        ->label('ຢືນຢັນ')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Payment $record): bool => $record->payment_status === 'pending')
                        ->action(fn(Payment $record) => $record->update(['payment_status' => 'confirmed'])),

                    Tables\Actions\Action::make('cancel')
                        ->label('ຍົກເລີກ')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Payment $record): bool => $record->payment_status === 'pending')
                        ->action(fn(Payment $record) => $record->update(['payment_status' => 'cancelled'])),

                    Tables\Actions\Action::make('print_receipt')
                        ->label('ພິມໃບບິນ')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn(Payment $record): string => route('print.receipt', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make()
                        ->label('ລຶບ')
                        ->visible(fn(): bool => auth()->user()->hasRole('admin')),
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
     * ຂໍ້ມູນລະອຽດສຳລັບການເບິ່ງ
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
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'cancelled' => 'danger',
                                'refunded' => 'info',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'ລໍຖ້າຢືນຢັນ',
                                'confirmed' => 'ຢືນຢັນແລ້ວ',
                                'cancelled' => 'ຍົກເລີກ',
                                'refunded' => 'ຄືນເງິນ',
                                default => $state,
                            }),
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
                            ->getStateUsing(
                                fn(Payment $record): string =>
                                implode(', ', json_decode($record->tuition_months, true) ?? [])
                            ),

                        TextEntry::make('food_months')
                            ->label('ເດືອນຄ່າອາຫານ')
                            ->getStateUsing(
                                fn(Payment $record): string =>
                                implode(', ', json_decode($record->food_months, true) ?? []) ?: 'ບໍ່ມີ'
                            ),
                    ])
                    ->columns(2),

                Section::make('ຂໍ້ມູນເພີ່ມເຕີມ')
                    ->schema([
                        TextEntry::make('receiver.name')
                            ->label('ຜູ້ຮັບເງິນ'),

                        TextEntry::make('note')
                            ->label('ໝາຍເຫດ')
                            ->placeholder('ບໍ່ມີໝາຍເຫດ'),

                        ImageEntry::make('images.image_path')
                            ->label('ຮູບໃບບິນ')
                            ->disk('public')
                            ->height(200)
                            ->width(200),
                    ])
                    ->columns(2),
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
        return static::getModel()::where('payment_status', 'pending')->count();
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
            'ຈຳນວນ' => number_format($record->total_amount) . ' ກີບ',
        ];
    }
}