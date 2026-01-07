<?php

namespace App\Http\Requests\Dashboard\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'      => ['required', 'string', 'max:255'],
			'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('customer')->id)],
			'password'  => ['nullable', 'string', 'min:8'],
			'phone'     => ['nullable', 'string', 'max:20'],
			'address'   => ['nullable', 'string'],
		];
	}
}
