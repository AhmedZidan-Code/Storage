<form id="form" enctype="multipart/form-data" method="POST" action="{{route('purchases.update',$row->id)}}">
    @csrf
    @method('PUT')
    <div class="row my-4 g-4">

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="purchases_number" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">  رقم الطلب</span>
            </label>
            <!--end::Label-->
            <input id="purchases_number" disabled required type="text"
                   class="form-control form-control-solid" name="purchases_number"
                   value="{{$row->purchases_number}}"/>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="purchases_date" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">  تاريخ الطلب</span>
            </label>
            <!--end::Label-->
            <input id="purchases_date" required type="date" class="form-control form-control-solid"
                   name="purchases_date"
                   value="{{$row->purchases_date}}"/>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="pay_method" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">  طريقة الشراء </span>
            </label>
            <select id='pay_method' name="pay_method" class="form-control">
                <option selected disabled>اختر طريقة الشراء</option>
                <option @if($row->pay_method=='cash') selected @endif value="cash">كاش</option>
                <option @if($row->pay_method=='debit') selected @endif value="debit">اجل</option>

            </select>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="storage_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">   المخزن</span>
            </label>
            <select id='storage_id' name="storage_id" style='width: 200px;'>
                <option value="{{$row->storage_id}}">{{$row->storage->title??''}}</option>
            </select>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="supplier_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">   المورد</span>
            </label>
            <select id='supplier_id' name="supplier_id" style='width: 200px;'>
                <option value="{{$row->supplier_id}}">{{$row->supplier->name??''}}</option>
            </select>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="fatora_number" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">  رقم الفاتورة</span>
            </label>
            <!--end::Label-->
            <input id="fatora_number" required type="text" class="form-control form-control-solid"
                   name="fatora_number"
                   value="{{$row->fatora_number}}"/>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="supplier_fatora_number"
                   class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">  رقم فاتورة المورد</span>
            </label>
            <!--end::Label-->
            <input id="supplier_fatora_number" required type="text" class="form-control form-control-solid"
                   name="supplier_fatora_number"
                   value="{{$row->supplier_fatora_number}}"/>
        </div>

        <div class="col-md-10">
            <table id="table-details"
                   class="table table-bordered dt-responsive nowrap table-striped align-middle"
                   style="width:70% !important;">
                <thead>
                <tr>
                    <th> المنتج</th>
                    <th> كود المنتج</th>
                    <th>الوحدة</th>
                    <th> الكمية</th>
                    <th>سعر الشراء</th>
                    <th> القيمة الاجمالية</th>
                    <th>العمليات</th>
                </tr>
                </thead>
                <tbody id="details-container">
                @foreach(\App\Models\PurchasesDetails::where('purchases_id',$row->id)->get() as $key=>$pivot)
                    <tr id="tr-{{$key}}">
                        {{--                <th>1</th>--}}
                        <th>
                            <div class="d-flex flex-column mb-7 fv-row col-sm-2 ">
                                <label for="productive_id-{{$key}}"
                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                    <span class="required mr-1">  </span>
                                </label>
                                <select class="changeKhamId" data-id="{{$key}}" name="productive_id[]"
                                        id='productive_id-{{$key}}'
                                        style='width: 200px;'>
                                    <option selected
                                            value="{{$pivot->productive_id}}">{{$pivot->productive->name??''}}</option>
                                </select>
                            </div>
                        </th>
                        <th>
                            <input type="text" value="{{$pivot->productive_code}}" disabled
                                   id="productive_code-{{$key}}">
                        </th>
                        <th>
                            <input type="text" value="{{$pivot->productive->unit->title??''}}" disabled
                                   id="unit-{{$key}}">

                        </th>
                        <th>
                            <input data-id="{{$key}}" onchange="callTotal()" onkeyup="callTotal()" type="number"
                                   value="{{$pivot->amount}}" min="1"
                                   name="amount[]" id="amount-{{$key}}">

                        </th>
                        <th>
                            <input data-id="{{$key}}" step=".1" onchange="callTotal()" onkeyup="callTotal()"
                                   type="number" value="{{$pivot->productive_buy_price}}" min="1"
                                   name="productive_buy_price[]" id="productive_buy_price-{{$key}}">

                        </th>
                        <th>
                            <input type="number" disabled value="{{$pivot->total}}" min="1" name="total[]"
                                   id="total-{{$key}}">

                        </th>
                        <th>
                            <button class="btn rounded-pill btn-danger waves-effect waves-light delete-sup"
                                    data-id="{{$key}}">
                    <span class="svg-icon svg-icon-3">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-trash"></i>
                                </span>
                            </span>
                            </button>
                        </th>
                    </tr>

                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4" style="text-align: center; background-color: yellow">الاجمالي</th>
                    <th colspan="3" id="total_productive_buy_price"
                        style="text-align: center; background-color: #6c757d;color: white">{{$row->total}}
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <button id="addNewDetails" class="btn btn-primary">اضافة منتج اخر

            </button>
        </div>


    </div>

    <button form="form" type="submit" id="submit" class="btn btn-primary">
        <span class="indicator-label">اتمام</span>
    </button>

</form>

