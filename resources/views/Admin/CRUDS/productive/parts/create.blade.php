<!--begin::Form-->

<form id="form" enctype="multipart/form-data" method="POST" action="{{ route('productive.store') }}">
    @csrf
    <div class="row g-4">


        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="name" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الاسم</span>
            </label>
            <!--end::Label-->
            <input id="name" required type="text" class="form-control form-control-solid" name="name"
                value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="code" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الكود</span>
            </label>
            <!--end::Label-->
            <input id="code" required type="text" class="form-control form-control-solid" name="code"
                value="" />
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="category_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">القسم </span>
            </label>

            <select id="category_id" name="category_id" class="form-control">
                <option selected disabled>اختر القسم</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"> {{ $category->title }}</option>
                @endforeach
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
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

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="one_buy_price" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">سعر شراء القطعة</span>
            </label>
            <!--end::Label-->
            <input id="one_buy_price" required type="text" min="0" class="form-control form-control-solid"
                name="one_buy_price" value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="packet_buy_price" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">سعر شراء المجموعة</span>
            </label>
            <!--end::Label-->
            <input id="packet_buy_price" required type="text" min="0" class="form-control form-control-solid"
                name="packet_buy_price" value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="one_sell_price" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">سعر بيع القطعة</span>
            </label>
            <!--end::Label-->
            <input id="one_sell_price" required type="text" min="0" class="form-control form-control-solid"
                name="one_sell_price" value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="packet_sell_price" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">سعر بيع المجموعة</span>
            </label>
            <!--end::Label-->
            <input id="packet_sell_price" required type="text" min="0" class="form-control form-control-solid"
                name="packet_sell_price" value="" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="num_pieces_in_package" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">عدد الوحدات داخل القطعة</span>
            </label>
            <!--end::Label-->
            <input id="num_pieces_in_package" required type="number" min="0"
                class="form-control form-control-solid" name="num_pieces_in_package" value="" />
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4 " style="width: 100%;">
            <label for="company_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> </span>
            </label>
            <select class="companies" name="company_id" id='company_id' style="width: 100%;">
                <option selected disabled>- ابحث عن الشركة -</option>
            </select>
        </div>
    </div>
</form>
