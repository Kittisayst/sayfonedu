<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\RelationManagers;
use App\Models\Message;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $recordTitleAttribute = 'subject';
    protected static ?string $navigationGroup = 'ການສື່ສານ';
    protected static ?int $navigationSort = 6;
    protected static ?string $pluralModelLabel = 'ຂໍ້ຄວາມ';
    protected static ?string $modelLabel = 'ຂໍ້ຄວາມ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('sender_id')
                    ->default(fn() => auth()->id()),

                Select::make('receiver_id')
                    ->label('ຜູ້ຮັບ')
                    ->relationship('receiver', 'username')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('subject')
                    ->label('ຫົວຂໍ້')
                    ->required()
                    ->maxLength(255),

                Textarea::make('message_content')
                    ->label('ເນື້ອໃນຂໍ້ຄວາມ')
                    ->required()
                    ->rows(6),

                FileUpload::make('attachment')
                    ->label('ແນບໄຟລ໌')
                    ->directory('message-attachments')
                    ->maxSize(10240),

                Toggle::make('read_status')
                    ->label('ສະຖານະການອ່ານ')
                    ->visible(fn($livewire) => $livewire instanceof Pages\EditMessage)
                    ->disabled(),

                DateTimePicker::make('read_at')
                    ->label('ເວລາທີ່ອ່ານ')
                    ->visible(fn($livewire) => $livewire instanceof Pages\EditMessage)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.username')
                    ->label('ຜູ້ສົ່ງ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('receiver.username')
                    ->label('ຜູ້ຮັບ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subject')
                    ->label('ຫົວຂໍ້')
                    ->searchable()
                    ->limit(50),

                IconColumn::make('read_status')
                    ->label('ສະຖານະ')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label('ວັນທີສົ່ງ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('read_status')
                    ->label('ສະຖານະການອ່ານ')
                    ->options([
                        '1' => 'ອ່ານແລ້ວ',
                        '0' => 'ຍັງບໍ່ໄດ້ອ່ານ',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_read')
                    ->label('ໝາຍວ່າອ່ານແລ້ວ')
                    ->icon('heroicon-o-check')
                    ->visible(fn(Message $record) => !$record->read_status && $record->receiver_id === auth()->id())
                    ->action(function (Message $record) {
                        $record->update([
                            'read_status' => true,
                            'read_at' => now(),
                        ]);
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn(Message $record) => $record->sender_id === auth()->id()),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn(Message $record) => $record->sender_id === auth()->id()),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }

    // ຈຳກັດການເຂົ້າເຖິງຂໍ້ມູນເພື່ອເຫັນແຕ່ຂໍ້ມູນຂອງຕົນເອງ
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            });
    }
}
