@extends('Admin.layouts.inc.app')
@section('title')
    اضافة عملية بيع
@endsection
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">عملية بيع</h5>


        </div>
        <div class="card-body">

            @include('Admin.CRUDS.sales.parts.form')

        </div>
    @endsection

    @section('js')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('form');
                let navigableElements = Array.from(document.querySelectorAll('.navigable'));

                function updateNavigableElements() {
                    // Re-fetch all navigable elements
                    navigableElements = Array.from(document.querySelectorAll('.navigable'));
                }

                form.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        let currentElement = event.target;

                        if (navigableElements.includes(currentElement)) {
                            let currentIndex = navigableElements.indexOf(currentElement);
                            let nextElement = navigableElements[currentIndex + 1];

                            if (nextElement) {
                                nextElement.focus();
                                if (nextElement.tagName === 'SELECT') {
                                    nextElement.click();
                                }
                            } else if (currentElement.tagName === 'BUTTON') {
                                const targetIndex = Math.max(0, navigableElements.length - 2);
                                navigableElements[targetIndex].focus();
                                currentElement.click();
                                updateNavigableElements();
                            }
                        }
                    }
                });

                // Handle adding new rows dynamically
                $(document).on('click', '#addNewDetails', function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.makeRowDetailsForSalesDetails') }}",
                        success: function(res) {
                            $('#details-container').append(res.html);
                            $("html,body").animate({
                                scrollTop: $(document).height()
                            }, 1000);

                            loadScript(res.id); // If this initializes row-specific data
                            callTotal(); // Update totals

                            // Reinitialize navigable elements to include the new row
                            updateNavigableElements();
                        },
                        error: function(data) {
                            // Handle error
                        }
                    });
                });
            });
        </script>
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

                $("#client_id").select2({
                    placeholder: 'Channel...',
                    // width: '350px',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('admin.getClients') }}',
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

                $("#productive_id-1").select2({
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
                if (!$('#client_id').val()) {
                    alert('لابد من اختيار العميل أولاً.');
                    return;
                }

                var rowId = $(this).attr('data-id');
                var id = $(this).val();
                var clientId = $('#client_id').val();
                var route = "{{ route('admin.getProductiveDetails') }}";

                $.ajax({
                    type: 'GET',
                    data: {
                        productId: id,
                        clientId: clientId
                    },
                    url: route,

                    success: function(res) {
                        $(`#productive_code-${rowId}`).val(res.code);
                        $(`#productive_sale_price-${rowId}`).val(res.productive_buy_price);
                        $(`#likely_discount-${rowId}`).val(res.active_likely_discount);
                        $(`#discount_percentage-${rowId}`).val(res.client_discount);
                        const selectElement = document.getElementById('batch_number-' + rowId);
                        let options = res.batches;
                        selectElement.innerHTML = '';
                        if (options && options.length > 0) {
                            options.forEach(option => {
                                console.log(option);

                                const opt = document.createElement('option');
                                opt.value = option.batch_number;
                                opt.textContent = option.batch_number;
                                selectElement.appendChild(opt);
                            });
                        } else {
                            const opt = document.createElement('option');
                            opt.textContent = 'لايوجد رقم تشغيلة';
                            opt.value = null;
                            selectElement.appendChild(opt);
                        }

                        callTotal();
                    },
                    error: function(data) {

                    }
                });
            });
        </script>
        <script>
            function callTotal() {
                var amounts = document.getElementsByName('amount[]');
                var prices = document.getElementsByName('productive_sale_price[]');
                var discounts = document.getElementsByName('discount_percentage[]');
                var likely_discount = document.getElementsByName('likely_discount[]');

                var total = 0;
                var subTotal = 0;
                for (var i = 0; i < amounts.length; i++) {
                    subTotal = 1;
                    var amount = amounts[i];
                    var price = prices[i];
                    var discount = parseFloat(likely_discount[i].value) - parseFloat(discounts[i].value);
                    subTotal = amount.value * price.value - (amount.value * price.value * discount / 100);
                    var rowId = amount.getAttribute('data-id');
                    $(`#total-${rowId}`).val(subTotal.toFixed(2));
                    total = total + subTotal;
                }

                $('#total_productive_sale_price').text(total.toFixed(2));
                totalAfterDiscount();
            }

            function totalAfterDiscount() {
                let total = parseFloat($('#total_productive_sale_price').text());
                let after_disc = total - (total * $('#total_discount').val() / 100);
                $('#total_after_discount').val(after_disc.toFixed(2))
                let balance = parseFloat(after_disc) + parseFloat($('#initial_balance').val());
                $('#balance_after_sale').val(balance.toFixed(2));
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
                                toastr.success(data.message);
                                setTimeout(() => location.reload(), 500);
                            } else if (data.code == 500) {
                                toastr.error(data.error);
                            } else {
                                toastr.error(data.message);
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

            $(document).on('change', '#client_id', function() {
                let CLIENT_ID = $(this).val();

                if (!CLIENT_ID) {
                    toastr.warning('من فضلك اختر عميل');
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.customerBalance') }}',
                    type: 'GET',
                    data: {
                        'client_id': CLIENT_ID,
                    },
                    beforeSend: function() {},
                    complete: function() {

                    },
                    success: function(data) {
                        $('#initial_balance').empty();
                        $('#balance_after_sale').empty();
                        $('#initial_balance').val(data.balance);
                        let total = parseFloat($('#total_after_discount').val());
                        if (total) {
                            let balance = parseFloat(total) + parseFloat(data.balance);
                            $('#balance_after_sale').val(balance);
                        } else {
                            $('#balance_after_sale').val(data.balance);
                        }

                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.message);
                    },
                    cache: false
                });
            });

            $('.selectClass').select2();
        </script>
    @endsection
