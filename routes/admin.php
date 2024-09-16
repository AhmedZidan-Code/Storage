<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\EsalatController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchasesController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\ProductiveController;
use App\Http\Controllers\Admin\RasiedAyniController;
use App\Http\Controllers\Admin\DestructionController;
use App\Http\Controllers\Admin\Area\CountryController;
use App\Http\Controllers\Admin\StoreManagerController;
use App\Http\Controllers\Admin\Area\ProvinceController;
use App\Http\Controllers\Admin\HeadBackSalesController;
use App\Http\Controllers\Admin\PreparingItemController;
use App\Http\Controllers\Admin\SupplierVoucherController;
use App\Http\Controllers\Admin\ItemInstallationController;
use App\Http\Controllers\Admin\HeadBackPurchasesController;
use App\Http\Controllers\Admin\Reports\Bills\SalesBillController;
use App\Http\Controllers\Admin\Reports\Bills\PurchasesBillController;
use App\Http\Controllers\Admin\Reports\AccountStatements\SupplierAccountStatmentController;
use App\Http\Controllers\Admin\Reports\AccountStatements\CustomerAccountStatementController;

// Authentication Routes
Route::get('admin/login', [AuthController::class, 'loginView'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'postLogin'])->name('admin.postLogin');

// Admin Routes Group
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('admin.index');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Admin Management
    Route::resource('admins', AdminController::class);
    Route::get('activateAdmin', [AdminController::class, 'activate'])->name('admin.active.admin');

    // Roles and Settings
    Route::resource('roles', RoleController::class);
    Route::resource('settings', SettingController::class);

    // Units and Categories
    Route::resource('unites', UnitController::class);
    Route::resource('categories', CategoryController::class);

    // Productive and Branches
    Route::resource('productive', ProductiveController::class);
    Route::resource('branches', BranchController::class);

    // Storage and Rasied Ayni
    Route::resource('storages', StorageController::class);
    Route::resource('rasied_ayni', RasiedAyniController::class);
    Route::get('rasied_ayni_for_productive/{id}', [RasiedAyniController::class, 'rasied_ayni_for_productive'])->name('admin.rasied_ayni_for_productive');
    Route::get('getStorageForBranch/{id}', [RasiedAyniController::class, 'getStorageForBranch'])->name('admin.getStorageForBranch');
    Route::get('gitCreditForProductive/{id}', [RasiedAyniController::class, 'gitCreditForProductive'])->name('admin.gitCreditForProductive');

    // Area Management
    Route::resource('countries', CountryController::class);
    Route::resource('provinces', ProvinceController::class);

    // Clients and Suppliers
    Route::resource('clients', ClientController::class);
    Route::get('getCitiesForGovernorate/{id}', [ClientController::class, 'getCitiesForGovernorate'])->name('admin.getCitiesForGovernorate');
    Route::resource('suppliers', SupplierController::class);

    // Item Installations
    Route::resource('itemInstallations', ItemInstallationController::class);
    Route::get('makeRowDetailsForItemInstallations', [ItemInstallationController::class, 'makeRowDetailsForItemInstallations'])->name('admin.makeRowDetailsForItemInstallations');
    Route::get('getSubProductive', [ItemInstallationController::class, 'getSubProductive'])->name('admin.getSubProductive');
    Route::get('getProductiveDetails/{id}', [ItemInstallationController::class, 'getProductiveDetails'])->name('admin.getProductiveDetails');
    Route::get('getProductiveTypeKham', [ItemInstallationController::class, 'getProductiveTypeKham'])->name('admin.getProductiveTypeKham');
    Route::get('getProductiveTypeTam', [ItemInstallationController::class, 'getProductiveTypeTam'])->name('admin.getProductiveTypeTam');
    Route::get('getProductiveTamDetails/{id}', [ItemInstallationController::class, 'getProductiveTamDetails'])->name('admin.getProductiveTamDetails');
    Route::get('getAllProductive', [ItemInstallationController::class, 'getAllProductive'])->name('admin.getAllProductive');

    // Esalat and Supplier Vouchers
    Route::resource('esalat', EsalatController::class);
    Route::get('getClientForEsalat', [EsalatController::class, 'getClientForEsalat'])->name('admin.getClientForEsalat');
    Route::get('getClientNameForEsalat/{id}', [EsalatController::class, 'getClientNameForEsalat'])->name('admin.getClientNameForEsalat');
    Route::get('getClients', [EsalatController::class, 'getClients'])->name('admin.getClients');
    Route::get('testing', [EsalatController::class, 'testing'])->name('admin.testing');

    Route::resource('supplier_vouchers', SupplierVoucherController::class);
    Route::get('getSupplierForVouchers', [SupplierVoucherController::class, 'getSupplierForVouchers'])->name('admin.getSupplierForVouchers');
    Route::get('getSupplierNameForVouchers/{id}', [SupplierVoucherController::class, 'getSupplierNameForVouchers'])->name('admin.getSupplierNameForVouchers');
    Route::get('getSupplier', [SupplierVoucherController::class, 'getSupplier'])->name('admin.getSupplier');

    // Purchases and Sales
    Route::resource('purchases', PurchasesController::class);
    Route::get('getPurchasesDetails/{id}', [PurchasesController::class, 'getPurchasesDetails'])->name('admin.getPurchasesDetails');
    Route::get('getStorages', [PurchasesController::class, 'getStorages'])->name('admin.getStorages');
    Route::get('makeRowDetailsForPurchasesDetails', [PurchasesController::class, 'makeRowDetailsForPurchasesDetails'])->name('admin.makeRowDetailsForPurchasesDetails');

    Route::resource('head_back_purchases', HeadBackPurchasesController::class);
    Route::get('getHeadBackPurchasesDetails/{id}', [HeadBackPurchasesController::class, 'getHeadBackPurchasesDetails'])->name('admin.getHeadBackPurchasesDetails');

    Route::resource('sales', SalesController::class);
    Route::get('getSalesDetails/{id}', [SalesController::class, 'getSalesDetails'])->name('admin.getSalesDetails');
    Route::get('makeRowDetailsForSalesDetails', [SalesController::class, 'makeRowDetailsForSalesDetails'])->name('admin.makeRowDetailsForSalesDetails');
    Route::post('sales/update-status', [SalesController::class, 'updateStatus'])->name('admin.update-sales-status');

    Route::resource('head_back_sales', HeadBackSalesController::class);
    Route::get('getHeadBackSalesDetails/{id}', [HeadBackSalesController::class, 'getHeadBackSalesDetails'])->name('admin.getHeadBackSalesDetails');

    // Production and Destruction
    Route::resource('productions', ProductionController::class);
    Route::get('getProductionDetails/{id}', [ProductionController::class, 'getProductionDetails'])->name('admin.getProductionDetails');
    Route::get('makeRowDetailsForProductionDetails', [ProductionController::class, 'makeRowDetailsForProductionDetails'])->name('admin.makeRowDetailsForProductionDetails');

    Route::resource('destruction', DestructionController::class);
    Route::get('getDestructionDetails/{id}', [DestructionController::class, 'getDestructionDetails'])->name('admin.getDestructionDetails');
    Route::get('makeRowDetailsForDestructionDetails', [DestructionController::class, 'makeRowDetailsForDestructionDetails'])->name('admin.makeRowDetailsForDestructionDetails');
    Route::get('getDestructionPrice', [DestructionController::class, 'getDestructionPrice'])->name('admin.getDestructionPrice');

    // Reports
    Route::get('customerAccountStatements', [CustomerAccountStatementController::class, 'index'])->name('admin.customerAccountStatements');
    Route::get('supplierAccountStatements', [SupplierAccountStatmentController::class, 'index'])->name('admin.supplierAccountStatements');
    Route::resource('purchasesBills', PurchasesBillController::class);
    Route::resource('salesBills', SalesBillController::class);

    // Employees and Companies
    Route::resource('employees', EmployeeController::class);
    Route::get('getCompanies', [CompanyController::class, 'getCompanies'])->name('admin.get-companies');

    // Prepare Items
    Route::resource('prepare-items', PreparingItemController::class);
    Route::post('update-status', [PreparingItemController::class, 'updateIsPrepared'])->name('update.prepare-status');
    
    Route::resource('store-managers', StoreManagerController::class);

});
