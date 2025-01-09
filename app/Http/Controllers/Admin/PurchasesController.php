<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Productive;
use App\Models\Purchases;
use App\Models\PurchasesDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:عرض المشتريات,admin')->only('index');
        $this->middleware('permission:تعديل المشتريات,admin')->only(['edit', 'update']);
        $this->middleware('permission:إنشاء المشتريات,admin')->only(['create', 'store']);
        $this->middleware('permission:حذف المشتريات,admin')->only('destroy');
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('Admin.CRUDS.purchases.index');
        }

        return $this->generateDataTable(
            Purchases::query()->with(['storage', 'supplier'])
        );
    }

    private function generateDataTable($query)
    {
        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return $this->generateActionButtons($row);
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier?->name;
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

    private function generateActionButtons($row)
    {
        return '
            <button class="editBtn-p btn rounded-pill btn-primary waves-effect waves-light"
                    data-id="' . $row->id . '">
                <span class="svg-icon svg-icon-3">
                    <i class="fa fa-edit"></i>
                </span>
            </button>
            <button class="btn rounded-pill btn-danger waves-effect waves-light delete"
                    data-id="' . $row->id . '">
                <span class="svg-icon svg-icon-3">
                    <i class="fa fa-trash"></i>
                </span>
            </button>';
    }

    public function create()
    {
        $lastId = DB::table('purchases')->latest('id')->value('id') ?? 0;
        return view('Admin.CRUDS.purchases.create', ['count' => $lastId]);
    }

    public function store(Request $request)
    {

        try {
            return DB::transaction(function () use ($request) {
                [$data, $details] = $this->validateData($request);
                $data['total_discount'] ??= 0;
                $purchases = $this->createPurchaseRecord($data);
                $this->processDetailsAndUpdateTotals($request, $purchases, $data['total_discount'] ??= 0);

                return $this->successResponse('تمت العملية بنجاح!');
            });
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function validateData(Request $request, $id = null)
    {
        $data = $request->validate([
            'storage_id' => 'required|exists:storages,id',
            'total_discount' => 'nullable|numeric|min:0|max:99',
            'purchases_date' => 'required|date',
            'pay_method' => 'required|in:debit,cash',
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_fatora_number' => 'required|unique:purchases,supplier_fatora_number' . ($id ? ',' . $id : ''),
            'fatora_number' => 'required|unique:purchases,fatora_number' . ($id ? ',' . $id : ''),
        ]);

        $details = $request->validate([
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
            'exp_date.*' => 'required|date',
        ]);

        if (count($request->amount) !== count($request->productive_id)) {
            throw ValidationException::withMessages(['المنتج مطلوب']);
        }

        return [$data, $details];
    }

    private function createPurchaseRecord(array $data)
    {
        $now = Carbon::now();
        $latestId = DB::table('purchases')->latest('id')->value('id') ?? 0;

        return Purchases::create(array_merge($data, [
            'publisher' => auth('admin')->user()->id,
            'purchases_number' => $latestId + 1,
            'date' => $now->toDateString(),
            'month' => $now->month,
            'year' => $now->year,
        ]));
    }

    private function processDetailsAndUpdateTotals(Request $request, Purchases $purchases, $totalDiscount)
    {
        if (empty($request->productive_id)) {
            return;
        }

        $detailsData = $this->prepareDetailsData($request, $purchases);
        DB::table('purchases_details')->insert($detailsData);

        $this->updatePurchasesTotals($purchases, $totalDiscount);
    }

    private function prepareDetailsData(Request $request, Purchases $purchases)
    {
        $detailsData = [];
        $now = Carbon::now();

        foreach ($request->productive_id as $i => $productiveId) {
            $productive = Productive::findOrFail($productiveId);
            $buyPrice = $request->productive_buy_price[$i];
            $amount = $request->amount[$i];
            $discountPercentage = $request->discount_percentage[$i];

            $detailsData[] = [
                'storage_id' => $purchases->storage_id,
                'purchases_id' => $purchases->id,
                'productive_id' => $productiveId,
                'productive_code' => $productive->code,
                'amount' => $amount,
                'bouns' => $request->bouns[$i],
                'exp_date' => $request->exp_date[$i],
                'discount_percentage' => $discountPercentage,
                'batch_number' => $request->batch_number[$i],
                'productive_buy_price' => $buyPrice,
                'total' => $this->calculateTotal($buyPrice, $amount, $discountPercentage),
                'all_pieces' => $amount * $productive->num_pieces_in_package,
                'date' => $purchases->date,
                'year' => $purchases->year,
                'month' => $purchases->month,
                'publisher' => $purchases->publisher,
                'created_at' => $purchases->created_at ?? $now,
                'updated_at' => $now,
            ];
        }

        return $detailsData;
    }

    private function calculateTotal($buyPrice, $amount, $discountPercentage)
    {
        $subtotal = $buyPrice * $amount;
        return $subtotal - ($subtotal * $discountPercentage / 100);
    }

    private function updatePurchasesTotals(Purchases $purchases, $totalDiscount)
    {
        $total = PurchasesDetails::where('purchases_id', $purchases->id)->sum('total');
        $totalAfterDiscount = $total - ($total * $totalDiscount / 100);

        $purchases->update([
            'total' => $total,
            'total_discount' => $totalDiscount,
            'total_after_discount' => $totalAfterDiscount,
        ]);
    }

    private function successResponse($message, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
        ]);
    }

    private function errorResponse($message, $code = 500)
    {
        return response()->json([
            'code' => $code,
            'message' => 'حدث خطأ أثناء معالجة الطلب',
            'error' => $message,
        ]);
    }

    public function edit($id)
    {
        $row = Purchases::findOrFail($id);
        return view('Admin.CRUDS.purchases.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                [$data, $details] = $this->validateData($request, $id);

                $purchases = Purchases::findOrFail($id);
                $purchases->update($data);

                PurchasesDetails::where('purchases_id', $id)->delete();
                $this->processDetailsAndUpdateTotals($request, $purchases, $data['total_discount'] ?? 0);

                return $this->successResponse('تمت العملية بنجاح!');
            });
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        Purchases::findOrFail($id)->delete();
        return $this->successResponse('تمت العملية بنجاح!');
    }

    public function getPurchasesDetails($id)
    {
        $rows = PurchasesDetails::where('purchases_id', $id)
            ->with(['productive', 'purchases'])
            ->get();
        return view('Admin.CRUDS.purchases.parts.purchasesDetails', compact('rows'));
    }

    public function getStorages(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $term = trim($request->term);
        $posts = DB::table('storages')
            ->select('id', 'title as text')
            ->where('title', 'LIKE', '%' . $term . '%')
            ->orderBy('title', 'asc')
            ->simplePaginate(3);

        $morePages = !empty($posts->nextPageUrl());

        return response()->json([
            "results" => $posts->items(),
            "pagination" => ["more" => $morePages],
        ]);
    }

    public function makeRowDetailsForPurchasesDetails()
    {
        $id = rand(2, 999999999999999);
        $html = view('Admin.CRUDS.purchases.parts.details', compact('id'))->render();
        return response()->json(['status' => true, 'html' => $html, 'id' => $id]);
    }

    public function getPurchasesForSupplier(Request $request, $supplier_id)
    {
        if ($request->ajax()) {
            $numbers = DB::table('purchases')->where('supplier_id', $supplier_id)->select('id', 'fatora_number as text')
                ->orderBy('id', 'asc')->simplePaginate(3);
            $morePages = true;
            $pagination_obj = json_encode($numbers);
            if (empty($numbers->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $numbers->items(),
                "pagination" => array(
                    "more" => $morePages,
                ),
            );

            return \Response::json($results);
        }
    }
}
