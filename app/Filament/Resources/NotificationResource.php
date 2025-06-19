<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationGroup = 'ການສື່ສານ';
    protected static ?int $navigationSort = 6;
    protected static ?string $pluralModelLabel = 'ການແຈ້ງເຕືອນ';
    protected static ?string $modelLabel = 'ການແຈ້ງເຕືອນ';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('ຜູ້ຮັບການແຈ້ງເຕືອນ')
                    ->relationship('user', 'username')
                    ->required()
                    ->searchable(),

                TextInput::make('title')
                    ->label('ຫົວຂໍ້ການແຈ້ງເຕືອນ')
                    ->required()
                    ->maxLength(255),

                Textarea::make('content')
                    ->label('ເນື້ອໃນການແຈ້ງເຕືອນ')
                    ->nullable()
                    ->rows(4),

                Select::make('notification_type')
                    ->label('ປະເພດການແຈ້ງເຕືອນ')
                    ->options([
                        'new_message' => 'ຂໍ້ຄວາມໃໝ່',
                        'announcement' => 'ປະກາດຂ່າວສານ',
                        'grade_update' => 'ຄະແນນອັບເດດ',
                        'attendance' => 'ການຂາດຮຽນ',
                        'payment_due' => 'ກຳນົດຊຳລະເງິນ',
                        'request_update' => 'ອັບເດດຄຳຮ້ອງ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required(),

                TextInput::make('related_id')
                    ->label('ID ອ້າງອີງ')
                    ->numeric()
                    ->nullable(),

                Toggle::make('is_read')
                    ->label('ອ່ານແລ້ວ')
                    ->default(false),

                DateTimePicker::make('read_at')
                    ->label('ເວລາທີ່ອ່ານ')
                    ->nullable()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label('ຜູ້ຮັບ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('ຫົວຂໍ້')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('notification_type')
                    ->label('ປະເພດ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'new_message' => 'ຂໍ້ຄວາມໃໝ່',
                        'announcement' => 'ປະກາດຂ່າວສານ',
                        'grade_update' => 'ຄະແນນອັບເດດ',
                        'attendance' => 'ການຂາດຮຽນ',
                        'payment_due' => 'ກຳນົດຊຳລະເງິນ',
                        'request_update' => 'ອັບເດດຄຳຮ້ອງ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    }),

                IconColumn::make('is_read')
                    ->label('ອ່ານແລ້ວ')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('created_at')
                    ->label('ວັນທີສ້າງ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('read_at')
                    ->label('ວັນທີອ່ານ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_read')
                    ->label('ສະຖານະການອ່ານ')
                    ->options([
                        '1' => 'ອ່ານແລ້ວ',
                        '0' => 'ຍັງບໍ່ໄດ້ອ່ານ',
                    ]),

                SelectFilter::make('notification_type')
                    ->label('ປະເພດການແຈ້ງເຕືອນ')
                    ->options([
                        'new_message' => 'ຂໍ້ຄວາມໃໝ່',
                        'announcement' => 'ປະກາດຂ່າວສານ',
                        'grade_update' => 'ຄະແນນອັບເດດ',
                        'attendance' => 'ການຂາດຮຽນ',
                        'payment_due' => 'ກຳນົດຊຳລະເງິນ',
                        'request_update' => 'ອັບເດດຄຳຮ້ອງ',
                        'other' => 'ອື່ນໆ',
                    ]),
            ])
            ->actions([
                Action::make('mark_as_read')
                    ->label('ໝາຍວ່າອ່ານແລ້ວ')
                    ->icon('heroicon-o-check')
                    ->visible(fn(Notification $record) => !$record->is_read && $record->user_id === auth()->id())
                    ->action(function (Notification $record) {
                        $record->update([
                            'is_read' => true,
                            'read_at' => now(),
                        ]);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
        ];
    }

    // ຈຳກັດການເຂົ້າເຖິງຂໍ້ມູນຕາມສິດທິ
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // ຖ້າຜູ້ໃຊ້ບໍ່ແມ່ນ admin ໃຫ້ສະແດງສະເພາະການແຈ້ງເຕືອນຂອງຕົນເອງ
        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
