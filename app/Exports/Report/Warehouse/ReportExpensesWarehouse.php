<?php
namespace App\Exports\Report\Warehouse;
use App\Models\Expense;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportExpensesWarehouse implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $expenseQuery = Expense::where('deleted_at', '=', null)
            ->with('expense_category', 'warehouse');

        if ($this->request->filled('warehouse_id')) {
            $expenseQuery->where('warehouse_id', '=', $this->request->input('warehouse_id'));
        }

        if ($this->request->filled('search')) {
            $expenseQuery->where(function ($query) {
                $query->where('Ref', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere('details', 'LIKE', '%' . $this->request->input('search') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('expense_category', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->request->input('search') . '%');
                        });
                    });
            });
        }

        return $expenseQuery;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Details',
            'Amount',
            'Warehouse Name',
            'Category Name',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->date,
            $expense->Ref,
            $expense->details,
            $expense->amount,
            $expense->warehouse->name,
            $expense->expense_category->name,
        ];
    }
}
