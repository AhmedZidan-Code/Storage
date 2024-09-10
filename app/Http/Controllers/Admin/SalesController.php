<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Productive;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SalesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rows = Sales::query()->with(['storage', 'client']);
            return DataTables::of($rows)
                ->addColumn('action', function ($row) {

                    $edit = '';
                    $delete = '';

                    return '

                           <button ' . $edit . '   class="editBtn-p btn rounded-pill btn-primary waves-effect waves-light"
                                    data-id="' . $row->id . '"
                            <span class="svg-icon svg-icon-3">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-edit"></i>
                                </span>
                            </span>
                            </button>
                            <button ' . $delete . '  class="btn rounded-pill btn-danger waves-effect waves-light delete"
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

        return view('Admin.CRUDS.sales.index');
    }

    public function create()
    {
        $model = DB::table('sales')->latest('id')->select('id')->first();
        if ($model) {
            $count = $model->id;
        } else {
            $count = 0;
        }

        return view('Admin.CRUDS.sales.create', compact('count'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'sales_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'client_id' => 'required|exists:clients,id',
            'fatora_number' => 'required|unique:sales,fatora_number',

        ]);

        $datails = $request->validate([
            'productive_id' => 'required|array',
            'productive_id.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'required',
            'productive_sale_price' => 'required|array',
            'productive_sale_price.*' => 'required',
            'bouns' => 'required|array',
            'discount_percentage' => 'required|array',
            'batch_number' => 'required|array',
            'bouns.*' => 'required',
            'discount_percentage.*' => 'required',
            'batch_number.*' => 'required',
        ]);

        if (count($request->amount) != count($request->productive_id)) {
            return response()->json(
                [
                    'code' => 421,
                    'message' => 'المنتج مطلوب',
                ]);
        }

        $purchases_number = 1;
        $latestModel = DB::table('sales')->latest('id')->select('id')->first();
        if ($latestModel) {
            $purchases_number = $latestModel->id + 1;
        }

        $data['publisher'] = auth('admin')->user()->id;
        $data['sales_number'] = $purchases_number;
        $data['date'] = date('Y-m-d');
        $data['month'] = date('m');
        $data['year'] = date('Y');

        $sales = Sales::create($data);

        $sql = [];

        if ($request->productive_id) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [

                    'sales_id' => $sales->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount' => $request->amount[$i],
                    'bouns' => $request->bouns[$i],
                    'discount_percentage' => $request->discount_percentage[$i],
                    'batch_number' => $request->batch_number[$i],
                    'productive_sale_price' => $request->productive_sale_price[$i],
                    'total' => $request->productive_sale_price[$i] * $request->amount[$i],
                    'all_pieces' => $request->amount[$i] * $productive->num_pieces_in_package,
                    'date' => date('Y-m-d'),
                    'year' => date('Y'),
                    'month' => date('m'),
                    'publisher' => auth('admin')->user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),

                ];

                array_push($sql, $details);
            }
            DB::table('sales_details')->insert($sql);

            $sales->update([
                'total' => SalesDetails::where('sales_id', $sales->id)->sum('total'),
            ]);

        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    }

    public function edit($id)
    {

        $row = Sales::find($id);

        return view('Admin.CRUDS.sales.edit', compact('row'));

    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'sales_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'client_id' => 'required|exists:clients,id',
            'fatora_number' => 'required|unique:sales,fatora_number,' . $id,

        ]);

        $datails = $request->validate([
            'productive_id' => 'required|array',
            'productive_id.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'required',
            'productive_sale_price' => 'required|array',
            'productive_sale_price.*' => 'required',
            'bouns' => 'required|array',
            'discount_percentage' => 'required|array',
            'batch_number' => 'required|array',
            'bouns.*' => 'required',
            'discount_percentage.*' => 'required',
            'batch_number.*' => 'required',
        ]);

        if (count($request->amount) != count($request->productive_id)) {
            return response()->json(
                [
                    'code' => 421,
                    'message' => 'المنتج مطلوب',
                ]);
        }

        $sales = Sales::findOrFail($id);
        $sales->update($data);

        SalesDetails::where('sales_id', $id)->delete();

        $sql = [];

        if ($request->productive_id) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [

                    'sales_id' => $sales->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount' => $request->amount[$i],
                    'bouns' => $request->bouns[$i],
                    'discount_percentage' => $request->discount_percentage[$i],
                    'batch_number' => $request->batch_number[$i],
                    'productive_sale_price' => $request->productive_sale_price[$i],
                    'total' => $request->productive_sale_price[$i] * $request->amount[$i],
                    'all_pieces' => $request->amount[$i] * $productive->num_pieces_in_package,
                    'date' => $sales->date,
                    'year' => $sales->year,
                    'month' => $sales->month,
                    'publisher' => $sales->publisher,
                    'created_at' => $sales->created_at,
                    'updated_at' => date('Y-m-d H:i:s'),

                ];

                array_push($sql, $details);
            }
            DB::table('sales_details')->insert($sql);

            $sales->update([
                'total' => SalesDetails::where('sales_id', $sales->id)->sum('total'),
            ]);

        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    }

    public function destroy($id)
    {

        $row = Sales::find($id);

        $row->delete();

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    } //end fun

    public function getSalesDetails($id)
    {
        $purchase = Sales::findOrFail($id);
        $rows = SalesDetails::where('sales_id', $id)->with(['productive', 'sales'])->get();
        return view('Admin.CRUDS.sales.parts.salesDetails', compact('rows'));
    }

    public function makeRowDetailsForSalesDetails()
    {
        $id = rand(2, 999999999999999);
        $html = view('Admin.CRUDS.sales.parts.details', compact('id'))->render();

        return response()->json(['status' => true, 'html' => $html, 'id' => $id]);
    }

}
