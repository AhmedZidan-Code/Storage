<table id="table-details" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
    <thead>
        <tr>
            <th>#</th>
            <th>الصنف</th>
            <th>كود الصنف</th>
            <th>رقم التشغيلة</th>
            <th>سعر شراء الصنف</th>
            <th>البونص</th>
            <th>نسبة الخصم</th>
            <th>الكمية</th>
            <th>الاجمالي</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <th>{{ $row->id }}</th>
                <th>{{ $row->productive->name ?? '' }}</th>
                <th>{{ $row->productive->code ?? '' }}</th>
                <th>{{ $row->batch_number }}</th>
                <th>{{ $row->productive_buy_price }}</th>
                <th>{{ $row->bouns }}</th>
                <th>{{ $row->discount_percentage }}</th>
                <th>{{ $row->amount }}</th>
                <th>{{ $row->total }}</th>

            </tr>
        @endforeach
    </tbody>
</table>
