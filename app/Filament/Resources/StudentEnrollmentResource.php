<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentEnrollmentResource\Pages;
use App\Filament\Resources\StudentEnrollmentResource\RelationManagers;
use App\Models\StudentEnrollment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentEnrollmentResource extends Resource
{
    protected static ?string $model = StudentEnrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'ຈັດການຊັ້ນຮຽນ ແລະ ຫ້ອງຮຽນ';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'ລົງທະບຽນຮຽນນັກຮຽນ';

    protected static ?string $pluralModelLabel = 'ການລົງທະບຽນທົງໝົດ';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ຂໍ້ມູນການລົງທະບຽນ')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('ນັກຮຽນ')
                            ->relationship('student', 'first_name_lao')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name_lao} {$record->last_name_lao}")
                            ->searchable(['first_name_lao', 'last_name_lao'])
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('is_new_student', false);
                                }
                            }),

                        Forms\Components\Select::make('class_id')
                            ->label('ຫ້ອງຮຽນ')
                            ->relationship('schoolClass', 'class_name')
                            ->required()
                            ->preload(),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('ສົກຮຽນ')
                            ->relationship('academicYear', 'year_name')
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $set('is_new_student', true);
                                    
                                    // ກວດສອບການລົງທະບຽນຊ້ຳ
                                    $studentId = $get('student_id');
                                    if ($studentId) {
                                        $existingEnrollment = StudentEnrollment::where('student_id', $studentId)
                                            ->where('academic_year_id', $state)
                                            ->first();

                                        if ($existingEnrollment) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('ບໍ່ສາມາດລົງທະບຽນໄດ້')
                                                ->body('ນັກຮຽນຄົນນີ້ໄດ້ລົງທະບຽນໃນສົກຮຽນນີ້ແລ້ວ')
                                                ->danger()
                                                ->send();

                                            $set('student_id', null);
                                            $set('academic_year_id', null);
                                        }
                                    }
                                }
                            }),

                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('ວັນທີລົງທະບຽນ')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\Select::make('enrollment_status')
                            ->label('ສະຖານະການລົງທະບຽນ')
                            ->options([
                                'enrolled' => 'ກຳລັງຮຽນ',
                                'transferred' => 'ຍ້າຍໂຮງຮຽນ',
                                'dropped' => 'ອອກກາງຄັນ',
                            ])
                            ->default('enrolled')
                            ->required(),

                        Forms\Components\Select::make('previous_class_id')
                            ->label('ຫ້ອງຮຽນກ່ອນໜ້າ')
                            ->relationship('previousClass', 'class_name')
                            ->preload()
                            ->visible(fn (Forms\Get $get) => !$get('is_new_student')),

                        Forms\Components\Toggle::make('is_new_student')
                            ->label('ເປັນນັກຮຽນໃໝ່')
                            ->default(true)
                            ->inline(false)
                            ->live(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.first_name_lao')
                    ->label('ນັກຮຽນ')
                    ->formatStateUsing(fn ($record) => $record ? "{$record->student->first_name_lao} {$record->student->last_name_lao}" : '')
                    ->searchable(['first_name_lao', 'last_name_lao'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('schoolClass.class_name')
                    ->label('ຫ້ອງຮຽນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.year_name')
                    ->label('ສົກຮຽນ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('ວັນທີລົງທະບຽນ')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment_status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'enrolled' => 'ກຳລັງຮຽນ',
                        'transferred' => 'ຍ້າຍໂຮງຮຽນ',
                        'dropped' => 'ອອກກາງຄັນ',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'enrolled' => 'success',
                        'transferred' => 'warning',
                        'dropped' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_new_student')
                    ->label('ນັກຮຽນໃໝ່')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂລ່າສຸດ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('enrollment_status')
                    ->label('ສະຖານະ')
                    ->options([
                        'enrolled' => 'ກຳລັງຮຽນ',
                        'transferred' => 'ຍ້າຍໂຮງຮຽນ',
                        'dropped' => 'ອອກກາງຄັນ',
                    ]),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('ສົກຮຽນ')
                    ->relationship('academicYear', 'year_name'),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('ຫ້ອງຮຽນ')
                    ->relationship('schoolClass', 'class_name'),

                Tables\Filters\TernaryFilter::make('is_new_student')
                    ->label('ນັກຮຽນໃໝ່'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()
                    ->label('ລຶບ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('ລຶບ'),
                ]),
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
            'index' => Pages\ListStudentEnrollments::route('/'),
            'create' => Pages\CreateStudentEnrollment::route('/create'),
            'edit' => Pages\EditStudentEnrollment::route('/{record}/edit'),
        ];
    }
}
