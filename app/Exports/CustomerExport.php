<?php

namespace App\Exports;

use App\Models\User;
use App\Enums\RolesEnum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::role(RolesEnum::CUSTOMER->value)
            ->with(['orders'])
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Email Verified',
            'Phone',
            'Address',
            'Total Orders',
            'Total Spent',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $customer
     * @return array
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->email_verified_at ? 'Yes' : 'No',
            $customer->phone ?? 'N/A',
            $customer->address ?? 'N/A',
            $customer->orders->count(),
            $customer->orders->where('status', 'completed')->sum('total_amount'),
            $customer->created_at->format('Y-m-d H:i:s'),
            $customer->updated_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
