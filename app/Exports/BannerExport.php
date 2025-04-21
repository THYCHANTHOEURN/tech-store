<?php

namespace App\Exports;

use App\Models\Banner;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BannerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Banner::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Link',
            'Position',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $banner
     * @return array
     */
    public function map($banner): array
    {
        return [
            $banner->id,
            $banner->title,
            $banner->link,
            $this->formatPosition($banner->position),
            $banner->status ? 'Active' : 'Inactive',
            $banner->created_at->format('Y-m-d H:i:s'),
            $banner->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Format the position value for better readability
     *
     * @param string $position
     * @return string
     */
    private function formatPosition(string $position): string
    {
        switch ($position) {
            case 'slider':
                return 'Main Slider';
            case 'side':
                return 'Side Banner';
            case 'promo':
                return 'Promo Banner';
            default:
                return ucfirst($position);
        }
    }
}
