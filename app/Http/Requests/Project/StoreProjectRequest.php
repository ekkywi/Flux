<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'repository_url'    => ['required', 'url'],
            'branch'            => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'repository_url.url'    => 'Repository link must be valid.',
            'branch.requireq'       => 'Please select a target branch for deployment.'
        ];
    }
}
