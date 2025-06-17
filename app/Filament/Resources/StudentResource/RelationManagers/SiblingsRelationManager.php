<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Student;
use App\Models\StudentSibling;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Http\Get;

class SiblingsRelationManager extends RelationManager
{
    protected static string $relationship = 'siblings';
    protected static ?string $title = 'ພີ່ນ້ອງ';
    protected static ?string $recordTitleAttribute = 'first_name_lao';
    protected static ?string $icon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'ພີ່ນ້ອງ';
    protected static ?string $pluralModelLabel = 'ພີ່ນ້ອງ';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sibling_student_id')
                    ->label('ພີ່ນ້ອງ')
                    ->options(function () {
                        $currentStudentId = $this->getOwnerRecord()->student_id;
                        
                        $query = Student::where('student_id', '!=', $currentStudentId);
                        
                        if ($record = $this->getMountedTableActionRecord()) {
                            $query->orWhere('student_id', $record->sibling_student_id);
                        }
                        
                        return $query->get()
                            ->mapWithKeys(function ($student) {
                                if (!$student->student_code || !$student->first_name_lao || !$student->last_name_lao) {
                                    return [];
                                }
                                return [$student->student_id => "{$student->first_name_lao} {$student->last_name_lao} ({$student->student_code})"];
                            });
                    })
                    ->required()
                    ->searchable(['first_name_lao', 'last_name_lao', 'student_code']),

                Forms\Components\Select::make('relationship')
                    ->label('ຄວາມສຳພັນ')
                    ->options([
                        'brother' => 'ອ້າຍ/ນ້ອງຊາຍ',
                        'sister' => 'ເອື້ອຍ/ນ້ອງສາວ',
                        'step_brother' => 'ອ້າຍ/ນ້ອງຊາຍລ້ຽງ',
                        'step_sister' => 'ເອື້ອຍ/ນ້ອງສາວລ້ຽງ',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name_lao')
            ->columns([
                Tables\Columns\TextColumn::make('student_code')
                    ->label('ລະຫັດນັກຮຽນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name_lao')
                    ->label('ຊື່ນັກຮຽນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name_lao')
                    ->label('ນາມສະກຸນ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('relationship')
                    ->label('ຄວາມສຳພັນ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'brother' => 'ອ້າຍ/ນ້ອງຊາຍ',
                        'sister' => 'ເອື້ອຍ/ນ້ອງສາວ',
                        'step_brother' => 'ອ້າຍ/ນ້ອງຊາຍລ້ຽງ',
                        'step_sister' => 'ເອື້ອຍ/ນ້ອງສາວລ້ຽງ',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data): StudentSibling {
                        $ownerRecord = $this->getOwnerRecord();

                        return StudentSibling::create([
                            'student_id' => $ownerRecord->student_id,
                            'sibling_student_id' => $data['sibling_student_id'],
                            'relationship' => $data['relationship'],
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ແກ້ໄຂ'),
                Tables\Actions\DeleteAction::make()->label('ລຶບ'),
            ]);
    }
}
