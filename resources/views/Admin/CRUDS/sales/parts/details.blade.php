<tr id="tr-{{ $id }}">
    {{--                <th>1</th> --}}
    <th>
        <div class="d-flex flex-column mb-7 fv-row col-sm-2 " style="width: 100%;">
            <label for="productive_id-{{ $id }}" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> </span>
            </label>
            <select class="changeKhamId" data-id="{{ $id }}" name="productive_id[]"
                id='productive_id-{{ $id }}'  style='width: 200px;'>
                <option selected disabled value='0'>- ابحث عن المنتج -</option>
            </select>
        </div>
    </th>
    <th>
        <input type="text" disabled id="productive_code-{{ $id }}" style="width: 100%;">
        <input data-id="{{ $id }}" type="hidden" value="" name="company_id[]"
            id="company_id-{{ $id }}">
    </th>
    <th>
        <select class="form-control select2" data-id="{{ $id }}" name="batch_number[]"
            id="batch_number-{{ $id }}" style="width: 100%;" onchange="getPrice({{ $id }})">

        </select>
    </th>
    <th>
        <input data-id="{{ $id }}" class="form-control navigable" onchange="callTotal()" type="number"
            value="1" min="1" name="amount[]" id="amount-{{ $id }}" style="width: 100%;">

    </th>
    <th>
        <input data-id="{{ $id }}" step=".1" type="number" value="1" min="1" name="productive_buy_price[]"
            id="productive_buy_price-{{ $id }}" class="form-control" style="width: 100%;">

    </th>
    <th>
        <input step=".1" data-id="{{ $id }}" class="form-control navigable" onchange="callTotal()"
            type="number" value="1" min="1" name="productive_sale_price[]"
            id="productive_sale_price-{{ $id }}" style="width: 100%;">

    </th>
    <th>
        <input type="number" class="form-control navigable" value="0" min="0" name="bouns[]"
            id="bouns-{{ $id }}" style="width: 100%;">
    </th>
    <th>
        <input type="number" class="form-control navigable" value="0" min="0" name="discount_percentage[]"
            id="discount_percentage-{{ $id }}" style="width: 100%;" onkeyup="callTotal()">
    </th>
    <th>
        <input type="number" disabled value="1" min="1" name="total[]" id="total-{{ $id }}">

    </th>
    <th>
        <button class="btn rounded-pill btn-danger waves-effect waves-light delete-sup" data-id="{{ $id }}">
            <span class="svg-icon svg-icon-3">
                <span class="svg-icon svg-icon-3">
                    <i class="fa fa-trash"></i>
                </span>
            </span>
        </button>
    </th>
</tr>
