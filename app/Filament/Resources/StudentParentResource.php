<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentParentResource\Pages;
use App\Filament\Resources\StudentParentResource\RelationManagers;
use App\Models\StudentParent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentParentResource extends Resource
{
    protected static ?string $model = StudentParent::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'ຈັດການຂໍ້ມູນນັກຮຽນ';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'ຜູ້ປົກຄອງ';

    protected static ?string $pluralModelLabel = 'ຜູ້ປົກຄອງທັງໝົດ';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('first_name_lao')
                                ->label('ຊື່ (ພາສາລາວ)')
                                ->required(),
                            Forms\Components\TextInput::make('last_name_lao')
                                ->label('ນາມສະກຸນ (ພາສາລາວ)')
                                ->required(),
                            Forms\Components\TextInput::make('first_name_en')
                                ->label('ຊື່ (ພາສາອັງກິດ)')
                                ->required(),
                            Forms\Components\TextInput::make('last_name_en')
                                ->label('ນາມສະກຸນ (ພາສາອັງກິດ)')
                                ->required(),
                        ]),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('gender')
                                ->label('ເພດ')
                                ->options([
                                    'male' => 'ຊາຍ',
                                    'female' => 'ຍິງ'
                                ])
                                ->required(),
                            Forms\Components\DatePicker::make('date_of_birth')
                                ->label('ວັນເດືອນປີເກີດ')
                                ->required(),
                            Forms\Components\TextInput::make('national_id')
                                ->label('ເລກບັດປະຈຳຕົວ')
                                ->required(),
                        ]),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('occupation')
                                ->label('ອາຊີບ')
                                ->required(),
                            Forms\Components\TextInput::make('workplace')
                                ->label('ສະຖານທີ່ເຮັດວຽກ'),
                            Forms\Components\TextInput::make('education_level')
                                ->label('ລະດັບການສຶກສາ'),
                            Forms\Components\TextInput::make('income_level')
                                ->label('ລະດັບລາຍຮັບ'),
                        ]),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->label('ເບີໂທຫຼັກ')
                                ->tel()
                                ->required(),
                            Forms\Components\TextInput::make('alternative_phone')
                                ->label('ເບີໂທສຳຮອງ')
                                ->tel(),
                            Forms\Components\TextInput::make('email')
                                ->label('ອີເມວ')
                                ->email(),
                        ]),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('province_id')
                                ->label('ແຂວງ')
                                ->relationship('province', 'province_name_lao')
                                ->required(),
                            Forms\Components\Select::make('district_id')
                                ->label('ເມືອງ')
                                ->relationship('district', 'district_name_lao')
                                ->required(),
                            Forms\Components\Select::make('village_id')
                                ->label('ບ້ານ')
                                ->relationship('village', 'village_name_lao')
                                ->required(),
                        ]),
                    Forms\Components\Textarea::make('address')
                        ->label('ທີ່ຢູ່')
                        ->required(),
                    Forms\Components\FileUpload::make('profile_image')
                        ->label('ຮູບໂປຣໄຟລ໌')
                        ->image()
                        ->directory('parent-profiles'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\ImageColumn::make('profile_image')
                ->label('ຮູບ'),
            Tables\Columns\TextColumn::make('first_name_lao')
                ->label('ຊື່')
                ->searchable(),
            Tables\Columns\TextColumn::make('last_name_lao')
                ->label('ນາມສະກຸນ')
                ->searchable(),
            Tables\Columns\TextColumn::make('phone')
                ->label('ເບີໂທ')
                ->searchable(),
            Tables\Columns\TextColumn::make('occupation')
                ->label('ອາຊີບ'),
            Tables\Columns\TextColumn::make('village.name_la')
                ->label('ບ້ານ'),
            Tables\Columns\TextColumn::make('district.name_la')
                ->label('ເມືອງ'),
            Tables\Columns\TextColumn::make('province.name_la')
                ->label('ແຂວງ'),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStudentParents::route('/'),
            'create' => Pages\CreateStudentParent::route('/create'),
            'edit' => Pages\EditStudentParent::route('/{record}/edit'),
        ];
    }

    
}
