<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Productive;
use App\Models\Unite;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:عرض الاصناف,admin')->only('index');
        $this->middleware('permission:تعديل الاصناف,admin')->only(['edit', 'update']);
        $this->middleware('permission:إنشاء الاصناف,admin')->only(['create', 'store']);
        $this->middleware('permission:حذف الاصناف,admin')->only('destroy');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Productive::query()->with(['unit', 'category', 'company']);
            return DataTables::of($rows)
                ->addColumn('action', function ($row) {

                    $edit = '';
                    $delete = '';

                    return '
                            <button ' . $edit . '   class="editBtn btn rounded-pill btn-primary waves-effect waves-light"
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

            // ->add('company', function ($row) {
            //      return $row->company->title;

            // })

                ->editColumn('created_at', function ($admin) {
                    return date('Y/m/d', strtotime($admin->created_at));
                })
                ->escapeColumns([])
                ->make(true);

        }

        return view('Admin.CRUDS.productive.index');
    }

    public function create()
    {
        $categories = Category::get();
        $unites = Unite::get();

        return view('Admin.CRUDS.productive.parts.create', compact('categories', 'unites'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required',
            'name' => 'required|unique:productive,name',
            'one_buy_price' => 'required',
            'packet_buy_price' => 'required',
            'one_sell_price' => 'required',
            'packet_sell_price' => 'required',
            'num_pieces_in_package' => 'required',
            'unit_id' => 'required|exists:unites,id',
            'category_id' => 'required|exists:categories,id',
            'company_id' => 'required|exists:companies,id',

        ]);

        $data['publisher'] = auth('admin')->user()->id;

        Productive::create($data);

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    }

    public function edit($id)
    {
        $row = Productive::with('company')->find($id);
        $categories = Category::get();
        $unites = Unite::get();

        return view('Admin.CRUDS.productive.parts.edit', compact('row', 'categories', 'unites'));

    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'code' => 'required',
            'name' => 'required|unique:productive,name,' . $id,
            'one_buy_price' => 'required',
            'packet_buy_price' => 'required',
            'one_sell_price' => 'required',
            'packet_sell_price' => 'required',
            'num_pieces_in_package' => 'required',
            'unit_id' => 'required|exists:unites,id',
            'category_id' => 'required|exists:categories,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $row = Productive::find($id);
        $row->update($data);

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    }

    public function destroy($id)
    {

        $row = Productive::find($id);

        $row->delete();

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);
    } //end fun

}
