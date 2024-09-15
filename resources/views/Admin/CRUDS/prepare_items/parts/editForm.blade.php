<form id="form" enctype="multipart/form-data" method="POST" action="{{ route('prepare-items.update', $row->id) }}">
    @csrf
    @method('PUT')
    <div class="row my-4 g-4">
        <div class="col-md-10" style="width: 100%;">
            <table id="table-details" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                style="width: 100%;">
                <thead>
                    <tr>
                        <th> الشركة</th>
                        <th> المنتج</th>
                        <th> كود المنتج</th>
                        <th>الوحدة</th>
                        <th> الكمية</th>
                        <th>حالة تحضير الصنف</th>
                        <th>رقم التشغيلة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody id="details-container">
                    @foreach (\App\Models\SalesDetails::where('sales_id', $row->id)->get() as $key => $pivot)
                        <tr id="tr-{{ $key }}">
                            {{--                <th>1</th> --}}
                            <th>
                                <div class="d-flex flex-column mb-7 fv-row col-sm-2 " style="width: 100%;">
                                    <label for="company_id-{{ $key }}"
                                        class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                        <span class="required mr-1"> </span>
                                    </label>
                                    {{ $pivot->company->title ?? '' }}
                                    <input type="hidden" name="sales_details_id[]" value="{{ $pivot->id }}">
                                </div>
                            </th>
                            <th>
                                <div class="d-flex flex-column mb-7 fv-row col-sm-2 " style="width: 100%;">
                                    <label for="productive_id-{{ $key }}"
                                        class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                        <span class="required mr-1"> </span>
                                    </label>
                                    {{ $pivot->productive->name ?? '' }}
                                    </select>
                                </div>
                            </th>
                            <th>
                                {{ $pivot->productive_code }}
                            </th>
                            <th>
                                {{ $pivot->productive->unit->title ?? '' }}
                            </th>
                            <th>
                                <input data-id="{{ $key }}" onchange="callTotal()" onkeyup="callTotal()"
                                    type="number" value="{{ $pivot->amount }}" min="1" name="amount[]"
                                    id="amount-{{ $key }}" style="width: 100%;">

                            </th>
                            <th style="padding: 8px;">
                                <label class="switch">
                                    <input type="checkbox" class="is-prepared-toggle"
                                        id="isPreparedSwitch{{$pivot->id}}" {{$pivot->is_prepared ? 'checked' : ''}}
                                        data-id="{{$pivot->id}}">
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th style="padding: 8px;">
                                {{ $pivot->batch_number }}
                            </th>
                            <th>
                                <textarea name="notes[]"></textarea>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <button form="form" type="submit" id="submit" class="btn btn-primary">
        <span class="indicator-label">اتمام</span>
    </button>

</form>
