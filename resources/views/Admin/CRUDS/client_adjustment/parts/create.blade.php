<!--begin::Form-->

<form id="form" enctype="multipart/form-data" method="POST" action="{{ route('client-adjustments.store') }}">
    @csrf
    <div class="row g-4">

        <div class="d-flex flex-column mb-7 fv-row col-sm-3 ml-2">
            <!--begin::Label-->
            <label for="client_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> اختر العميل</span>
            </label>

            <select name="client_id" class="select2 client_id" id='client_id' style='width: 200px;'>
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="name" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">التاريخ</span>
            </label>
            <!--end::Label-->
            <input id="date" required type="date" class="form-control form-control-solid" name="date"
                value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="value" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">القيمة</span>
            </label>
            <!--end::Label-->
            <input id="value" required type="number" class="form-control form-control-solid" name="value"
                value="" />
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-3">
            <!--begin::Label-->
            <label for="type" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> النوع</span>
            </label>
            <select id='type' name="type" class="form-control">
                <option selected disabled>اختر النوع</option>
                <option value="1">زيادة</option>
                <option value="2">نقص</option>
            </select>
        </div>
    </div>
</form>
