<?php

namespace App\Http\Requests\Orchestration;

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
            'name'              => 'required|string|max:255',
            'repository_url'    => 'required|url',
            'stack_type'        => 'required|string|in:laravel,nodejs',
            'php_version'       => 'required_if:stack_type,laravel|string',
            'framework_version' => 'required_if:stack_type,laravel|string',
            'description'       => 'nullable|string',
        ];
    }
}
