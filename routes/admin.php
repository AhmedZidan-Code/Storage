<?php


use App\Http\Controllers\Admin\{AuthController, HomeController,};
use Illuminate\Support\Facades\Route;

Route::get('admin/login', [AuthController::class, 'loginView'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'postLogin'])->name('admin.postLogin');


Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {


    Route::get('/', [HomeController::class,'index'])->name('admin.index');

    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');


    ### admins

    Route::resource('admins', \App\Http\Controllers\Admin\AdminController::class);
    Route::get('activateAdmin',[App\Http\Controllers\Admin\AdminController::class,'activate'])->name('admin.active.admin');


    ### roles
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);//setting


    ### unites
    Route::resource('unites', \App\Http\Controllers\Admin\UnitController::class);//setting



    ### settings
    Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class);//setting

    ### categories

    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);//setting


    ### productive

    Route::resource('productive', \App\Http\Controllers\Admin\ProductiveController::class);//setting


    ### branches

    Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);//setting

    ### storages

    Route::resource('storages', \App\Http\Controllers\Admin\StorageController::class);//setting

    ### rasied_ayni

    Route::resource('rasied_ayni', \App\Http\Controllers\Admin\RasiedAyniController::class);//setting

    Route::get('rasied_ayni_for_productive/{id}', [\App\Http\Controllers\Admin\RasiedAyniController::class,'rasied_ayni_for_productive'])->name('admin.rasied_ayni_for_productive');//setting
    Route::get('getStorageForBranch/{id}', [\App\Http\Controllers\Admin\RasiedAyniController::class,'getStorageForBranch'])->name('admin.getStorageForBranch');//setting
    Route::get('gitCreditForProductive/{id}', [\App\Http\Controllers\Admin\RasiedAyniController::class,'gitCreditForProductive'])->name('admin.gitCreditForProductive');//setting



    ### countries
    Route::resource('countries', \App\Http\Controllers\Admin\Area\CountryController::class);//setting


    ### categories
    Route::resource('provinces', \App\Http\Controllers\Admin\Area\ProvinceController::class);//setting


    ### clients
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);//setting

    Route::get('getCitiesForGovernorate/{id}', [\App\Http\Controllers\Admin\ClientController::class,'getCitiesForGovernorate'])->name('admin.getCitiesForGovernorate');//setting


    ### suppliers
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);//setting

    ### item_installations

    Route::resource('itemInstallations', \App\Http\Controllers\Admin\ItemInstallationController::class);//setting
    Route::get('makeRowDetailsForItemInstallations', [\App\Http\Controllers\Admin\ItemInstallationController::class,'makeRowDetailsForItemInstallations'])->name('admin.makeRowDetailsForItemInstallations');//setting
    Route::get('getSubProductive', [\App\Http\Controllers\Admin\ItemInstallationController::class,'getSubProductive'])->name('admin.getSubProductive');//setting
    Route::get('getProductiveDetails/{id}', [\App\Http\Controllers\Admin\ItemInstallationController::class,'getProductiveDetails'])->name('admin.getProductiveDetails');//setting
    Route::get('getProductiveTypeKham', [\App\Http\Controllers\Admin\ItemInstallationController::class,'getProductiveTypeKham'])->name('admin.getProductiveTypeKham');//setting
    Route::get('getProductiveTypeTam', [\App\Http\Controllers\Admin\ItemInstallationController::class,'getProductiveTypeTam'])->name('admin.getProductiveTypeTam');//setting
    Route::get('getProductiveTamDetails/{id}', [\App\Http\Controllers\Admin\ItemInstallationController::class,'getProductiveTamDetails'])->name('admin.getProductiveTamDetails');//setting
    Route::get('getAllProductive', [\App\Http\Controllers\Admin\ItemInstallationController::class,'getAllProductive'])->name('admin.getAllProductive');//setting


    ### esalats
    Route::resource('esalat', \App\Http\Controllers\Admin\EsalatController::class);//setting
    Route::get('getClientForEsalat', [\App\Http\Controllers\Admin\EsalatController::class,'getClientForEsalat'])->name('admin.getClientForEsalat');//setting
    Route::get('getClientNameForEsalat\{id}', [\App\Http\Controllers\Admin\EsalatController::class,'getClientNameForEsalat'])->name('admin.getClientNameForEsalat');//setting


    Route::get('getClients', [\App\Http\Controllers\Admin\EsalatController::class,'getClients'])->name('admin.getClients');//setting


    Route::get('testing', [\App\Http\Controllers\Admin\EsalatController::class,'testing'])->name('admin.testing');//setting


    #### supplier_vouchers

    Route::resource('supplier_vouchers', \App\Http\Controllers\Admin\SupplierVoucherController::class);//setting
    Route::get('getSupplierForVouchers', [\App\Http\Controllers\Admin\SupplierVoucherController::class,'getSupplierForVouchers'])->name('admin.getSupplierForVouchers');//setting
    Route::get('getSupplierNameForVouchers\{id}', [\App\Http\Controllers\Admin\SupplierVoucherController::class,'getSupplierNameForVouchers'])->name('admin.getSupplierNameForVouchers');//setting
    Route::get('getSupplier', [\App\Http\Controllers\Admin\SupplierVoucherController::class,'getSupplier'])->name('admin.getSupplier');//setting


    ### purchases

    Route::resource('purchases', \App\Http\Controllers\Admin\PurchasesController::class);//setting
    Route::get('getPurchasesDetails/{id}', [\App\Http\Controllers\Admin\PurchasesController::class,'getPurchasesDetails'])->name('admin.getPurchasesDetails');//setting
    Route::get('getStorages', [\App\Http\Controllers\Admin\PurchasesController::class,'getStorages'])->name('admin.getStorages');//setting
    Route::get('makeRowDetailsForPurchasesDetails', [\App\Http\Controllers\Admin\PurchasesController::class,'makeRowDetailsForPurchasesDetails'])->name('admin.makeRowDetailsForPurchasesDetails');//setting


    ### head back purchases
    Route::resource('head_back_purchases', \App\Http\Controllers\Admin\HeadBackPurchasesController::class);//setting
    Route::get('getHeadBackPurchasesDetails/{id}', [\App\Http\Controllers\Admin\HeadBackPurchasesController::class,'getHeadBackPurchasesDetails'])->name('admin.getHeadBackPurchasesDetails');//setting

    ###


    Route::resource('sales', \App\Http\Controllers\Admin\SalesController::class);//setting
    Route::get('getSalesDetails/{id}', [\App\Http\Controllers\Admin\SalesController::class,'getSalesDetails'])->name('admin.getSalesDetails');//setting
    Route::get('makeRowDetailsForSalesDetails', [\App\Http\Controllers\Admin\SalesController::class,'makeRowDetailsForSalesDetails'])->name('admin.makeRowDetailsForSalesDetails');//setting



    Route::resource('head_back_sales', \App\Http\Controllers\Admin\HeadBackSalesController::class);//setting
    Route::get('getHeadBackSalesDetails/{id}', [\App\Http\Controllers\Admin\HeadBackSalesController::class,'getHeadBackSalesDetails'])->name('admin.getHeadBackSalesDetails');//setting



    Route::resource('productions', \App\Http\Controllers\Admin\ProductionController::class);//setting
    Route::get('getProductionDetails/{id}', [\App\Http\Controllers\Admin\ProductionController::class,'getProductionDetails'])->name('admin.getProductionDetails');//setting
    Route::get('makeRowDetailsForProductionDetails', [\App\Http\Controllers\Admin\ProductionController::class,'makeRowDetailsForProductionDetails'])->name('admin.makeRowDetailsForProductionDetails');//setting


    Route::resource('destruction', \App\Http\Controllers\Admin\DestructionController::class);//setting
    Route::get('getDestructionDetails/{id}', [\App\Http\Controllers\Admin\DestructionController::class,'getDestructionDetails'])->name('admin.getDestructionDetails');//setting
    Route::get('makeRowDetailsForDestructionDetails', [\App\Http\Controllers\Admin\DestructionController::class,'makeRowDetailsForDestructionDetails'])->name('admin.makeRowDetailsForDestructionDetails');//setting
    Route::get('getDestructionPrice', [\App\Http\Controllers\Admin\DestructionController::class,'getDestructionPrice'])->name('admin.getDestructionPrice');//setting



    ### ---------------reports ---------------reports------------------reports----------------------------reports--------------

      ###clientsAccountStatements
    Route::get('customerAccountStatements', [\App\Http\Controllers\Admin\Reports\AccountStatements\CustomerAccountStatementController::class,'index'])->name('admin.customerAccountStatements');//setting


    ###supplierAccountStatements
    Route::get('supplierAccountStatements', [\App\Http\Controllers\Admin\Reports\AccountStatements\SupplierAccountStatmentController::class,'index'])->name('admin.supplierAccountStatements');//setting


    Route::resource('purchasesBills', \App\Http\Controllers\Admin\Reports\Bills\PurchasesBillController::class);//setting


    Route::resource('salesBills', \App\Http\Controllers\Admin\Reports\Bills\salesBillController::class);//setting

});
