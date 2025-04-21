<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Provide a sample row with empty values
        return new Collection([
            [
                'Example Product', // name
                'SKU123', // sku (optional)
                'Electronics', // category
                'Apple', // brand
                '999.99', // price
                '899.99', // sale_price (optional)
                '100', // stock
                'Published', // status (Published/Unpublished)
                'Yes', // featured (Yes/No)
                'This is a sample product description.' // description (optional)
            ]
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'name',
            'sku',
            'category',
            'brand',
            'price',
            'sale_price',
            'stock',
            'status',
            'featured',
            'description'
        ];
    }

    /**
     * Apply styles to worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Apply bold styling to header row
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
