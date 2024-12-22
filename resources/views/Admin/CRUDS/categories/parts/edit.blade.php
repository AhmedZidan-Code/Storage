<!--begin::Form-->

<form id="form" enctype="multipart/form-data" method="POST" action="{{route('categories.update',$row->id)}}">
    @csrf
    @method('PUT')
    <div class="row g-4">


        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <!--begin::Label-->
            <label for="title" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">الاسم</span>
            </label>
            <!--end::Label-->
            <input id="title" required type="text" class="form-control form-control-solid"  name="title" value="{{$row->title}}"/>
        </div>



        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <!--begin::Label-->
            <label for="from_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">التصنيف الرئيسي</span>
            </label>

            <select id="from_id" name="from_id" class="form-control">
                <option selected disabled>اختر </option>
                @foreach($categories as $category)
                    <option @if($row->from_id==$category->id) selected @endif value="{{$category->id}}"> {{$category->title}}</option>
                @endforeach
            </select>

        </div>




    </div>
</form>
