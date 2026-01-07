<?php

namespace App\Http\Requests\Dashboard\Banner;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Banner;

class BannerUpdateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'title'     => ['required', 'string', 'max:255'],
			'link'      => ['required', 'string', 'max:255'],
			'position'  => ['required', 'string', 'in:' . Banner::POSITION_SLIDER . ',' . Banner::POSITION_SIDE . ',' . Banner::POSITION_PROMO],
			'status'    => ['required', 'boolean'],
			'image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
		];
	}
}
