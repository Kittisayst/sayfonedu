<?php
// app/Filament/Components/UserProfile.php
namespace App\Filament\Components;

use Filament\Forms\Components\Component;
use Filament\Forms;

class UserProfile extends Component
{
    public static function make(): Forms\Components\Section
    {
        return Forms\Components\Section::make('ຂໍ້ມູນໂປຣໄຟລ໌')
            ->schema([
                Forms\Components\FileUpload::make('profile_image')
                    ->label('ຮູບໂປຣໄຟລ໌')
                    ->image()
                    ->disk('public')
                    ->directory('profile-images')
                    ->circular()
                    ->imageEditor()
                    ->columnSpanFull(),

                // Components ອື່ນໆທີ່ກ່ຽວຂ້ອງກັບໂປຣໄຟລ໌
                Forms\Components\TextInput::make('username')
                    ->label('ຊື່ຜູ້ໃຊ້')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('ອີເມລ')
                    ->email()
                    ->required(),
            ])
            ->columns(2);
    }
}
