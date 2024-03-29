<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Adminarea;

use Illuminate\Support\Arr;
use Rinvex\Support\Traits\Escaper;
use Cortex\Foundation\Http\FormRequest;

class MemberFormRequest extends FormRequest
{
    use Escaper;

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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        $member = $this->route('member') ?? app('cortex.auth.member');
        $country = $data['country_code'] ?? null;
        $twoFactor = $member->getTwoFactor();

        if ($member->exists && empty($data['password'])) {
            unset($data['password'], $data['password_confirmation']);
        }

        // Set abilities
        if (! empty($data['abilities'])) {
            if ($this->user()->can('grant', app('cortex.auth.ability'))) {
                $abilities = array_map('intval', $this->get('abilities', []));
                $data['abilities'] = $this->user()->isA('superadmin') ? $abilities
                    : $this->user()->getAbilities()->pluck('id')->intersect($abilities)->toArray();
            } else {
                unset($data['abilities']);
            }
        }

        // Set roles
        if (! empty($data['roles'])) {
            if ($data['roles'] && $this->user()->can('assign', app('cortex.auth.role'))) {
                $roles = array_map('intval', $this->get('roles', []));
                $data['roles'] = $this->user()->isA('superadmin') ? $roles
                    : $this->user()->roles->pluck('id')->intersect($roles)->toArray();
            } else {
                unset($data['roles']);
            }
        }

        if ($twoFactor && (isset($data['phone_verified_at']) || $country !== $member->country_code)) {
            Arr::set($twoFactor, 'phone.enabled', false);
            $data['two_factor'] = $twoFactor;
        }

        $this->replace($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $member = $this->route('member') ?? app('cortex.auth.member');
        $member->updateRulesUniques();
        $rules = $member->getRules();

        $rules['password'][] = 'confirmed';
        $rules['roles'] = 'nullable|array';
        $rules['abilities'] = 'nullable|array';

        return $rules;
    }
}
