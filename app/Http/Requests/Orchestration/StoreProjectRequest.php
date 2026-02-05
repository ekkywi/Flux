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
            'name'                              => 'required|string|max:255',
            'repository_url'                    => 'required|url',
            'stack_type'                        => 'required|string|in:laravel,nodejs',
            'stack_options.php_version'         => 'required_if:stack_type,laravel',
            'stack_options.framework_version'   => 'nullable',
            'description'                       => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'stack_options.php_version' => 'PHP Version',
        ];
    }
}
