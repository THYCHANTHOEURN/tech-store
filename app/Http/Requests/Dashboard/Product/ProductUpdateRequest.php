<?php

namespace App\Http\Requests\Dashboard\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'              => 'required|string|max:255',
			'category_id'       => 'required|exists:categories,id',
			'brand_id'          => 'required|exists:brands,id',
			'price'             => 'required|numeric|min:0',
			'sale_price'        => 'nullable|numeric|min:0|lt:price',
			'stock'             => 'required|integer|min:0',
			'description'       => 'nullable|string',
			'status'            => 'required|boolean',
			'featured'          => 'required|boolean',
			'images'            => 'sometimes|array',
			'images.*'          => 'image|mimes:jpeg,png,jpg|max:2048',
			'remove_images'     => 'sometimes|array',
			'remove_images.*'   => 'exists:product_images,id',
			'primary_image'     => 'sometimes|nullable|exists:product_images,id',
		];
	}
}
