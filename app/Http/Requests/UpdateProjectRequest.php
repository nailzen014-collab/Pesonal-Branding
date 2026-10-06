<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProjectRequest extends StoreProjectRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return array_merge(parent::rules(), [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('projects', 'slug')->ignore($projectId),
            ],
        ]);
    }
}
