<!--begin::Form-->

<form id="form" enctype="multipart/form-data" method="POST" action="{{ route('productive.store') }}">
    @csrf
    <div class="row g-4">


        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="name" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الاسم</span>
            </label>
            <!--end::Label-->
            <input id="name" required type="text" class="form-control form-control-solid" name="name"
                value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="code" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الكود</span>
            </label>
            <!--end::Label-->
            <input id="code" required type="text" class="form-control form-control-solid" name="code"
                value="" />
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="limit_for_request" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">حد الاعتماد</span>
            </label>
            <!--end::Label-->
            <input id="limit_for_request" required type="text" class="form-control form-control-solid" name="limit_for_request"
                value="0" />
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="limit_for_sale" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">حد البيع</span>
            </label>
            <!--end::Label-->
            <input id="limit_for_sale" required type="text" class="form-control form-control-solid" name="limit_for_sale"
                value="0" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="category_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">التصنيف </span>
            </label>

            <select id="category_id" name="category_id" class="form-control">
                <option selected disabled>اختر التصنيف</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"> {{ $category->title }}</option>
                @endforeach
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="unit_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الوحدة </span>
            </label>

            <select id="unit_id" name="unit_id" class="form-control">
                <option selected disabled>اختر الوحدة</option>
                @foreach ($unites as $unit)
                    <option value="{{ $unit->id }}"> {{ $unit->title }}</option>
                @endforeach
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="one_buy_price" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">سعر الجمهور </span>
            </label>
            <!--end::Label-->
            <input id="audience_price" required type="text" min="0" class="form-control form-control-solid"
                name="audience_price" value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3 ">
            <label for="company_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> الشركة</span>
            </label>
            <select class="companies" name="company_id" id='company_id' style="width: 100%;">
                <option selected disabled>- ابحث عن الشركة -</option>
            </select>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-3 ">
            <label for="company_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> شكل المنتج</span>
            </label>
            <select class="shapes" name="shape_id" id='shape_id' style="width: 100%;">
                <option selected disabled>- ابحث عن الشكل -</option>
            </select>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3 ">
            <label for="zone_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> Zone</span>
            </label>
            <select id="zone_id" name="zone_id" class="form-control">
                <option selected disabled>Zone</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}"> {{ $zone->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-3 ">
            <label for="zone_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> الرف</span>
            </label>
            <select class="form-control" id="city_id" name="zones_setting_id">
                <option value="">اختر الرف</option>
            </select>
        </div>

    </div>
</form>
<script>
$(document).on('change', '#zone_id', function () {
    let parentId = $(this).val(); // Get the selected value from the parent select
    if (parentId) {
        $.ajax({
            url: '{{ route("admin.getChildCities") }}', // Replace with your route
            type: 'GET',
            data: { zone_id: parentId }, // Send the parent_id as a query parameter
            success: function (response) {
                let $childSelect = $('#city_id'); // Target the child select element
                $childSelect.empty(); // Clear previous options
                $childSelect.append('<option value="">اختر المدينة</option>'); // Add a placeholder
                
                // Populate new options
                $.each(response.data, function (index, item) {
                    $childSelect.append('<option value="' + item.id + '">' + item.title + '</option>');
                });
            },
            error: function (xhr) {
                toastr.error("Error fetching data:", xhr.responseText);
            }
        });
    } else {
        $('#city_id').empty().append('<option value="">اختر المدينة</option>'); // Reset if no parent selected
    }
});

</script>
