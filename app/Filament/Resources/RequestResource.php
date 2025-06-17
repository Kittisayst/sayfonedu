<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Filament\Resources\RequestResource\RelationManagers;
use App\Models\Request;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateRequest),

                Select::make('request_type')
                    ->label('ປະເພດຄຳຮ້ອງ')
                    ->options([
                        'document_request' => 'ຂໍເອກະສານ',
                        'leave_request' => 'ຂາດຮຽນ/ລາພັກ',
                        'financial_request' => 'ການເງິນ',
                        'other' => 'ອື່ນໆ',
                    ])
                    ->required(),
                TextInput::make('subject')
                    ->label('ຫົວຂໍ້ຄຳຮ້ອງ')
                    ->required()
                    ->maxLength(255),

                Textarea::make('content')
                    ->label('ລາຍລະອຽດຄຳຮ້ອງ')
                    ->required()
                    ->rows(6),

                FileUpload::make('attachment')
                    ->label('ແນບໄຟລ໌')
                    ->directory('request-attachments')
                    ->maxSize(10240),

                Select::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'pending' => 'ລໍຖ້າການດຳເນີນການ',
                        'processing' => 'ກຳລັງດຳເນີນການ',
                        'approved' => 'ອະນຸມັດແລ້ວ',
                        'rejected' => 'ປະຕິເສດ',
                    ])
                    ->default('pending')
                    ->disabled(fn($livewire) => $livewire instanceof Pages\CreateRequest)
                    ->required(),

                Textarea::make('response')
                    ->label('ຄຳຕອບ/ຄຳຄິດເຫັນ')
                    ->rows(4)
                    ->visible(fn($livewire) => !($livewire instanceof Pages\CreateRequest)),

                Select::make('handled_by')
                    ->label('ຜູ້ດຳເນີນການ')
                    ->relationship('handler', 'username')
                    ->searchable()
                    ->visible(fn($livewire) => !($livewire instanceof Pages\CreateRequest))
                    ->disabled(fn($livewire) => $livewire instanceof Pages\EditRequest && !auth()->user()->hasAnyRole(['admin', 'manager'])),

                DateTimePicker::make('handled_at')
                    ->label('ເວລາດຳເນີນການ')
                    ->visible(fn($livewire) => !($livewire instanceof Pages\CreateRequest))
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label('ຜູ້ຍື່ນຄຳຮ້ອງ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('request_type')
                    ->label('ປະເພດຄຳຮ້ອງ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'document_request' => 'ຂໍເອກະສານ',
                        'leave_request' => 'ຂາດຮຽນ/ລາພັກ',
                        'financial_request' => 'ການເງິນ',
                        'other' => 'ອື່ນໆ',
                        default => $state,
                    }),

                TextColumn::make('subject')
                    ->label('ຫົວຂໍ້')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'ລໍຖ້າ',
                        'processing' => 'ກຳລັງດຳເນີນການ',
                        'approved' => 'ອະນຸມັດແລ້ວ',
                        'rejected' => 'ປະຕິເສດ',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('handler.username')
                    ->label('ຜູ້ດຳເນີນການ')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('ວັນທີສ້າງ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('handled_at')
                    ->label('ວັນທີດຳເນີນການ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'pending' => 'ລໍຖ້າການດຳເນີນການ',
                        'processing' => 'ກຳລັງດຳເນີນການ',
                        'approved' => 'ອະນຸມັດແລ້ວ',
                        'rejected' => 'ປະຕິເສດ',
                    ]),

                SelectFilter::make('request_type')
                    ->label('ປະເພດຄຳຮ້ອງ')
                    ->options([
                        'document_request' => 'ຂໍເອກະສານ',
                        'leave_request' => 'ຂາດຮຽນ/ລາພັກ',
                        'financial_request' => 'ການເງິນ',
                        'other' => 'ອື່ນໆ',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // ການດຳເນີນການຄຳຮ້ອງສຳລັບຜູ້ບໍລິຫານ/ຜູ້ຮັບຜິດຊອບ
                Tables\Actions\Action::make('process')
                    ->label('ດຳເນີນການ')
                    ->icon('heroicon-o-cog')
                    ->button()
                    ->color('info')
                    ->visible(
                        fn(Request $record) =>
                        auth()->user()->hasAnyRole(['admin', 'manager', 'teacher']) &&
                        $record->status === 'pending'
                    )
                    ->url(fn(Request $record) => route('filament.admin.resources.requests.edit', $record))
                    ->openUrlInNewTab(),

                // ການອະນຸມັດຄຳຮ້ອງແບບໄວ
                Tables\Actions\Action::make('approve')
                    ->label('ອະນຸມັດ')
                    ->icon('heroicon-o-check')
                    ->button()
                    ->color('success')
                    ->visible(
                        fn(Request $record) =>
                        auth()->user()->hasAnyRole(['admin', 'manager']) &&
                        in_array($record->status, ['pending', 'processing'])
                    )
                    ->requiresConfirmation()
                    ->action(function (Request $record) {
                        $record->update([
                            'status' => 'approved',
                            'handled_by' => auth()->id(),
                            'handled_at' => now(),
                            'response' => $record->response ?? 'ຄຳຮ້ອງຖືກອະນຸມັດແລ້ວ',
                        ]);

                        // ສ້າງການແຈ້ງເຕືອນໃຫ້ຜູ້ຍື່ນຄຳຮ້ອງ
                        \App\Models\Notification::create([
                            'user_id' => $record->user_id,
                            'title' => 'ຄຳຮ້ອງຖືກອະນຸມັດແລ້ວ',
                            'content' => "ຄຳຮ້ອງ '{$record->subject}' ຂອງທ່ານຖືກອະນຸມັດແລ້ວ",
                            'notification_type' => 'request_update',
                            'related_id' => $record->id,
                        ]);
                    }),

                // ການປະຕິເສດຄຳຮ້ອງແບບໄວ
                Tables\Actions\Action::make('reject')
                    ->label('ປະຕິເສດ')
                    ->icon('heroicon-o-x-mark')
                    ->button()
                    ->color('danger')
                    ->visible(
                        fn(Request $record) =>
                        auth()->user()->hasAnyRole(['admin', 'manager']) &&
                        in_array($record->status, ['pending', 'processing'])
                    )
                    ->requiresConfirmation()
                    ->action(function (Request $record) {
                        $record->update([
                            'status' => 'rejected',
                            'handled_by' => auth()->id(),
                            'handled_at' => now(),
                            'response' => $record->response ?? 'ຄຳຮ້ອງຖືກປະຕິເສດ',
                        ]);

                        // ສ້າງການແຈ້ງເຕືອນໃຫ້ຜູ້ຍື່ນຄຳຮ້ອງ
                        \App\Models\Notification::create([
                            'user_id' => $record->user_id,
                            'title' => 'ຄຳຮ້ອງຖືກປະຕິເສດ',
                            'content' => "ຄຳຮ້ອງ '{$record->subject}' ຂອງທ່ານຖືກປະຕິເສດ",
                            'notification_type' => 'request_update',
                            'related_id' => $record->id,
                        ]);
                    }),

                // ຜູ້ຍື່ນຄຳຮ້ອງສາມາດແກ້ໄຂຄຳຮ້ອງໄດ້ ຖ້າຍັງບໍ່ໄດ້ຮັບການອະນຸມັດ
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn(Request $record) =>
                        $record->user_id === auth()->id() &&
                        $record->status === 'pending'
                    ),
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
            'view' => Pages\ViewRequest::route('/{record}'),
        ];
    }

    // ຈຳກັດການເຂົ້າເຖິງຂໍ້ມູນຕາມສິດທິ
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // ຖ້າບໍ່ແມ່ນ admin ຫຼື manager ໃຫ້ສະແດງສະເພາະຄຳຮ້ອງຂອງຕົນເອງ
        if (!auth()->user()->hasAnyRole(['admin', 'manager'])) {
            // ຄູສອນສາມາດເຫັນຄຳຮ້ອງຈາກນັກຮຽນທີ່ຕົນຮັບຜິດຊອບໄດ້
            if (auth()->user()->hasRole('teacher')) {
                $teacher = \App\Models\Teacher::where('user_id', auth()->id())->first();

                if ($teacher) {
                    // ດຶງຫ້ອງຮຽນທີ່ຄູເປັນອາຈານປະຈຳຫ້ອງ
                    $classIds = \App\Models\SchoolClass::where('homeroom_teacher_id', $teacher->id)->pluck('class_id');

                    // ດຶງລາຍຊື່ນັກຮຽນໃນຫ້ອງທີ່ຄູຮັບຜິດຊອບ
                    $studentIds = \App\Models\StudentEnrollment::whereIn('class_id', $classIds)
                        ->pluck('student_id');

                    // ດຶງ user_id ຂອງນັກຮຽນດັ່ງກ່າວ
                    $studentUserIds = \App\Models\Student::whereIn('student_id', $studentIds)
                        ->whereNotNull('user_id')
                        ->pluck('user_id');

                    return $query->where(function ($q) use ($studentUserIds) {
                        $q->where('user_id', auth()->id())
                            ->orWhereIn('user_id', $studentUserIds);
                    });
                }
            }

            return $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
