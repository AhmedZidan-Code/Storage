<form id="form" enctype="multipart/form-data" method="POST" action="{{ route('sales.store') }}">
    @csrf
    <div class="row my-4 g-4">

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="sales_number" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> رقم الطلب</span>
            </label>
            <!--end::Label-->
            <input id="sales_number" disabled required type="text" class="form-control form-control-solid"
                name="sales_number" value="{{ $count + 1 }}" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="sales_date" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> تاريخ الطلب</span>
            </label>
            <!--end::Label-->
            <input id="sales_date" required type="date" class="form-control form-control-solid" name="sales_date"
                value="{{ date('Y-m-d') }}" />
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="pay_method" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> طريقة الشراء </span>
            </label>
            <select id='pay_method' name="pay_method" class="form-control ">
                <option selected disabled>اختر طريقة الشراء</option>
                <option value="cash">كاش</option>
                <option value="debit">اجل</option>

            </select>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="storage_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> المخزن</span>
            </label>
            <select id='storage_id' name="storage_id" class="form-control " style='width: 200px;'>
                <option selected disabled>- ابحث عن المخزن</option>
            </select>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="client_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> العميل</span>
            </label>
            <select id='client_id' class="form-control " name="client_id" style='width: 200px;'>
                <option selected disabled>- ابحث عن عملاء</option>
            </select>
        </div>

        {{-- <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="fatora_number" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> رقم الفاتورة</span>
            </label>
            <!--end::Label-->
            <input id="fatora_number" required type="text" class="form-control form-control-solid "
                name="fatora_number" value="" />
        </div> --}}



        <div class="table-responsive">
            <table id="table-details" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 150px;"> المنتج</th>
                        <th> كود المنتج</th>
                        <th style="width:200px;">رقم التشغيلة</th>
                        <th style="width:50px;"> الكمية</th>
                        <th>سعر الجمهور</th>
                        <th>بونص</th>
                        <th>نسبة الخصم</th>
                        <th>مرجح الشراء</th>
                        <th>مرجح البيع</th>
                        <th> القيمة الاجمالية</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody id="details-container">
                    <tr id="tr-1">
                        {{--                <th>1</th> --}}
                        <th>
                            <div class="d-flex flex-column mb-7 fv-row col-sm-2 " style="width: 100%;">
                                <label for="productive_id"
                                    class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                    <span class="required mr-1"> </span>
                                </label>
                                <select class="changeKhamId " data-id="1" name="productive_id[]" id='productive_id-1'
                                    style='width: 200px;'>
                                    <option selected disabled value='0'>- ابحث عن المنتج -</option>
                                </select>
                            </div>
                        </th>
                        <th>
                            <input type="text" disabled id="productive_code-1">
                            <input name="company_id[]" data-id="1" type="hidden" value="" id="company_id-1"
                                style="width: 100px; text-align: center;">

                        </th>
                        <th>
                            <select class="form-control selectClass" name="batch_number[]" id="batch_number-1"
                                style="width: 100px; text-align: center;">

                            </select>
                        </th>
                        <th>
                            <input data-id="1" onkeyup="callTotal(); checkBalance(this);"
                                class="form-control navigable" type="number" value="1" min="1"
                                name="amount[]" id="amount-1" style="width: 100px; text-align: center;">
                            <input data-id="1" class="form-control navigable" type="hidden" id="limit_for_request-1"
                                style="width: 100px; text-align: center;" disabled>
                            <input data-id="1" class="form-control navigable" type="hidden" id="limit_for_sale-1"
                                style="width: 100px; text-align: center;" disabled>
                            <input data-id="1" class="form-control navigable" type="hidden"
                                id="product_balance-1" style="width: 100px; text-align: center;" disabled>

                        </th>
                        <th>
                            <input data-id="1" step=".1" onchange="callTotal()" onkeyup="callTotal()"
                                type="number" value="1" min="1" name="productive_sale_price[]"
                                id="productive_sale_price-1" class="form-control navigable"
                                style="width: 100px; text-align: center;">

                        </th>
                        <th>
                            <input type="number" class="form-control navigable" value="0" min="0"
                                name="bouns[]" id="bouns-1" style="width: 100px; text-align: center;">
                            <!-- Adjusted width -->
                        </th>
                        <th>
                            <input type="number" value="0" min="0" name="discount_percentage[]"
                                id="discount_percentage-1" class="form-control navigable"
                                style="width: 100px; text-align: center;" onkeyup="callTotal()">
                            <!-- Adjusted width -->
                        </th>
                        <th>
                            <input type="number" class="form-control" value="0" min="0"
                                readonly name="likely_discount[]" id="likely_discount-1"
                                style="width: 100px; text-align: center;" {{-- onkeyup="callTotal()" --}}>
                            <!-- Adjusted width -->
                        </th>
                        <th>
                            <input type="number" class="form-control" value="0" min="0"
                                readonly name="likely_sale[]" id="likely_sale-1"
                                style="width: 100px; text-align: center;">
                        </th>
                        <th>
                            <input type="number" disabled value="1" min="1" name="total[]"
                                id="total-1" style="width: 100px; text-align: center;">

                        </th>
                        <th>
                            <button class="btn rounded-pill btn-danger waves-effect waves-light delete-sup"
                                data-id="1">
                                <span class="svg-icon svg-icon-3">
                                    <span class="svg-icon svg-icon-3">
                                        <i class="fa fa-trash"></i>
                                    </span>
                                </span>
                            </button>
                        </th>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th style=" background-color: #6c757d;color: white">المديونية السابقة</th>
                        <th style=" background-color: #6c757d;"> <input type="number" readonly id="initial_balance">
                        </th>
                        <th style=" background-color: #6c757d;color: white">المديونية بعد الفاتورة</th>
                        <th style=" background-color: #6c757d;"><input type="number" readonly
                                id="balance_after_sale"></th>
                        <th style=" background-color: #6c757d;color: white">الاجمالي</th>
                        <th id="total_productive_sale_price" style=" background-color: #6c757d;color: white">1
                        </th>
                        <th style=" background-color: #6c757d;color: white">نسبة الخصم الكلية</th>
                        <th style=" background-color: #6c757d;color: white">
                            <input type="number" id="total_discount" value="0" min="0" max="99"
                                name="total_discount" style="width: 100px; text-align: center;"
                                onkeyup="totalAfterDiscount()">
                            <!-- Adjusted width -->
                        </th>
                        <th style=" background-color: #6c757d;color: white"> الاجمالي
                            بعد الخصم الكلي</th>
                        <th style=" background-color: #6c757d;color: white">
                            <input type="text" id="total_after_discount" value="" name="total_discount"
                                style="width: 100px; text-align: center;" disabled> <!-- Adjusted width -->
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <button id="addNewDetails" class="btn btn-primary navigable">اضافة منتج اخر

            </button>
        </div>


    </div>

    <button form="form" type="submit" id="submit" class="btn btn-primary">
        <span class="indicator-label">اتمام</span>
    </button>

</form>
