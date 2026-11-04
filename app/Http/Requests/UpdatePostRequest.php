<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePostRequest extends StorePostRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $postId = $this->route('post')?->id;

        return array_merge(parent::rules(), [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
        ]);
    }
}
