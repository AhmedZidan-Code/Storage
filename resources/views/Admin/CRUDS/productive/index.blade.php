@extends('Admin.layouts.inc.app')
@section('title')
    الاصناف
@endsection
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">الاصناف</h5>

                <div>
                    <button id="addBtn" class="btn btn-primary">اضافة صنف</button>
                </div>

        </div>
        <div class="card-body">
            <table id="table" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                   style="width:100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th> الكود</th>
                    <th> الوحدة</th>
                    <th> القسم</th>
                    <th> النوع</th>
                    <th> سعر شراء القطعة</th>
                    <th> سعر شراء المجموعة</th>
                    <th> سعر بيع القطعة</th>
                    <th> سعر بيع المجموعة</th>
                    <th> عدد الوحدات داخل القطعة</th>
                    <th> تاريخ الانشاء</th>
                    <th>العمليات</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="Modal" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content" id="modalContent">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2><span id="operationType"></span> صنف </h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <button class="btn btn-sm btn-icon btn-active-color-primary" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                    <!--end::Close-->
                </div>
                <!--begin::Modal body-->
                <div class="modal-body py-4" id="form-load">

                </div>
                <!--end::Modal body-->
                <div class="modal-footer">
                    <div class="text-center">
                        <button type="reset" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light me-2">
                            الغاء
                        </button>
                        <button form="form" type="submit" id="submit" class="btn btn-primary">
                            <span class="indicator-label">اتمام</span>
                        </button>
                    </div>
                </div>
            </div>

            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>

@endsection
@section('js')
    <script>
        var columns = [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'code', name: 'code'},
            {data: 'unit.title', name: 'unit.title'},
            {data: 'category.title', name: 'category.title'},
            {data: 'product_type', name: 'product_type'},
            {data: 'one_buy_price', name: 'one_buy_price'},
            {data: 'packet_buy_price', name: 'packet_buy_price'},
            {data: 'one_sell_price', name: 'one_sell_price'},
            {data: 'packet_sell_price', name: 'packet_sell_price'},
            {data: 'num_pieces_in_package', name: 'num_pieces_in_package'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ];
    </script>
    @include('Admin.layouts.inc.ajax',['url'=>'productive'])


@endsection
