<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeadBackSales;
use App\Models\HeadBackSalesDetails;
use App\Models\Productive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class HeadBackSalesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rows = HeadBackSales::query()->with(['storage','client']);
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

        return view('Admin.CRUDS.headBackSales.index');
    }


    public function create()
    {
        $model=DB::table('head_back_sales')->latest('id')->select('id')->first();
        if ($model)
            $count=$model->id;
        else
            $count=0;
        return view('Admin.CRUDS.headBackSales.create',compact('count'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id' ,
            'sales_date'=>'required|date',
            'pay_method'=>'required|in:debit,cash',
            'client_id'=>'required|exists:clients,id',
            'fatora_number'=>'required|unique:head_back_sales,fatora_number',

        ]);


        $datails = $request->validate([
            'productive_id'=>'required|array',
            'productive_id.*'=>'required',
            'amount'=>'required|array',
            'amount.*'=>'required',
            'productive_sale_price'=>'required|array',
            'productive_sale_price.*'=>'required',

        ]);

        if (count($request->amount) != count($request->productive_id) )
            return response()->json(
                [
                    'code' => 421,
                    'message' => 'المنتج مطلوب'
                ]);
        $purchases_number=1;
        $latestModel=DB::table('head_back_sales')->latest('id')->select('id')->first();
        if ($latestModel)
            $purchases_number=$latestModel->id+1;


        $data['publisher']=auth('admin')->user()->id;
        $data['sales_number']=$purchases_number;
        $data['date']=date('Y-m-d');
        $data['month']=date('m');
        $data['year']=date('Y');

        $headBackSales= HeadBackSales::create($data);



        $sql=[];

        if ($request->productive_id ) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [
                    'storage_id' => $productive->storage_id,
                    'head_back_sales_id' => $headBackSales->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount'=>$request->amount[$i],
                    'productive_sale_price'=>$request->productive_sale_price[$i],
                    'total'=>$request->productive_sale_price[$i]*$request->amount[$i],
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
            DB::table('head_back_sales_details')->insert($sql);

            $headBackSales->update([
                'total'=>HeadBackSalesDetails::where('head_back_sales_id',$headBackSales->id)->sum('total'),
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



        $row=HeadBackSales::find($id);

        return view('Admin.CRUDS.headBackSales.edit', compact('row'));

    }

    public function update(Request $request, $id )
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id' ,
            'sales_date'=>'required|date',
            'pay_method'=>'required|in:debit,cash',
            'client_id'=>'required|exists:clients,id',
            'fatora_number'=>'required|unique:head_back_sales,fatora_number,'.$id,

        ]);


        $datails = $request->validate([
            'productive_id'=>'required|array',
            'productive_id.*'=>'required',
            'amount'=>'required|array',
            'amount.*'=>'required',
            'productive_sale_price'=>'required|array',
            'productive_sale_price.*'=>'required',

        ]);

        if (count($request->amount) != count($request->productive_id) )
            return response()->json(
                [
                    'code' => 421,
                    'message' => 'المنتج مطلوب'
                ]);




        $headBackSales= HeadBackSales::findOrFail($id);
        $headBackSales->update($data);


        HeadBackSalesDetails::where('head_back_sales_id',$id)->delete();

        $sql=[];

        if ($request->productive_id ) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [
                    'storage_id' => $productive->storage_id,
                    'head_back_sales_id' => $headBackSales->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount'=>$request->amount[$i],
                    'productive_sale_price'=>$request->productive_sale_price[$i],
                    'total'=>$request->productive_sale_price[$i]*$request->amount[$i],
                    'all_pieces'=>$request->amount[$i]*$productive->num_pieces_in_package,
                    'date' => $headBackSales->date,
                    'year' => $headBackSales->year,
                    'month' => $headBackSales->month,
                    'publisher' => $headBackSales->publisher,
                    'created_at'=>$headBackSales->created_at,
                    'updated_at'=>date('Y-m-d H:i:s'),

                ];

                array_push($sql,$details);
            }
            DB::table('head_back_sales_Details')->insert($sql);

            $headBackSales->update([
                'total'=>HeadBackSalesDetails::where('head_back_sales_id',$headBackSales->id)->sum('total'),
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

        $row=HeadBackSales::find($id);

        $row->delete();

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);
    }//end fun

    public function getHeadBackSalesDetails($id){
        $headBackSales=HeadBackSales::findOrFail($id);
        $rows=HeadBackSalesDetails::where('head_back_sales_id',$id)->with(['productive'])->get();
        return view('Admin.CRUDS.headBackSales.parts.headBackSalesDetails',compact('rows'));
    }


    public function makeRowDetailsForHeadBackSalesDetails(){
        $id=rand(2,999999999999999);
        $html=  view('Admin.CRUDS.headBackSales.parts.details', compact('id'))->render();

        return response()->json(['status'=>true,'html'=>$html,'id'=>$id]);
    }

}
