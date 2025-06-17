<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'ຈັດການການຮຽນການສອນ';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'ການບັນທຶກການຂາດ-ມາຮຽນ';

    protected static ?string $pluralModelLabel = 'ການຂາດ-ມາຮຽນທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'student_code')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ນັກຮຽນ'),
                Forms\Components\Select::make('class_id')
                    ->relationship('schoolClass', 'class_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຫ້ອງຮຽນ'),
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'subject_name_lao')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ວິຊາ'),
                Forms\Components\DatePicker::make('attendance_date')
                    ->required()
                    ->label('ວັນທີ'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'present' => 'ມາ',
                        'absent' => 'ຂາດ',
                        'late' => 'ສະໝານ',
                        'excused' => 'ມີເຫດຜົນ'
                    ])
                    ->label('ສະຖານະ'),
                Forms\Components\Textarea::make('reason')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('ເຫດຜົນ'),
                Forms\Components\Select::make('recorded_by')
                    ->relationship('recorder', 'username')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('ຜູ້ບັນທຶກ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.student_code')
                    ->searchable()
                    ->sortable()
                    ->label('ລະຫັດນັກຮຽນ'),
                Tables\Columns\TextColumn::make('student.first_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ຊື່ນັກຮຽນ'),
                Tables\Columns\TextColumn::make('schoolClass.class_name')
                    ->searchable()
                    ->sortable()
                    ->label('ຫ້ອງຮຽນ'),
                Tables\Columns\TextColumn::make('subject.subject_name_lao')
                    ->searchable()
                    ->sortable()
                    ->label('ວິຊາ'),
                Tables\Columns\TextColumn::make('attendance_date')
                    ->date()
                    ->sortable()
                    ->label('ວັນທີ'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->label('ສະຖານະ'),
                Tables\Columns\TextColumn::make('recorder.username')
                    ->searchable()
                    ->sortable()
                    ->label('ຜູ້ບັນທຶກ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'ມາ',
                        'absent' => 'ຂາດ',
                        'late' => 'ສະໝານ',
                        'excused' => 'ມີເຫດຜົນ'
                    ])
                    ->label('ສະຖານະ'),
                Tables\Filters\Filter::make('attendance_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('ຈາກວັນທີ'),
                        Forms\Components\DatePicker::make('until')
                            ->label('ຫາວັນທີ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '<=', $date),
                            );
                    })
                    ->label('ວັນທີ'),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
