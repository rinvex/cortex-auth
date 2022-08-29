<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Managerarea;

class EmailVerificationSendRequest extends EmailVerificationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns|min:3|max:128|exists:'.config('cortex.auth.models.manager').',email',
        ];
    }
}
