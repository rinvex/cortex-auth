<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Frontarea;

use Cortex\Foundation\Http\FormRequest;
use Cortex\Foundation\Exceptions\GenericException;

class PhoneVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @throws \Cortex\Foundation\Exceptions\GenericException
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user()
                ?? $this->attemptUser()
                   ?? app('cortex.auth.member')->whereNotNull('phone')->where('phone', $this->get('phone'))->first();

        // Redirect users if their phone already verified, no need to process their request
        if ($user && $user->hasVerifiedPhone()) {
            throw new GenericException(trans('cortex/auth::messages.verification.phone.already_verified'), route('frontarea.cortex.auth.account.settings'));
        }

        // Phone field required before verification
        if ($user && ! $user->phone) {
            throw new GenericException(trans('cortex/auth::messages.account.phone_required'), route('frontarea.cortex.auth.account.settings'));
        }

        // Country field required for phone verification
        if ($user && ! $user->country_code) {
            throw new GenericException(trans('cortex/auth::messages.account.country_required'), route('frontarea.cortex.auth.account.settings'));
        }

        // Email verification required for phone verification
        if ($user && ! $user->hasVerifiedPhone()) {
            throw new GenericException(trans('cortex/auth::messages.account.email_verification_required'), route('frontarea.cortex.auth.account.verification.email.request'));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
