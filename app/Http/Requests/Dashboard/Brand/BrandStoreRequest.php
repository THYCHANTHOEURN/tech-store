<?php

namespace App\Http\Requests\Dashboard\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandStoreRequest extends FormRequest
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
			'logo'          => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
		];
	}
}
