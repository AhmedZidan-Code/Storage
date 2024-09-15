<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <!-- App Search-->
            <ul class="metismenu list-unstyled">
                <li>
                    <form class="app-search d-none d-lg-block">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <div class="position-relative">
                            <input type="text" id="myInput" onkeyup="myFunction()" class="form-control"
                                   placeholder="ابحث هنا ..." onchange="SearchP($(this))">
                            <span class="fa fa-search"></span>
                        </div>
                    </form>
                </li>
            </ul>
            <ul class="metismenu list-unstyled " id="side-menu">


                <li>
                    <a href="{{ route('admin.index') }}" class="waves-effect">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-user-secret"></i>
                        <span>  المستخدمين </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('admins.index')}}"><i class="mdi mdi-album"></i>
                                <span> المستخدمين</span></a></li>
                        <li><a href="{{route('roles.index')}}"><i class="mdi mdi-album"></i> <span>الادوار </span></a>
                        </li>


                    </ul>
                </li>




                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-cog"></i>
                        <span>  الاعدادات </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('settings.index')}}"><i class="mdi mdi-album"></i>
                                <span> اعدادات البرنامج</span></a></li>
                        <li><a href="{{route('countries.index')}}"><i class="mdi mdi-album"></i> <span>المحافظات </span></a>
                        </li>
                        <li><a href="{{route('provinces.index')}}"><i class="mdi mdi-album"></i> <span>المدن </span></a>
                        </li>
                        <li><a href="{{route('categories.index')}}"><i class="mdi mdi-album"></i> <span>الاقسام </span></a>
                        </li>
                        <li><a href="{{route('unites.index')}}"><i class="mdi mdi-album"></i> <span>الوحدات </span></a>
                        </li>
                    </ul>
                </li>


                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-code-branch"></i>
                        <span>  الفروع والمخازن </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('branches.index')}}"><i class="mdi mdi-album"></i>
                                <span>  الفروع</span></a></li>
                        <li><a href="{{route('storages.index')}}"><i class="mdi mdi-album"></i> <span>المخازن </span></a>
                        </li>
                        <li><a href="{{route('prepare-items.index')}}"><i class="mdi mdi-album"></i> <span>تحضير الاصناف </span></a>
                        </li>

                    </ul>
                </li>




                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-user"></i>
                        <span>  العملاء   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('clients.index')}}"><i class="mdi mdi-album"></i>
                                <span>  العملاء</span></a></li>
                        <li><a href="{{route('esalat.index')}}"><i class="mdi mdi-album"></i> <span>  ايصلات العميل </span></a>
                        </li>
                        <li><a href="{{route('admin.customerAccountStatements')}}"><i class="mdi mdi-album"></i> <span>كشف حساب عميل </span></a>
                        </li>

                    </ul>
                </li>



                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-user-check"></i>
                        <span>  الموردين   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('suppliers.index')}}"><i class="mdi mdi-album"></i>
                                <span>  الموردين</span></a></li>
                        <li><a href="{{route('supplier_vouchers.index')}}"><i class="mdi mdi-album"></i> <span>  ايصلات المورد </span></a>
                        </li>
                        <li><a href="{{route('admin.supplierAccountStatements')}}"><i class="mdi mdi-album"></i> <span>كشف حساب مورد </span></a>
                        </li>

                    </ul>
                </li>




                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-list"></i>
                        <span>  الاصناف   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('productive.index')}}"><i class="mdi mdi-album"></i>
                                <span>  الاصناف</span></a></li>
                        <li><a href="{{route('rasied_ayni.index')}}"><i class="mdi mdi-album"></i> <span>   رصيد اول مدة </span></a>
                        </li>


                    </ul>
                </li>




                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-shopping-cart"></i>
                        <span>  المشتريات   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('purchases.index')}}"><i class="mdi mdi-album"></i>
                                <span>  المشتريات</span></a></li>
                        <li><a href="{{route('purchasesBills.index')}}"><i class="mdi mdi-album"></i> <span>     تقرير المشتريات </span></a>
                        </li>


                    </ul>
                </li>




                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-download"></i>
                        <span>  التصنيع   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('itemInstallations.index')}}"><i class="mdi mdi-album"></i>
                                <span>  تكوين الاصناف</span></a></li>
                        <li><a href="{{route('productions.index')}}"><i class="mdi mdi-album"></i> <span>      الانتاج </span></a>
                        </li>


                    </ul>
                </li>




                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-money-bill"></i>
                        <span>  المبيعات   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('sales.index')}}"><i class="mdi mdi-album"></i>
                                <span>   المبيعات</span></a></li>
                        <li><a href="{{route('salesBills.index')}}"><i class="mdi mdi-album"></i> <span>      تقارير المبيعات </span></a>
                        </li>


                    </ul>
                </li>





                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fa fa-money-bill"></i>
                        <span>  المرتجعات   </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('head_back_sales.index')}}"><i class="mdi mdi-album"></i>
                                <span>   مرتجع المبيعات</span></a></li>
                        <li><a href="{{route('head_back_purchases.index')}}"><i class="mdi mdi-album"></i> <span>       مرتجع المشتريات </span></a>
                        </li>



                    </ul>
                </li>








                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('destruction.index')}}">
                        <i class="fa fa-industry"></i>
                        <span>   الاهلاك </span>
                    </a>
                </li>







                <!--</div>-->
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->

<script>
    function myFunction() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("side-menu");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
</script>
