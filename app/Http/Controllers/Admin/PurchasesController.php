<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Productive;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PurchasesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rows = Purchases::query()->with(['storage', 'supplier']);
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

        return view('Admin.CRUDS.purchases.index');
    }

    public function create()
    {
        $model = DB::table('purchases')->latest('id')->select('id')->first();
        if ($model) {
            $count = $model->id;
        } else {
            $count = 0;
        }

        return view('Admin.CRUDS.purchases.create', compact('count'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'total_discount' => 'nullable|numeric|min:0|max:99',
            'purchases_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_fatora_number' => 'required|unique:purchases,supplier_fatora_number',
            'fatora_number' => 'required|unique:purchases,fatora_number',

        ]);

        $datails = $request->validate([
            'productive_id' => 'required|array',
            'productive_id.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'required',
            'productive_buy_price' => 'required|array',
            'productive_buy_price.*' => 'required',
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
        $latestModel = DB::table('purchases')->latest('id')->select('id')->first();
        if ($latestModel) {
            $purchases_number = $latestModel->id + 1;
        }

        $data['publisher'] = auth('admin')->user()->id;
        $data['purchases_number'] = $purchases_number;
        $data['date'] = date('Y-m-d');
        $data['month'] = date('m');
        $data['year'] = date('Y');

        $purchases = Purchases::create($data);

        $sql = [];

        if ($request->productive_id) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [

                    'purchases_id' => $purchases->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'bouns' => $request->bouns[$i],
                    'discount_percentage' => $request->discount_percentage[$i],
                    'batch_number' => $request->batch_number[$i],
                    'amount' => $request->amount[$i],
                    'productive_buy_price' => $request->productive_buy_price[$i],
                    'total' => $request->productive_buy_price[$i] * $request->amount[$i],
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
            DB::table('purchases_details')->insert($sql);

            $total = PurchasesDetails::where('purchases_id', $purchases->id)->sum('total');
            $totalAfterDiscount = $total - ($total / 100 * $data['total_discount'] ?? 0);

            $purchases->update([
                'total' => $total,
                'total_discount' => $data['total_discount'],
                'total_after_discount' => $totalAfterDiscount,
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

        $row = Purchases::find($id);

        return view('Admin.CRUDS.purchases.edit', compact('row'));

    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'total_discount' => 'nullable|numeric|min:0|max:99',
            'purchases_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_fatora_number' => 'required|unique:purchases,supplier_fatora_number,' . $id,
            'fatora_number' => 'required|unique:purchases,fatora_number,' . $id,

        ]);

        $datails = $request->validate([
            'productive_id' => 'required|array',
            'productive_id.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'required',
            'productive_buy_price' => 'required|array',
            'productive_buy_price.*' => 'required',
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

        $purchases = Purchases::findOrFail($id);
        $purchases->update($data);

        PurchasesDetails::where('purchases_id', $id)->delete();

        $sql = [];

        if ($request->productive_id) {
            for ($i = 0; $i < count($request->productive_id); $i++) {

                $details = [];
                $productive = Productive::findOrFail($request->productive_id[$i]);

                $details = [

                    'purchases_id' => $purchases->id,
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount' => $request->amount[$i],
                    'bouns' => $request->bouns[$i],
                    'discount_percentage' => $request->discount_percentage[$i],
                    'batch_number' => $request->batch_number[$i],
                    'productive_buy_price' => $request->productive_buy_price[$i],
                    'total' => $request->productive_buy_price[$i] * $request->amount[$i],
                    'all_pieces' => $request->amount[$i] * $productive->num_pieces_in_package,
                    'date' => $purchases->date,
                    'year' => $purchases->year,
                    'month' => $purchases->month,
                    'publisher' => $purchases->publisher,
                    'created_at' => $purchases->created_at,
                    'updated_at' => date('Y-m-d H:i:s'),

                ];

                array_push($sql, $details);
            }
            DB::table('purchases_details')->insert($sql);

            $total = PurchasesDetails::where('purchases_id', $purchases->id)->sum('total');
            $totalAfterDiscount = $total - ($total / 100 * $data['total_discount'] ?? 0);

            $purchases->update([
                'total' => $total,
                'total_discount' => $data['total_discount'],
                'total_after_discount' => $totalAfterDiscount,
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

        $row = Purchases::find($id);

        $row->delete();

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    } //end fun

    public function getPurchasesDetails($id)
    {
        $purchase = Purchases::findOrFail($id);
        $rows = PurchasesDetails::where('purchases_id', $id)->with(['productive', 'purchases'])->get();
        return view('Admin.CRUDS.purchases.parts.purchasesDetails', compact('rows'));
    }

    public function getStorages(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('storages')->select('id', 'title as text')
                ->where('title', 'LIKE', '%' . $term . '%')
                ->orderBy('title', 'asc')->simplePaginate(3);

            $morePages = true;
            $pagination_obj = json_encode($posts);
            if (empty($posts->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $posts->items(),
                "pagination" => array(
                    "more" => $morePages,
                ),
            );

            return \Response::json($results);

        }

    }

    public function makeRowDetailsForPurchasesDetails()
    {
        $id = rand(2, 999999999999999);
        $html = view('Admin.CRUDS.purchases.parts.details', compact('id'))->render();

        return response()->json(['status' => true, 'html' => $html, 'id' => $id]);
    }

}
