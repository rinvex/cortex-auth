<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Frontarea;

use Cortex\Foundation\Http\FormRequest;

class AuthenticationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'loginfield' => 'required|min:3|max:128',
            'password' => ['required', config('validation.rules.password')],
        ];
    }
}
