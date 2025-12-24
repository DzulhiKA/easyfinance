<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class YearlyReportExport implements FromCollection, WithHeadings
{
  protected $userId;
  protected $year;

  public function __construct($userId, $year)
  {
    $this->userId = $userId;
    $this->year   = $year;
  }

  public function collection()
  {
    $rows = DB::table('transactions')
      ->selectRaw('
                MONTH(date) as month,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
            ')
      ->where('user_id', $this->userId)
      ->whereYear('date', $this->year)
      ->groupByRaw('MONTH(date)')
      ->orderBy('month')
      ->get();

    return collect(range(1, 12))->map(function ($m) use ($rows) {
      $row = $rows->firstWhere('month', $m);

      $income  = $row->income ?? 0;
      $expense = $row->expense ?? 0;

      return [
        'month'   => $m,
        'income'  => $income,
        'expense' => $expense,
        'balance' => $income - $expense,
      ];
    });
  }

  public function headings(): array
  {
    return [
      'Bulan',
      'Income',
      'Expense',
      'Balance',
    ];
  }
}
