<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'      => ['required', 'string', 'max:255'],
			'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password'  => ['required', 'string', 'min:8'],
			'phone'     => ['nullable', 'string', 'max:20'],
			'address'   => ['nullable', 'string'],
			'role'      => ['required', 'exists:roles,name'],
		];
	}
}
