<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Adminarea;

class AbilityFormProcessRequest extends AbilityFormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Set roles
        if (! empty($data['roles'])) {
            if ($data['roles'] && $this->user()->can('grant', app('cortex.auth.ability'))) {
                $roles = array_map('intval', $this->get('roles', []));
                $data['roles'] = $this->user()->isA('superadmin') ? $roles
                    : $this->user()->roles->pluck('id')->intersect($roles)->toArray();
            } else {
                unset($data['roles']);
            }
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
        $user = $this->route('ability') ?? app('cortex.auth.ability');
        $user->updateRulesUniques();
        $rules = $user->getRules();
        $rules['roles'] = 'nullable|array';

        return $rules;
    }
}
