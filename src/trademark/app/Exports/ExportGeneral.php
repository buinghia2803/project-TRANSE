<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportGeneral implements FromArray, WithHeadings, WithColumnFormatting, WithColumnWidths
{
    use Exportable;

    /**
     * @return  array $data
     */
    protected array $data;

    /**
     * @param   array $data
     * @return  void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return  array
     */
    public function headings(): array
    {
        return $this->data['fields'] ?? [];
    }

    /**
     * @return  array
     */
    public function array(): array
    {
        return $this->data['data'] ?? [];
    }

    /**
     * @return  array
     */
    public function columnWidths(): array
    {
        return $this->data['column_widths'] ?? [];
    }

    /**
     * @return  array
     */
    public function columnFormats(): array
    {
        return $this->data['column_formats'] ?? [];
    }
}
