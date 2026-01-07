<?php

namespace App\Http\Requests\Dashboard\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'        => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($this->route('role')->id)],
			'permissions' => ['nullable', 'array'],
		];
	}
}
