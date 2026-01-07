<?php

namespace App\Http\Requests\Dashboard\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

class OrderStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'user_id'               => 'required|exists:users,id',
			'shipping_address'      => 'required|string',
			'phone'                 => 'required|string|max:20',
			'payment_method'        => 'required|string',
			'status'                => 'required|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
			'payment_status'        => 'required|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
			'items'                 => 'required|array|min:1',
			'items.*.product_id'    => 'required|exists:products,id',
			'items.*.quantity'      => 'required|integer|min:1',
			'items.*.price'         => 'required|numeric|min:0',
		];
	}
}
