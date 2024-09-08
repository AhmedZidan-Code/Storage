@extends('Admin.layouts.inc.app')
@section('title')
    كشف حساب عميل
@endsection
@section('css')
@endsection


@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0 flex-grow-1">كشف حساب</h5>


                    <div class="row my-4 g-4">


                        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
                            <!--begin::Label-->
                            <label for="client_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                <span class="required mr-1">   العميل</span>
                            </label>
                            <select id='client_id' name="client_id" style='width: 200px;'>
                                <option selected disabled>- ابحث عن عملاء</option>
                            </select>
                        </div>

                    </div>

                </div>


                <div class="card-body" id="table-container">

                </div>



            </div>
        </div>
    </div>
    <!-- end row -->

@endsection
@section('js')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

        (function () {

            $("#client_id").select2({
                placeholder: 'Channel...',
                // width: '350px',
                allowClear: true,
                ajax: {
                    url: '{{route('admin.getClients')}}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
        })();
    </script>
    <script>
        $(document).on('change','#client_id',function (){
            var client_id=$(this).val();

            $.ajax({
                type: 'GET',
                url: "{{route('admin.customerAccountStatements')}}",
               data:{
                    client_id:client_id,
               },
                success: function (res) {

                  $('#table-container').html(res.html);


                },
                error: function (data) {
                    // location.reload();
                }
            });

        })
    </script>
@endsection
