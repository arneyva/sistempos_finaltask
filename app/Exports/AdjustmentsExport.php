<?php

namespace App\Exports;

use App\Models\Adjustment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdjustmentsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        // Inisialisasi query dengan menggunakan model Adjustment
        $adjustmentQuery = Adjustment::with('warehouse')->where('deleted_at', '=', null);

        // Terapkan filter berdasarkan parameter yang diterima dari request
        if ($this->request->has('date') && $this->request->filled('date')) {
            $adjustmentQuery->whereDate('date', '=', $this->request->input('date'));
        }

        if ($this->request->has('Ref') && $this->request->filled('Ref')) {
            $adjustmentQuery->where('Ref', 'like', '%'.$this->request->input('Ref').'%');
        }

        if ($this->request->has('warehouse_id') && $this->request->filled('warehouse_id')) {
            $adjustmentQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        // Lakukan sorting sesuai request jika diperlukan
        if ($this->request->has('SortField') && $this->request->has('SortType')) {
            $sortField = $this->request->input('SortField');
            $sortType = $this->request->input('SortType');
            $adjustmentQuery->orderBy($sortField, $sortType);
        }

        // Lakukan pencarian jika diperlukan
        if ($this->request->has('search') && $this->request->filled('search')) {
            $search = $this->request->input('search');
            $adjustmentQuery->where('Ref', 'like', '%'.$search.'%');
        }

        return $adjustmentQuery;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Added By',
            'Date',
            'Reference',
            'Warehouse Name',
            'Total Items',
            'Notes',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($adjustment): array
    {
        return [
            $adjustment->id,
            $adjustment->user->firstname,
            $adjustment->date,
            $adjustment->Ref,
            $adjustment->warehouse->name ?? 'deleted',
            $adjustment->items,
            $adjustment->notes,
            $adjustment->created_at ?? 'null',
            $adjustment->updated_at ?? 'null',
            $adjustment->deleted_at ?? 'null',
        ];
    }
}
