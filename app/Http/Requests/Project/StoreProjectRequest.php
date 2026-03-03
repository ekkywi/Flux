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
            'stack'             => ['required', 'string', 'in:laravel,nodejs,html'],
            'php_version'       => ['nullable', 'string', 'in:8.1,8.2,8.3,8.4'],
            'database_type'     => ['required', 'string', 'in:sqlite,mysql,pgsql'],
        ];
    }

    public function messages(): array
    {
        return [
            'repository_url.url'    => 'Repository link must be valid.',
            'branch.requireq'       => 'Please select a target branch for deployment.',
            'stack.required'        => 'Please select a project stack.'
        ];
    }
}
