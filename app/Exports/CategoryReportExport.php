<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryReportExport implements FromCollection, WithHeadings
{
  protected $userId;
  protected $month;
  protected $year;

  public function __construct($userId, $month, $year)
  {
    $this->userId = $userId;
    $this->month  = $month;
    $this->year   = $year;
  }

  public function collection()
  {
    return DB::table('transactions')
      ->join('categories', 'transactions.category_id', '=', 'categories.id')
      ->where('transactions.user_id', $this->userId)
      ->whereMonth('transactions.date', $this->month)
      ->whereYear('transactions.date', $this->year)
      ->groupBy('categories.name', 'transactions.type')
      ->selectRaw('
                categories.name as category,
                transactions.type,
                SUM(transactions.amount) as total
            ')
      ->orderBy('transactions.type')
      ->orderByDesc('total')
      ->get();
  }

  public function headings(): array
  {
    return [
      'Kategori',
      'Tipe',
      'Total',
    ];
  }
}
