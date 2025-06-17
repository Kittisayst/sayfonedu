<?php

namespace App\Filament\Auth;

use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use \Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Forms\Components\Button;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return __('ເຂົ້າສູ່ລະບົບ');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('ຍິນດີຕ້ອນຮັບເຂົ້າສູ່ລະບົບ Sayfone School');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make()
                            ->schema([
                                $this->getLoginFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getRememberFormComponent(),
                            ])
                            ->columns(1),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('ອີເມວ ຫຼື ຊື່ຜູ້ໃຊ້'))
            ->placeholder(__('ກະລຸນາປ້ອນອີເມວ ຫຼື ຊື່ຜູ້ໃຊ້'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('ລະຫັດຜ່ານ'))
            ->placeholder(__('ກະລຸນາປ້ອນລະຫັດຜ່ານ'))
            ->password()
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('ຈົດຈຳການເຂົ້າສູ່ລະບົບ'))
            ->extraInputAttributes(['tabindex' => 3]);
    }

    public function mount(): void
    {
        parent::mount();

        // ກວດສອບຂໍ້ຄວາມຜິດພາດຈາກ session
        if (Session::has('login_error')) {
            $errorMessage = Session::get('login_error');
            Session::forget('login_error');
            
            // ສະແດງຂໍ້ຄວາມຜິດພາດຜ່ານ Notification
            Notification::make()
                ->title(__('ຂໍ້ຄວາມຜິດພາດ'))
                ->body($errorMessage)
                ->danger()
                ->send();
                
            // ສະແດງຂໍ້ຄວາມຜິດພາດໃນຟອມ
            $this->form->addError('login', $errorMessage);
        }

        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getCurrentPanel()->getUrl());
        }

        if (Session::has('alert')) {
            $alert = Session::get('alert');
            Notification::make()
                ->title($alert['title'])
                ->body($alert['message'])
                ->{$alert['type']}()
                ->send();
        }

        $this->form->fill();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('ກະລຸນາກວດສອບອີເມວ/ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານຂອງທ່ານ'),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $credentials = $this->getCredentialsFromFormData($this->form->getState());
        $remember = $this->form->getState()['remember'] ?? false;

        if (!Auth::attempt($credentials, $remember)) {
            $this->throwFailureValidationException();
        }

        $user = Auth::user();
        
        // ກວດສອບສິດ
        $allowedRoles = config('roles.access_panels', ['admin', 'school_admin', 'finance_staff']);
        
        if (!$user->role || !in_array($user->role->role_name, $allowedRoles)) {
            Auth::logout();
            
            // ສະແດງຂໍ້ຄວາມຜິດພາດຜ່ານ Notification
            Notification::make()
                ->title('ບໍ່ມີສິດເຂົ້າໃຊ້')
                ->body('ທ່ານບໍ່ມີສິດໃນການເຂົ້າໃຊ້ລະບົບ.')
                ->danger()
                ->persistent()
                ->send();
                
            // ສະແດງຂໍ້ຄວາມຜິດພາດໃນຟອມ
            $this->form->addError('login', 'ທ່ານບໍ່ມີສິດໃນການເຂົ້າໃຊ້ລະບົບ.');
            
            return null;
        }

        // ສົ່ງຄຳຕອບການເຂົ້າສູ່ລະບົບທີ່ຖືກຕ້ອງ
        return app(CustomLogin::class);
    }
}
