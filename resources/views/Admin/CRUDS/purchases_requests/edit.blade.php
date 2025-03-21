@extends('Admin.layouts.inc.app')
@section('title')
    تعديل طلب شراء
@endsection
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">طلب الشراء</h5>


        </div>
        <div class="card-body">

            @include('Admin.CRUDS.purchases_requests.parts.editForm')

        </div>
    @endsection

    @section('js')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            (function() {

                $("#storage_id").select2({
                    placeholder: 'Channel...',
                    // width: '350px',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('admin.getStorages') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
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
            (function() {

                $("#supplier_id").select2({
                    placeholder: 'Channel...',
                    // width: '350px',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('admin.getSupplier') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
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
            (function() {

                $("#productive_id").select2({
                    placeholder: 'Channel...',
                    // width: '350px',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('admin.getProductiveTypeKham') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
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
            $(document).on('click', '.delete-sup', function(e) {
                e.preventDefault();
                var rowId = $(this).attr('data-id');
                $(`#tr-${rowId}`).remove();
                callTotal();
            })
        </script>


        <script>
            $(document).on('click', '#addNewDetails', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.makeRowDetailsForPurchasesDetails') }}",

                    success: function(res) {

                        $('#details-container').append(res.html);
                        $("html,body").animate({
                            scrollTop: $(document).height()
                        }, 1000);


                        loadScript(res.id);
                        callTotal();


                    },
                    error: function(data) {
                        // location.reload();
                    }
                });


            })
        </script>



        <script>
            function loadScript(id) {
                $(`#productive_id-${id}`).select2({
                    placeholder: 'searching For Supplier...',
                    // width: '350px',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('admin.getProductiveTypeKham') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }
                });

            }
        </script>

        <script>
            $(document).on('change', '.changeKhamId', function() {

                var rowId = $(this).attr('data-id');
                var id = $(this).val();
                var route = "{{ route('admin.getProductiveDetailsForPurchase', ':id') }}";
                route = route.replace(':id', id);

                $.ajax({
                    type: 'GET',
                    url: route,

                    success: function(res) {


                        $(`#unit-${rowId}`).val(res.unit);
                        $(`#productive_code-${rowId}`).val(res.code);
                        $(`#productive_buy_price-${rowId}`).val(res.productive_buy_price);
                        callTotal();

                    },
                    error: function(data) {
                        // location.reload();
                    }
                });

            })
        </script>
        <script>
            function callTotal() {

                var amounts = document.getElementsByName('amount[]');
                var prices = document.getElementsByName('productive_buy_price[]');
                var discounts = document.getElementsByName('discount_percentage[]');

                var total = 0;
                var subTotal = 0;
                for (var i = 0; i < amounts.length; i++) {
                    subTotal = 1;
                    var amount = amounts[i];
                    var price = prices[i];
                    var discount = discounts[i];
                    subTotal = amount.value * price.value - (amount.value * price.value * discount.value / 100);
                    var rowId = amount.getAttribute('data-id');
                    $(`#total-${rowId}`).val(subTotal);
                    total = total + subTotal;
                }
                $('#total_productive_buy_price').text(total);
                totalAfterDiscount();
                changeDiscount(rowId);

            }

            function totalAfterDiscount() {
                let total = parseFloat($('#total_productive_buy_price').text());
                $('#total_after_discount').val(total - (total * $('#total_discount').val() / 100))
            }
        </script>
        <script>
            $(document).on('submit', "#form", function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                var url = $('#form').attr('action');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {


                        $('#submit').html('<span class="spinner-border spinner-border-sm mr-2" ' +
                            ' ></span> <span style="margin-left: 4px;">{{ trans('admin.working') }}</span>'
                        ).attr('disabled', true);
                    },
                    complete: function() {},
                    success: function(data) {
                        window.setTimeout(function() {
                            $('#submit').html('{{ trans('admin.submit') }}').attr('disabled',
                                false);

                            if (data.code == 200) {
                                toastr.success(data.message)
                            } else if (data.code == 500) {
                                toastr.error(data.error); // Error message for code 500
                            } else {
                                toastr.error(data.message); // General error message
                            }
                        }, 1000);


                    },
                    error: function(data) {

                        $('#submit').html('{{ trans('admin.submit') }}').attr('disabled', false);
                        if (data.status === 500) {
                            toastr.error('{{ trans('admin.error') }}')
                        }
                        if (data.status === 422) {
                            var errors = $.parseJSON(data.responseText);

                            $.each(errors, function(key, value) {
                                if ($.isPlainObject(value)) {
                                    $.each(value, function(key, value) {
                                        toastr.error(value)
                                    });

                                } else {

                                }
                            });
                        }
                        if (data.status == 421) {
                            toastr.error(data.message)
                        }

                    }, //end error method

                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        </script>



        <script>
            @foreach (\App\Models\PurchasesDetails::where('purchases_id', $row->id)->get() as $key => $pivot)

                loadScript({{ $key }})
            @endforeach
        </script>
        <script>
            function changeDiscount(id) {
                if (!$('#productive_id-' + id).val()) {
                    $('#first_discount-' + id).val(0);
                    $('#second_discount-' + id).val(0);
                    $('#likely_discount-' + id).val(0);
                    return;
                }
                let first_discount = parseFloat($('#first_discount-' + id).val()) || 0;
                let second_discount = parseFloat($('#second_discount-' + id).val()) || 0;
                let totalDiscount = first_discount + second_discount;
                let likely_discount = parseFloat($('#likely_discount-' + id).val()) || 0;
                let tax = parseFloat($('#tax-' + id).val()) || 0;
                let productTotal = parseFloat($('#total-' + id).val()) || 0;
                let originalPrice = parseFloat($('#productive_buy_price-' + id).val()) * parseFloat($('#amount-' + id).val()) ||
                    0;
                let afterDiscount = productTotal - (productTotal / 100) * totalDiscount;
                let LIKELYDISCOUNT = ((originalPrice - afterDiscount) / originalPrice) * 100;
                console.log(LIKELYDISCOUNT);
                console.log(originalPrice);
                console.log(afterDiscount);

                let afterTaxsAndDiscount = afterDiscount - tax;
                $('#likely_discount-' + id).val(LIKELYDISCOUNT.toFixed(2));
                $('#total-' + id).val(afterTaxsAndDiscount);
                let totalForAll = document.getElementsByName('total[]');
                let sumTotal = 0;
                for (var i = 0; i < totalForAll.length; i++) {
                    sumTotal += parseFloat(totalForAll[i].value) || 0;
                }
                console.log(sumTotal);

                $('#total_productive_buy_price').text(sumTotal);
                totalAfterDiscount();

            }
        </script>
    @endsection
