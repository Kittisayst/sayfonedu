<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherTaskResource\Pages;
use App\Filament\Resources\TeacherTaskResource\RelationManagers;
use App\Models\Teacher;
use App\Models\TeacherTask;
use App\Tables\Columns\TaskProgress;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherTaskResource extends Resource
{
    protected static ?string $model = TeacherTask::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = "ມອບໝາຍວຽກ";
    protected static ?string $navigationGroup = 'ການຈັດການຂໍ້ມູນຄູສອນ';
    protected static ?string $pluralModelLabel = 'ໜ້າວຽກທັງໝົດ';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // ຖ້າເປັນຄູສອນ, ສະແດງສະເພາະວຽກທີ່ຖືກມອບໝາຍໃຫ້
        if (auth()->user()->hasRole('teacher')) {
            $query->where('assigned_to', auth()->user()->teacher->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ຂໍ້ມູນໜ້າວຽກ')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('ຫົວຂໍ້')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\Textarea::make('description')
                            ->label('ລາຍລະອຽດ')
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\Select::make('assigned_by')
                            ->label('ມອບໝາຍໂດຍ')
                            ->relationship('assignedBy', 'username')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\Select::make('assigned_to')
                            ->label('ມອບໝາຍໃຫ້')
                            ->relationship('assignedTo', 'user_id', fn(Builder $query) => $query->with('user'))
                            ->getOptionLabelFromRecordUsing(fn(Teacher $record) => $record->user ? $record->user->username : "{$record->first_name_lao} {$record->last_name_lao}")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\Select::make('priority')
                            ->label('ຄວາມສຳຄັນ')
                            ->options([
                                'low' => 'ຕ່ຳ',
                                'medium' => 'ປານກາງ',
                                'high' => 'ສູງ',
                                'urgent' => 'ດ່ວນ',
                            ])
                            ->default('medium')
                            ->required()
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('ວັນທີເລີ່ມຕົ້ນ')
                            ->required()
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('ວັນທີກຳນົດສົ່ງ')
                            ->required()
                            ->afterOrEqual('start_date')
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),

                        Forms\Components\Select::make('status')
                            ->label('ສະຖານະ')
                            ->options([
                                'pending' => 'ລໍຖ້າ',
                                'in_progress' => 'ກຳລັງດຳເນີນການ',
                                'completed' => 'ສຳເລັດແລ້ວ',
                                'overdue' => 'ເກີນກຳນົດ',
                            ])
                            ->default('pending')
                            ->required()
                            ->disabled(fn() => auth()->user()->hasRole('teacher')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('ຄວາມຄືບໜ້າ')
                    ->schema([
                        Forms\Components\TextInput::make('progress')
                            ->live()
                            ->label('ຄວາມຄືບໜ້າ (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(5)
                            ->required(),

                        Forms\Components\Textarea::make('latest_update')
                            ->label('ການອັບເດດຫຼ້າສຸດ')
                            ->maxLength(500),

                        Forms\Components\Textarea::make('completion_note')
                            ->label('ບັນທຶກການສຳເລັດວຽກ')
                            ->maxLength(500)
                            ->visible(fn(Forms\Get $get) => $get('status') === 'completed'),

                        Forms\Components\DateTimePicker::make('completion_date')
                            ->label('ວັນທີສຳເລັດວຽກ')
                            ->visible(fn(Forms\Get $get) => $get('status') === 'completed'),

                        Forms\Components\Radio::make('rating')
                            ->label('ຄະແນນປະເມີນຜົນ')
                            ->options([
                                1 => '1 - ຕ່ຳຫຼາຍ',
                                2 => '2 - ຕ່ຳ',
                                3 => '3 - ປານກາງ',
                                4 => '4 - ດີ',
                                5 => '5 - ດີຫຼາຍ',
                            ])
                            ->visible(fn(Forms\Get $get) => $get('status') === 'completed'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('ຫົວຂໍ້')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('assignedBy.username')
                    ->label('ມອບໝາຍໂດຍ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedTo.user.username')
                    ->label('ມອບໝາຍໃຫ້')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\SelectColumn::make('priority')
                    ->label('ຄວາມສຳຄັນ')
                    ->options([
                        'low' => 'ຕ່ຳ',
                        'medium' => 'ປານກາງ',
                        'high' => 'ສູງ',
                        'urgent' => 'ດ່ວນ',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('ວັນທີກຳນົດສົ່ງ')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(
                        fn(TeacherTask $record): string =>
                        $record->isOverdue ? 'danger' : 'primary'
                    ),

                Tables\Columns\SelectColumn::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'pending' => 'ລໍຖ້າ',
                        'in_progress' => 'ກຳລັງດຳເນີນການ',
                        'completed' => 'ສຳເລັດແລ້ວ',
                        'overdue' => 'ເກີນກຳນົດ',
                    ])
                    ->sortable(),

                TaskProgress::make('progress')
                    ->label('ຄວາມຄືບໜ້າ')
                    ->maxValue(100),

                Tables\Columns\TextColumn::make('completion_date')
                    ->label('ວັນທີສຳເລັດວຽກ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rating')
                    ->label('ຄະແນນ')
                    ->formatStateUsing(fn($state) => $state ? "{$state}/5" : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'pending' => 'ລໍຖ້າ',
                        'in_progress' => 'ກຳລັງດຳເນີນການ',
                        'completed' => 'ສຳເລັດແລ້ວ',
                        'overdue' => 'ເກີນກຳນົດ',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('ຄວາມສຳຄັນ')
                    ->options([
                        'low' => 'ຕ່ຳ',
                        'medium' => 'ປານກາງ',
                        'high' => 'ສູງ',
                        'urgent' => 'ດ່ວນ',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->label('ເກີນກຳນົດ')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('due_date', '<', now())
                            ->where('status', '!=', 'completed')
                    ),

                Tables\Filters\Filter::make('due_this_week')
                    ->label('ກຳນົດໃນອາທິດນີ້')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereBetween('due_date', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                            ->where('status', '!=', 'completed')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('ເບິ່ງ'),
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ')
                    ->visible(fn() => !auth()->user()->hasRole('teacher')),
                Tables\Actions\Action::make('mark_complete')
                    ->label('ສຳເລັດວຽກ')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('completion_note')
                            ->label('ບັນທຶກການສຳເລັດວຽກ')
                            ->required(),
                        Forms\Components\Radio::make('rating')
                            ->label('ຄະແນນປະເມີນຜົນ')
                            ->options([
                                1 => '1 - ຕ່ຳຫຼາຍ',
                                2 => '2 - ຕ່ຳ',
                                3 => '3 - ປານກາງ',
                                4 => '4 - ດີ',
                                5 => '5 - ດີຫຼາຍ',
                            ])
                            ->required(),
                    ])
                    ->action(function (TeacherTask $record, array $data): void {
                        $record->markAsCompleted($data['completion_note']);
                        $record->setRating($data['rating']);
                        $record->save();
                    })
                    ->visible(
                        fn(TeacherTask $record): bool =>
                        $record->status !== 'completed' && 
                        auth()->user()->hasRole('teacher') &&
                        $record->assigned_to === auth()->user()->teacher->id
                    )
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
            'index' => Pages\ListTeacherTasks::route('/'),
            'create' => Pages\CreateTeacherTask::route('/create'),
            'edit' => Pages\EditTeacherTask::route('/{record}/edit'),
        ];
    }
}
