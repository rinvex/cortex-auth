<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Adminarea;

class PasswordResetSendRequest extends PasswordResetRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', ...config('validation.rules.email'), 'exists:'.config('cortex.auth.models.admin').',email'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectUrl()
    {
        return $this->redirector->getUrlGenerator()->route('adminarea.cortex.auth.account.passwordreset.request');
    }
}
