<?php

namespace App\Http\Controllers\Admin\Reports\AccountStatements;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAccountStatementController extends Controller
{
    //
    public function index(Request $request){





        if ($request->ajax()){
            $data = $request->validate([
                'client_id' => 'required|exists:clients,id' ,
            ]);


            $sales = DB::table('sales')
                ->select('sales_date', 'total','paid')
                ->where('client_id',$request->client_id)
                ->orderBy('id', 'DESC');

            $headBackSales=DB::table('head_back_sales')
                ->select('sales_date', 'total','paid')
                ->where('client_id',$request->client_id)
                ->orderBy('id', 'DESC');

            $esalat=DB::table('esalats')
                ->select('date_esal', 'paid')
                ->where('client_id',$request->client_id)
                ->orderBy('id', 'DESC');


            $rows = $sales
                ->select('sales_date as date', 'total as total_price','paid as paid')
                ->selectRaw("'sales' as type")
                ->union($headBackSales->select('sales_date as date', 'total as total_price','paid as paid')   ->selectRaw("'headBackSales' as type")
                    ->union($esalat->select('date_esal as date','paid as total_price')->selectRaw("0 as paid")->selectRaw("'esalat' as type")))
                ->get();


            $client=Client::findOrFail($request->client_id);
            $html=view('Admin.reports.accountStatement.clientAccountStatement.parts.table',compact('client','rows'))->render();
            return response()->json([
                'status'=>true,
                'html'=>$html,
            ]);
        }
        return view('Admin.reports.accountStatement.clientAccountStatement.index');
    }
}
