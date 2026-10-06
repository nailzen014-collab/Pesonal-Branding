<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateSkillRequest extends StoreSkillRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:100', Rule::unique('skills', 'name')->ignore($this->route('skill')?->id)],
        ]);
    }
}
