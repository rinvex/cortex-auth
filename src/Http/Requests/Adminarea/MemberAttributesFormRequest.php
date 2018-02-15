<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Requests\Adminarea;

use Illuminate\Foundation\Http\FormRequest;

class MemberAttributesFormRequest extends FormRequest
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
        $member = $this->route('member') ?? app('cortex.fort.member');

        // Attach attribute rules
        $member->getEntityAttributes()->each(function ($attribute, $attributeSlug) use (&$rules) {
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