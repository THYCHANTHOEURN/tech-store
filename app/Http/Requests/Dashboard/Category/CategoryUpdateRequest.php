<?php

namespace App\Http\Requests\Dashboard\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'          => 'required|string|max:255',
			'parent_id'     => 'nullable|exists:categories,id',
			'description'   => 'nullable|string',
			'status'        => 'required|boolean',
			'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
		];
	}
}
