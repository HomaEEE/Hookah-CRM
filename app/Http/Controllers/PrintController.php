<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\Shift;

class PrintController extends Controller
{
    public function ReportCashflow()
    {
        $start = $this->filters['start_date'] ?? now()->startOfMonth();
        $end = $this->filters['end_date'] ?? now()->endOfMonth();

        $expenses = Transaction::whereBetween('created_at', [$start, $end])->sum('sum');
        $incomes = Shift::whereBetween('created_at', [$start, $end])->sum('cash');

        $expensesCategory = Transaction::whereBetween('created_at', [$start, $end])->with('supplier')
            ->selectRaw('expense_category_id as id, expense_category_id, sum(sum) as total, count(*) as count')
            ->groupBy('expense_category_id')
            ->orderBy('sum', 'desc');

        return Pdf::loadView('reports.cashflow', compact('start', 'end', 'expenses', 'incomes', 'expensesCategory'))->download('report-'.$start.'.pdf');
    }

    public function PreviewCashflow()
    {
        $start = $this->filters['start_date'] ?? now()->startOfMonth();
        $end = $this->filters['end_date'] ?? now()->endOfMonth();

        $expenses = Expense::whereBetween('created_at', [$start, $end])->sum('sum');
        $incomes = Shift::whereBetween('created_at', [$start, $end])->sum('cash');

        $expensesCategory = Expense::whereBetween('created_at', [$start, $end])->with('supplier')
            ->selectRaw('category_id as id, category_id, sum(sum) as total, count(*) as count')
            ->groupBy('category_id')
            ->orderBy('sum', 'desc');

        return view('reports.cashflow', compact('start', 'end', 'expenses', 'incomes', 'expensesCategory'));
    }
}
