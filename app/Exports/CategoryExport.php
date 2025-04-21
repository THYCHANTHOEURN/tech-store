<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Category::with(['parent'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Parent Category',
            'Status',
            'Created At',
            'Description'
        ];
    }

    /**
     * @param Category $category
     * @return array
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->name,
            $category->slug,
            $category->parent ? $category->parent->name : 'Root Category',
            $category->status ? 'Active' : 'Inactive',
            $category->created_at->format('Y-m-d H:i:s'),
            $category->description ?? '',
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
