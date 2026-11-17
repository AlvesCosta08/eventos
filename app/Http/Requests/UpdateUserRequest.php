<?php

namespace App\Http\Requests;

use App\Domain\Users\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuarioAutenticado = $this->user();
        $usuarioEditado = $this->route('user');

        if (!$usuarioAutenticado || !$usuarioEditado instanceof User) {
            return false;
        }

        return $usuarioAutenticado->can('update', $usuarioEditado);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
            ],

            'role' => [
                'required',
                Rule::enum(UserRole::class),
            ],

            'congregacao_id' => [
                'nullable',
                'exists:congregacoes,id',
                Rule::requiredIf(
                    fn () => $this->input('role') === UserRole::LiderCongregacao->value
                ),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('role') === UserRole::AdminGeral->value) {
            $this->merge([
                'congregacao_id' => null,
            ]);
        }
    }
}
