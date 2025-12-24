<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyReportExport implements FromCollection, WithHeadings
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
      ->orderBy('transactions.date')
      ->select(
        'transactions.date',
        'categories.name as category',
        'transactions.type',
        'transactions.amount',
        'transactions.description'
      )
      ->get();
  }

  public function headings(): array
  {
    return [
      'Tanggal',
      'Kategori',
      'Tipe',
      'Jumlah',
      'Deskripsi',
    ];
  }
}
