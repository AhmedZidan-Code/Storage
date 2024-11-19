<?php

namespace App\Http\Controllers\Admin\Reports\Productive;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductiveMovementController extends Controller
{
    protected $total = 0;
    public function index(Request $request)
    {
        $startDate = $request->input('fromDate') ? Carbon::parse($request->input('fromDate'))->startOfDay() : null;
        $endDate = $request->input('toDate') ? Carbon::parse($request->input('toDate'))->endOfDay() : null;
        $storage = $request->input('storage_id');
        $productive_id = $request->input('product_id');

        if ($request->ajax()) {
            $buildQuery = function ($tableName, $dateColumn, $type, $process) use ($startDate, $endDate, $storage, $productive_id) {
                return DB::table($tableName)->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
                    ->when($storage, fn($q) => $q->where('storage_id', $storage))
                    ->selectRaw('SUM(amount) as total_amount, DATE(' . $dateColumn . ') as date, ? as type , ? as process', [$type, $process])
                    ->when($startDate, fn($q) => $q->whereDate($dateColumn, '<=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate($dateColumn, '>=', $endDate))
                    ->groupBy(DB::raw('DATE(' . $dateColumn . ')'));

            };

            // Create the initial query and union all other queries
            $query = $buildQuery('rasied_ayni', 'created_at', 'رصيد عيني', 1)
            // ->unionAll($buildQuery('sales_details', 'date', 'مبيعات', 2))
                ->unionAll(DB::table('sales_details')->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
                        ->when($storage, fn($q) => $q->where('storage_id', $storage))
                        ->where('is_prepared', 1)
                        ->selectRaw('SUM(amount) as total_amount, DATE(date) as date, ? as type , ? as process', ['مبيعات', 2])
                        ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
                        ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
                        ->groupBy(DB::raw('DATE(date)'))
                )
                ->unionAll($buildQuery('purchases_details', 'date', 'مشتريات', 3))
                ->unionAll($buildQuery('head_back_sales_details', 'date', 'مرتجع مبيعات', 4))
                ->unionAll($buildQuery('head_back_purchases_details', 'date', 'مرتجع مشتريات', 5))
                ->unionAll($buildQuery('destruction_details', 'date', 'اهلاك', 6))
                ->unionAll(
                    DB::table('product_adjustments')
                        ->when($productive_id, fn($q) => $q->where('product_id', $productive_id))
                        ->when($storage, fn($q) => $q->where('storage_id', $storage))
                        ->selectRaw('SUM(CASE WHEN type = 2 THEN -amount ELSE amount END) as total_amount, DATE(date) as date, ? as type, ? as process', ['تسوية', 7])
                        ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
                        ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
                        ->groupBy(DB::raw('DATE(date)'))
                );
            // Order the final result
            $query->orderBy('date', 'ASC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('total', function ($row) {
                    if ($row->process == 1) {
                        $this->total += $row->total_amount;
                    } elseif ($row->process == 2) {
                        $this->total -= $row->total_amount;
                    } elseif ($row->process == 3) {
                        $this->total += $row->total_amount;
                    } elseif ($row->process == 4) {
                        $this->total += $row->total_amount;
                    } elseif ($row->process == 5) {
                        $this->total -= $row->total_amount;
                    } elseif ($row->process == 6) {
                        $this->total -= $row->total_amount;
                    } elseif ($row->process == 7) {
                        $this->total += $row->total_amount;
                    }
                    return $this->total;

                })
                ->escapeColumns([])
                ->make(true);
        }
        return view('Admin.reports.productive.index', $this->statistics($productive_id, $storage, $startDate, $endDate));
    }

    public function statistics($productive_id = null, $storage = null, $startDate = null, $endDate = null)
    {
        $rasied_ayni = DB::table('rasied_ayni')
            ->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '>=', $endDate))
            ->sum('amount');
        $sales_details = DB::table('sales_details')
            ->where('is_prepared', 1)
            ->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
            ->sum('amount');
        $purchases_details = DB::table('purchases_details')
            ->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
            ->sum('amount');
        $head_back_sales_details = DB::table('head_back_sales_details')
            ->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
            ->sum('amount');
        $head_back_purchases_details = DB::table('head_back_purchases_details')
            ->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
            ->sum('amount');
        $destruction_details = DB::table('destruction_details')
            ->when($productive_id, fn($q) => $q->where('productive_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
            ->sum('amount');
        $product_adjustment = DB::table('product_adjustments')
            ->when($productive_id, fn($q) => $q->where('product_id', $productive_id))
            ->when($storage, fn($q) => $q->where('storage_id', $storage))
            ->when($startDate, fn($q) => $q->whereDate('date', '<=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '>=', $endDate))
            ->sum(DB::raw('CASE WHEN type = 2 THEN -amount ELSE amount END'));

        return [
            'sales' => $sales_details,
            'purchases' => $purchases_details,
            'hadback_sales' => $head_back_sales_details,
            'hadback_purchases' => $head_back_purchases_details,
            'rasied_ayni' => $rasied_ayni,
            'destruction' => $destruction_details,
            'product_adjustment' => $product_adjustment,
        ];
    }

}
