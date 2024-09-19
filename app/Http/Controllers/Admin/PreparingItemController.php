<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Models\SalesDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PreparingItemController extends Controller
{
        public function __construct()
    {
        $this->middleware('permission:عرض الاصناف,admin')->only('index');
        $this->middleware('permission:تعديل الاصناف,admin')->only(['edit', 'update', 'updateIsPrepared']);

    }
    public function index(Request $request)
    {
        $user = auth('admin')->user();

        if ($request->ajax()) {
            $rows = Sales::query()->where('status', 'in_progress')->whereHas('details', function ($q) use ($user) {
                return $q->where('company_id', $user->employee->company_id);
            })->with(['storage', 'client']);
            return DataTables::of($rows)
                ->addColumn('action', function ($row) {

                    $edit = '';
                    $delete = '';

                    return '
                           <button ' . $edit . '   class=" btn rounded-pill btn-primary waves-effect waves-light showDetails"
                                    data-id="' . $row->id . '"
                            <span class="svg-icon svg-icon-3">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-edit"></i>
                                </span>
                            </span>
                            </button>';
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d H:i');
                })
                ->escapeColumns([])

                ->make(true);
        }

        return view('Admin.CRUDS.prepare_items.index');
    }
    public function edit($id)
    {
        $user = auth('admin')->user();
        $row = Sales::with(['details' => fn($q) => $q->where('company_id', $user->employee->company_id)])->find($id);

        $view = view('Admin.CRUDS.prepare_items.parts.editForm', compact('row'))->render();

        return response()->json(['view' => $view, 'row' => $row]);
    }

    public function update(Request $request, $id)
    {

        $datails = $request->validate([
            'amount' => 'required|array',
            'amount.*' => 'required',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|max:500',
            'sales_details_id.*' => 'exists:sales_details,id',

        ]);

        if ($request->sales_details_id) {
            foreach ($request->sales_details_id as $key => $value) {
                SalesDetails::where('id', $value)->update([
                    'amount' => $request->amount[$key],
                    'notes' => $request->notes[$key],
                ]);
            }
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    }

    public function updateIsPrepared(Request $request)
    {
        $row = SalesDetails::findOrFail($request->id);
        if ($request->is_prepared == 1) {
            $row->amount = $request->amount;
            $row->notes = $request->notes;
        }
        $row->is_prepared = $request->is_prepared;
        $row->save();

        return response()->json(['success' => true]);
    }

}
