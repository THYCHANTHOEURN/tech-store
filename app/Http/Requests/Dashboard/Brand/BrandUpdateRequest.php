<?php

namespace App\Http\Requests\Dashboard\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'          => ['required', 'string', 'max:255'],
			'description'   => ['nullable', 'string'],
			'status'        => ['required', 'boolean'],
			'logo'          => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
		];
	}
}
