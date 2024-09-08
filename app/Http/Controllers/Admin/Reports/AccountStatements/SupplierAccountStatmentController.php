<?php

namespace App\Http\Controllers\Admin\Reports\AccountStatements;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierAccountStatmentController extends Controller
{
    public function index(Request $request){





        if ($request->ajax()){
            $data = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id' ,
            ]);


            $purchases = DB::table('purchases')
                ->select('purchases_date', 'total','paid')
                ->where('supplier_id',$request->supplier_id)
                ->orderBy('id', 'DESC');

            $headBackPurchases=DB::table('head_back_purchases')
                ->select('purchases_date', 'total','paid')
                ->where('supplier_id',$request->supplier_id)
                ->orderBy('id', 'DESC');

            $vouchers=DB::table('supplier_vouchers')
                ->select('voucher_date', 'paid')
                ->where('supplier_id',$request->supplier_id)
                ->orderBy('id', 'DESC');


            $rows = $purchases
                ->select('purchases_date as date', 'total as total_price','paid as paid')
                ->selectRaw("'purchases' as type")
                ->union($headBackPurchases->select('purchases_date as date', 'total as total_price','paid as paid')   ->selectRaw("'headBackPurchases' as type")
                    ->union($vouchers->select('voucher_date as date','paid as total_price')->selectRaw("0 as paid")->selectRaw("'voucher' as type")))
                ->get();


            $supplier=Supplier::findOrFail($request->supplier_id);
            $html=view('Admin.reports.accountStatement.supplierAccountStatement.parts.table',compact('supplier','rows'))->render();
            return response()->json([
                'status'=>true,
                'html'=>$html,
            ]);
        }
        return view('Admin.reports.accountStatement.supplierAccountStatement.index');
    }
}
