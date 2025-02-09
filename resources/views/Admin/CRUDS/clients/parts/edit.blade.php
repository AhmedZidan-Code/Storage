<!--begin::Form-->

<form id="form" enctype="multipart/form-data" method="POST" action="{{ route('clients.update', @$row->id) }}">
    @csrf
    @method('PUT')
    <div class="row g-4">

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="name" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الاسم</span>
            </label>
            <!--end::Label-->
            <input id="name" required type="text" class="form-control form-control-solid" name="name"
                value="{{ @$row->name }}" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="code" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الكود</span>
            </label>
            <!--end::Label-->
            <input id="code" required type="text" class="form-control form-control-solid" name="code"
                value="{{ @$row->code }}" />
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="phone" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الهاتف</span>
            </label>
            <!--end::Label-->
            <input id="phone" required type="text" class="form-control form-control-solid" name="phone"
                value="{{ @$row->phone }}" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="payment_category" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> فئة السداد</span>
            </label>

            <select id="payment_category" name="payment_category" class="form-control">
                <option selected disabled>اختر الفئة</option>
                @foreach ($paymentCategories as $value => $category)
                    <option value="{{ $value }}" {{ @$row->payment_category == $value ? 'selected' : '' }}>
                        {{ $category }}</option>
                @endforeach
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="representative_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> اختر المندوب</span>
            </label>

            <select name="representative_id" class="form-control select2 representative_id" id='representative_id'>
                <option value="{{ @$row->representative_id }}" selected>
                    {{ @$row->representative?->full_name }}</option>
            </select>

        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4 ">
            <!--begin::Label-->
            <label for="distributor_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> اختر الموزع</span>
            </label>

            <select name="distributor_id" class="form-control select2 distributor_id" id='distributor_id'>
                <option value="{{ @$row->distributor_id }}" selected>{{ @$row->distributor?->full_name }}</option>
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="tele_sales" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> التلي سيلز الصباحي</span>
            </label>

            <select id="tele_sales" name="tele_sales_am" class="form-control">
                <option selected disabled>اختر التلي سيلز الصباحي</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ @$row->tele_sales_am == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="tele_sales" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> التلي سيلز المسائي</span>
            </label>

            <select id="tele_sales" name="tele_sales_pm" class="form-control">
                <option selected disabled>اختر التلي سيلز المسائي</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ @$row->tele_sales_pm == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}</option>
                @endforeach
            </select>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="governorate_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> المحافظة</span>
            </label>

            <select id="governorate_id" name="governorate_id" class="form-control">
                <option selected disabled>اختر المحافظة</option>
                @foreach ($governorates as $governorate)
                    <option @if ($governorate->id == @$row->governorate_id) selected @endif value="{{ $governorate->id }}">
                        {{ $governorate->title }}</option>
                @endforeach
            </select>

        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="city_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> المدينة</span>
            </label>

            <select id="city_id" name="city_id" class="form-control">
                <option selected disabled>اختر المدينة </option>
                @foreach ($cities as $city)
                    <option @if ($city->id == @$row->city_id) selected @endif value="{{ $city->id }}">
                        {{ $city->title }}</option>
                @endforeach
            </select>

        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="region_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1"> المنطقة</span>
            </label>

            <select id="region_id" name="region_id" class="form-control">
                <option selected disabled>اختر المدينة أولا</option>
                @foreach ($regions as $region)
                    <option @if ($region->id == @$row->region_id) selected @endif value="{{ $region->id }}">
                        {{ $region->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="client_subscription_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">فئة خصم العميل</span>
            </label>
            <select id="client_subscription_id" name="client_subscription_id" class="form-control">
                <option selected disabled>اختر الفئة</option>
                @foreach ($subscriptions as $suscribe)
                    <option value="{{ $suscribe->id }}"
                        {{ @$row->client_subscription_id == $suscribe->id ? 'selected' : '' }}> {{ $suscribe->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="commercial_register" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">السجل التجاري</span>
            </label>
            <!--end::Label-->
            <input id="commercial_register" required type="text" class="form-control form-control-solid"
                name="commercial_register" value="{{ @$row->commercial_register }}" />
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="tax_card" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">البطاقة الضريبية</span>
            </label>
            <!--end::Label-->
            <input id="tax_card" required type="text" class="form-control form-control-solid" name="tax_card"
                value="{{ @$row->tax_card }}" />
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="previous_indebtedness" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">المديونية السابقة</span>
            </label>
            <!--end::Label-->
            <input id="previous_indebtedness" required type="number" class="form-control form-control-solid"
                name="previous_indebtedness" value="{{ @$row->previous_indebtedness }}" />
        </div>

        <div class="col-md-12 my-4">
            <label for="address"> العنوان </label>

            <div class="form-floating ">

                <textarea class="form-control " name="address" placeholder="" id="address">{{ @$row->address }}</textarea>
            </div>
        </div>




    </div>
</form>
