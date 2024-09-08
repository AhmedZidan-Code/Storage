<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeadBackPurchases;
use App\Models\HeadBackPurchasesDetails;
use App\Models\Productive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class HeadBackPurchasesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rows = HeadBackPurchases::query()->with(['storage','supplier']);
            return DataTables::of( $rows)
                ->addColumn('action', function ($row) {

                    $edit='';
                    $delete='';


                    return '

                           <button '.$edit.'   class="editBtn-p btn rounded-pill btn-primary waves-effect waves-light"
                                    data-id="' . $row->id . '"
                            <span class="svg-icon svg-icon-3">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-edit"></i>
                                </span>
                            </span>
                            </button>
                            <button '.$delete.'  class="btn rounded-pill btn-danger waves-effect waves-light delete"
                                    data-id="' . $row->id . '">
                            <span class="svg-icon svg-icon-3">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-trash"></i>
                                </span>
                            </span>
                            </button>
                       ';



                })

                ->addColumn('details', function ($row) {
                    return "<button data-id='$row->id' class='btn btn-outline-dark showDetails'>عرض تفاصيل الطلب</button>";
                })

                ->editColumn('created_at', function ($admin) {
                    return date('Y/m/d', strtotime($admin->created_at));
                })
                ->escapeColumns([])
                ->make(true);


        }

        return view('Admin.CRUDS.headBackPurchases.index');
    }


    public function create()
    {
        $model=DB::table('head_back_purchases')->latest('id')->select('id')->first();
        if ($model)
            $count=$model->id;
        else
            $count=0;
        return view('Admin.CRUDS.headBackPurchases.create',compact('count'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id' ,
            'purchases_date'=>'required|date',
            'pay_method'=>'required|in:debit,cash',
            'supplier_id'=>'required|exists:suppliers,id',
            'supplier_fatora_number'=>'required|unique:head_back_purchases,supplier_fatora_number',
            'fatora_number'=>'required|unique:head_back_purchases,fatora_number',

        ]);


        $datails = $request->validate([
            'productive_id'=>'required|array',
            'productive_id.*'=>'required',
            'amount'=>'required|array',
            'amount.*'=>'required',
            'productive_buy_price'=>'required|array',
            'productive_buy_price.*'=>'required',

        ]);

        if (count($request->amount) != count($request->productive_id) )
            return response()->json(
                [
                    'code' => 421,
                    'message' => 'المنتج مطلوب'
                ]);
        $purchases_number=1;
        $latestModel=DB::table('head_back_purchases')->latest('id')->select('id')->first();
        if ($latestModel)
            $purchases_number=$latestModel->id+1;


        $data['publisher']=auth('admin')->user()->id;
        $data['purchases_number']=$purchases_number;
        $data['date']=date('Y-m-d');
        $data['month']=date('m');
        $data['year']=date('Y');

        $headBackPurchases= HeadBackPurchases::create($data);



        $sql=[];

        if ($request->productive_id ) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [

                    'head_back_purchases_id' => $headBackPurchases->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount'=>$request->amount[$i],
                    'productive_buy_price'=>$request->productive_buy_price[$i],
                    'total'=>$request->productive_buy_price[$i]*$request->amount[$i],
                    'all_pieces'=>$request->amount[$i]*$productive->num_pieces_in_package,
                    'date' => date('Y-m-d'),
                    'year' => date('Y'),
                    'month' => date('m'),
                    'publisher' => auth('admin')->user()->id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),

                ];

                array_push($sql,$details);
            }
            DB::table('head_back_purchases_details')->insert($sql);

            $headBackPurchases->update([
                'total'=>HeadBackPurchasesDetails::where('head_back_purchases_id',$headBackPurchases->id)->sum('total'),
            ]);

        }



        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);
    }



    public function edit(  $id)
    {



        $row=HeadBackPurchases::find($id);

        return view('Admin.CRUDS.headBackPurchases.edit', compact('row'));

    }

    public function update(Request $request, $id )
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id' ,
            'purchases_date'=>'required|date',
            'pay_method'=>'required|in:debit,cash',
            'supplier_id'=>'required|exists:suppliers,id',
            'supplier_fatora_number'=>'required|unique:head_back_purchases,supplier_fatora_number,'.$id,
            'fatora_number'=>'required|unique:head_back_purchases,fatora_number,'.$id,

        ]);


        $datails = $request->validate([
            'productive_id'=>'required|array',
            'productive_id.*'=>'required',
            'amount'=>'required|array',
            'amount.*'=>'required',
            'productive_buy_price'=>'required|array',
            'productive_buy_price.*'=>'required',

        ]);

        if (count($request->amount) != count($request->productive_id) )
            return response()->json(
                [
                    'code' => 421,
                    'message' => 'المنتج مطلوب'
                ]);




        $headBackPurchases= HeadBackPurchases::findOrFail($id);
        $headBackPurchases->update($data);


        HeadBackPurchasesDetails::where('head_back_purchases_id',$id)->delete();

        $sql=[];

        if ($request->productive_id ) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [

                    'head_back_purchases_id' => $headBackPurchases->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount'=>$request->amount[$i],
                    'productive_buy_price'=>$request->productive_buy_price[$i],
                    'total'=>$request->productive_buy_price[$i]*$request->amount[$i],
                    'all_pieces'=>$request->amount[$i]*$productive->num_pieces_in_package,
                    'date' => $headBackPurchases->date,
                    'year' => $headBackPurchases->year,
                    'month' => $headBackPurchases->month,
                    'publisher' => $headBackPurchases->publisher,
                    'created_at'=>$headBackPurchases->created_at,
                    'updated_at'=>date('Y-m-d H:i:s'),

                ];

                array_push($sql,$details);
            }
            DB::table('head_back_purchases_details')->insert($sql);

            $headBackPurchases->update([
                'total'=>HeadBackPurchasesDetails::where('head_back_purchases_id',$headBackPurchases->id)->sum('total'),
            ]);

        }




        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    }



    public function destroy( $id)
    {

        $row=HeadBackPurchases::find($id);

        $row->delete();

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);
    }//end fun

    public function getHeadBackPurchasesDetails($id){
        $purchase=HeadBackPurchases::findOrFail($id);
        $rows=HeadBackPurchasesDetails::where('head_back_purchases_id',$id)->with(['productive','purchases'])->get();
        return view('Admin.CRUDS.headBackPurchases.parts.headBackPurchasesDetails',compact('rows'));
    }


}