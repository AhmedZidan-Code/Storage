<?php

namespace App\Http\Controllers\Admin\Reports\AccountStatements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerAccountStatementController extends Controller
{
    private float $balanace = 0;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->validate([
                'client_id' => 'nullable|exists:clients,id',
                'payment_month' => 'nullable|between:1,12',
                'client_payment_setting_id' => 'nullable|exists:client_payment_settings,id',
            ]);

            $query = $this->getUnionQuery($data['client_id'] ?? null, $data['payment_month'] ?? null, $data['client_payment_setting_id'] ?? null);

            return DataTables::of($query)
                ->addColumn('type_label', function ($row) {
                    return $this->getTypeLabel($row->type);
                })
                ->addColumn('balance', function ($row) {
                    return $this->balanace = $this->balanace + floatval($row->debt - $row->credit);
                })
                ->editColumn('date', function ($row) {
                    return $row->date;
                })
                ->make(true);
        }

        return view('Admin.reports.accountStatement.clientAccountStatement.index');
    }

    private function getUnionQuery($clientId = null, $payment_month = null, $client_payment_setting_id = null)
    {
        $sales = DB::table('sales')
            ->select(
                'sales_date as date',
                DB::raw('0 as credit'),
                'paid',
                DB::raw("'sales' as type"),
                'total as debt',
                'total as total_price',
                DB::raw('0 as client_payment_setting_id')
            )
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->when($payment_month, function ($query, $payment_month) {
                return $query->where('month', $payment_month);
            });

        $headBackSales = DB::table('head_back_sales')
            ->select(
                'sales_date as date',
                'total as credit',
                'paid',
                DB::raw("'headBackSales' as type"),
                DB::raw('0 as debt'),
                'total as total_price',
                DB::raw('0 as client_payment_setting_id')
            )
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->when($payment_month, function ($query, $payment_month) {
                return $query->where('month', $payment_month);
            });

        $esalat = DB::table('esalats')
            ->select(
                'date_esal as date',
                'paid as credit',
                'paid',
                DB::raw("'esalat' as type"),
                DB::raw('0 as debt'),
                'paid as total_price',
                'client_payment_setting_id'
            )
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->when($payment_month, function ($query, $payment_month) {
                return $query->where('month', $payment_month);
            })
            ->when($client_payment_setting_id, function ($query, $client_payment_setting_id) {
                return $query->where('client_payment_setting_id', $client_payment_setting_id);
            });

        return $sales->unionAll($headBackSales)->unionAll($esalat)->orderBy('date', 'DESC');
    }

    private function getTypeLabel($type)
    {
        $labels = [
            'sales' => 'المبيعات',
            'headBackSales' => 'مرتجعات المبيعات',
            'esalat' => 'إيصالات السداد',
        ];

        return $labels[$type] ?? $type;
    }
}
