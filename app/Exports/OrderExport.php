<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::with(['user', 'orderItems'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Total Amount',
            'Status',
            'Payment Status',
            'Payment Method',
            'Items Count',
            'Shipping Address',
            'Phone',
            'Created At'
        ];
    }

    /**
     * @param Order $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->id,
            $order->uuid,
            $order->user ? $order->user->name : 'N/A',
            $order->user ? $order->user->email : 'N/A',
            $order->total_amount,
            $order->status,
            $order->payment_status,
            $order->payment_method,
            $order->orderItems->count(),
            $order->shipping_address,
            $order->phone,
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
