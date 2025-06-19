<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'ຈັດການຜູ້ໃຊ້';

    protected static ?string $navigationLabel = 'ບົດບາດຜູ້ໃຊ້ງານ';

    protected static ?string $modelLabel = 'ບົດບາດຜູ້ໃຊ້ງານ';

    protected static ?string $pluralModelLabel = 'ບົດບາດຜູ້ໃຊ້ງານ';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'role_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('role_name')
                    ->label('ຊື່ບົດບາດ')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->label('ຄຳອະທິບາຍໜ້າທີ່ຂອງບົດບາດ')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                Section::make('ສິດທິການເຂົ້າເຖິງ')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('ສິດທິ')
                            ->relationship('permissions', 'permission_name')
                            ->searchable()
                            ->columns(2)
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->options(function () {
                                return \App\Models\Permission::pluck('description', 'permission_id')->toArray();
                            })
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Textcolumn::make('role_id')
                    ->label('ລະຫັດ')
                    ->searchable(),
                Textcolumn::make('role_name')
                    ->label('ຊື່ບົດບາດ')
                    ->searchable()
                    ->sortable(),
                    Textcolumn::make('description')
                    ->label('ຄຳອະທິບາຍໜ້າທີ່ຂອງບົດບາດ')
                    ->searchable(),
                    Textcolumn::make('permissions_count')
                    ->label('ຈຳນວນສິດທິ')
                    ->counts('permissions'),
                    Textcolumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                    Textcolumn::make('updated_at')
                    ->label('ອັບເດດລ່າສຸດ')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

