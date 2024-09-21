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
    public function __construct()
    {
        $this->middleware('permission:عرض الفواتير,admin')->only('index');
        $this->middleware('permission:تعديل الفواتير,admin')->only(['edit', 'update']);
        $this->middleware('permission:إنشاء الفواتير,admin')->only(['create', 'store']);
        $this->middleware('permission:حذف الفواتير,admin')->only('destroy');
    }
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rows = Sales::query()->with(['storage', 'client']);

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $rows->whereBetween('sales_date', [$request->from_date, $request->to_date]);
            }

            if ($request->filled('representative_id')) {
                $rows->where('representative_id', $request->representative_id);
            }

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
                ->editColumn('status', function ($row) {
                    $statuses = [
                        'new' => ['text' => 'جديد', 'class' => 'btn-primary'],
                        'in_progress' => ['text' => 'جاري التجهيز', 'class' => 'btn-info'],
                        'complete' => ['text' => 'مكتمل', 'class' => 'btn-success'],
                        'canceled' => ['text' => 'ملغي', 'class' => 'btn-danger'],
                    ];
                    $statusesWithoutNew = [
                        'in_progress' => ['text' => 'جاري التجهيز', 'class' => 'btn-info'],
                        'complete' => ['text' => 'مكتمل', 'class' => 'btn-success'],
                        'canceled' => ['text' => 'ملغي', 'class' => 'btn-danger'],
                    ];

                    $currentStatus = $statuses[$row->status];

                    $dropdownHtml = '
                        <div class="dropdown">
                            <button class="btn ' . $currentStatus['class'] . ' dropdown-toggle" type="button" id="statusDropdown' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                ' . $currentStatus['text'] . '
                            </button>
                            <div class="dropdown-menu" aria-labelledby="statusDropdown' . $row->id . '">';

                    foreach ($statusesWithoutNew as $status => $info) {
                        $dropdownHtml .= '
                            <a class="dropdown-item" href="#" data-status="' . $status . '" data-row-id="' . $row->id . '">
                                ' . $info['text'] . '
                            </a>';
                    }

                    $dropdownHtml .= '
                            </div>
                        </div>';

                    return $dropdownHtml;
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
            'total_discount' => 'nullable|numeric|min:0|max:99',
            'sales_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'client_id' => 'required|exists:clients,id',
            'fatora_number' => 'required|unique:sales,fatora_number',

        ]);

        $datails = $request->validate([
            'company_id' => 'required|array',
            'company_id.*' => 'required|exists:companies,id',
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
            'discount_percentage.*' => 'required|numeric|max:100|min:0',
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
                $details = [

                    'sales_id' => $sales->id,
                    'company_id' => $request->company_id[$i],
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount' => $request->amount[$i],
                    'bouns' => $request->bouns[$i],
                    'discount_percentage' => $request->discount_percentage[$i],
                    'batch_number' => $request->batch_number[$i],
                    'productive_sale_price' => $request->productive_sale_price[$i],
                    'total' => ($request->productive_sale_price[$i] * $request->amount[$i]) - (($request->productive_sale_price[$i] * $request->amount[$i]) * $request->discount_percentage[$i] / 100),
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

            $total = SalesDetails::where('sales_id', $sales->id)->sum('total');
            $totalAfterDiscount = $total - ($total / 100 * $data['total_discount'] ?? 0);

            $sales->update([
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

        $row = Sales::find($id);

        return view('Admin.CRUDS.sales.edit', compact('row'));

    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'total_discount' => 'nullable|numeric|min:0|max:99',
            'sales_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'client_id' => 'required|exists:clients,id',
            'fatora_number' => 'required|unique:sales,fatora_number,' . $id,

        ]);

        $datails = $request->validate([
            'company_id' => 'required|array',
            'company_id.*' => 'required|exists:companies,id',
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
            'discount_percentage.*' => 'nullable|numeric|min:0|max:99',
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
                    'company_id' => $request->company_id[$i],
                    'productive_id' => $request->productive_id[$i],
                    'productive_code' => $productive->code,
                    'amount' => $request->amount[$i],
                    'bouns' => $request->bouns[$i],
                    'discount_percentage' => $request->discount_percentage[$i],
                    'batch_number' => $request->batch_number[$i],
                    'productive_sale_price' => $request->productive_sale_price[$i],
                    'total' => ($request->productive_sale_price[$i] * $request->amount[$i]) - (($request->productive_sale_price[$i] * $request->amount[$i]) * $request->discount_percentage[$i] / 100),
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

            $total = SalesDetails::where('sales_id', $sales->id)->sum('total');
            $totalAfterDiscount = $total - ($total / 100 * $data['total_discount'] ?? 0);

            $sales->update([
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

    /**
     * [Description for getStatusName]
     *
     * @param string $status
     * @return string
     */
    public function getStatusName(string $status)
    {
        $name = [
            'new' => '<span class="badge badge-primary">جديد</span>',
            'in_progress' => '<span class="badge badge-info">جاري التجهيز</span>',
            'complete' => '<span class="badge badge-success">مكتمل</span>',
            'canceled' => '<span class="badge badge-danger">ملغي</span>',
        ];

        return $name[$status];
    }

    public function updateStatus(Request $request)
    {
        $row = Sales::findOrFail($request->id);
        $row->status = $request->status;
        $row->save();

        return response()->json(['success' => true]);
    }

}
