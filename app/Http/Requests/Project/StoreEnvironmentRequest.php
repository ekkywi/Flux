<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnvironmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('update', $project);
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:50'],
            'type'      => ['required', Rule::in(['development', 'staging', 'production'])],
            'branch'    => ['required', 'string', 'max:255'],
            'server_id' => ['required', 'exists:servers,id'],
        ];
    }
}
