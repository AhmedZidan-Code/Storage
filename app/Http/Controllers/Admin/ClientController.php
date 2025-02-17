<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AreaType;
use App\Enum\PaymentCategory;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Client;
use App\Models\ClientSubscription;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:عرض العملاء,admin')->only('index');
        $this->middleware('permission:تعديل العملاء,admin')->only(['edit', 'update']);
        $this->middleware('permission:إنشاء العملاء,admin')->only(['create', 'store']);
        $this->middleware('permission:حذف العملاء,admin')->only('destroy');
    }
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rows = Client::query()->with(['city', 'governorate', 'subscription']);
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

                ->addColumn('subscription', function ($admin) {
                    if ($admin->subscription) {
                        return $admin->subscription?->title . "({$admin->subscription?->discount}%)";
                    }
                })
                ->editColumn('created_at', function ($admin) {
                    return date('Y/m/d', strtotime($admin->created_at));
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('Admin.CRUDS.clients.index');
    }

    public function create()
    {
        $governorates = Area::where('from_id', null)->get();
        $subscriptions = ClientSubscription::get();
        $employees = Employee::get();
        $paymentCategories = PaymentCategory::getCategoriesSelect();

        return view('Admin.CRUDS.clients.parts.create', compact('governorates', 'paymentCategories', 'subscriptions', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:clients,code',
            'phone' => 'required|unique:clients,phone',
            'governorate_id' => 'required|exists:areas,id',
            'payment_category' => 'required',
            'representative_id' => 'required|exists:representatives,id',
            'city_id' => 'required|exists:areas,id',
            'address' => 'nullable',
            'previous_indebtedness' => 'required|integer',
            'client_subscription_id' => 'required|exists:client_subscriptions,id',
            'region_id' => 'required|exists:areas,id',
            'distributor_id' => 'required|exists:representatives,id',
            'commercial_register' => 'required|max:100',
            'tax_card' => 'required|max:100',
            'tele_sales_am' => 'required',
            'tele_sales_pm' => 'required',
        ]);

        $data['publisher'] = auth('admin')->user()->id;

        Client::create($data);

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]
        );
    }

    public function show(Client $client)
    {
        return response()->json([
            'client' => $client,
            'category' => PaymentCategory::getCategoriesSelect()[$client->payment_category],
        ]);
    }

    public function edit($id)
    {

        $row = Client::with(['representative', 'distributor'])->find($id);
        $governorates = Area::where('from_id', null)->get();
        $cities = Area::where('type', AreaType::CITY)->where('from_id', $row->governorate_id)->get();
        $regions = Area::where('type', AreaType::REGION)->where('from_id', $row->city_id)->get();
        $subscriptions = ClientSubscription::get();
        $paymentCategories = PaymentCategory::getCategoriesSelect();
        $employees = Employee::get();

        return view('Admin.CRUDS.clients.parts.edit', compact('row', 'governorates', 'cities', 'regions', 'paymentCategories', 'subscriptions', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:clients,code,' . $id,
            'phone' => 'required|unique:clients,phone,' . $id,
            'governorate_id' => 'required|exists:areas,id',
            'payment_category' => 'required|in:1,2,3,4',
            'representative_id' => 'required|exists:representatives,id',
            'city_id' => 'required|exists:areas,id',
            'address' => 'nullable',
            'previous_indebtedness' => 'required|integer',
            'client_subscription_id' => 'required|exists:client_subscriptions,id',
            'region_id' => 'required|exists:areas,id',
            'distributor_id' => 'required|exists:representatives,id',
            'tele_sales_am' => 'required',
            'tele_sales_pm' => 'required',
            'commercial_register' => 'required|max:100',
            'tax_card' => 'required|max:100',

        ]);

        $row = Client::find($id);
        $row->update($data);

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]
        );
    }

    public function destroy($id)
    {

        $row = Client::find($id);

        $row->delete();

        return response()->json(
            [
                'code' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]
        );
    } //end fun

    public function getCitiesForGovernorate($id)
    {
        $cities = Area::where('from_id', $id)->get();
        return view('Admin.CRUDS.clients.parts.cities', compact('cities'));
    }

    public function getRegionsForCity($id)
    {
        $regions = Area::where('from_id', $id)->get();
        return view('Admin.CRUDS.clients.parts.regions', compact('regions'));
    }
}
