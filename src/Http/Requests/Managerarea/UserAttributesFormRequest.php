<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Requests\Managerarea;

use Illuminate\Foundation\Http\FormRequest;

class UserAttributesFormRequest extends FormRequest
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
        $user = $this->route('user') ?? app('rinvex.fort.user');

        // Attach attribute rules
        $user->getEntityAttributes()->each(function ($attribute, $attributeSlug) use (&$rules) {
            switch ($attribute->type) {
                case 'datetime':
                    $type = 'date';
                    break;
                case 'text':
                case 'varchar':
                    $type = 'string';
                    break;
                default:
                    $type = $attribute->type;
                    break;
            }

            $rule = ($attribute->is_required ? 'required|' : 'nullable|').$type;
            $rules[$attributeSlug.($attribute->is_collection ? '.*' : '')] = $rule;
        });

        return $rules ?? [];
    }
}
