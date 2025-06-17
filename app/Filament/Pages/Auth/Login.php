<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use \Filament\Http\Responses\Auth\Contracts\LoginResponse;

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
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath("data")
            ),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(('ອີເມວ ຫຼື ຊື່ຜູ້ໃຊ້'))
            ->placeholder(('ກະລຸນາປ້ອນອີເມວ ຫຼື ຊື່ຜູ້ໃຊ້'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(('ລະຫັດຜ່ານ'))
            ->placeholder(('ກະລຸນາປ້ອນລະຫັດຜ່ານ'))
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
        $data = $this->form->getState();

        if (!auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = auth()->user();

        // ກວດສອບສະຖານະຜູ້ໃຊ້
        if ($user->status !== 'active') {
            auth()->logout();
            throw ValidationException::withMessages([
                'data.login' => __('ບັນຊີຂອງທ່ານຖືກປິດໃຊ້ງານ'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}