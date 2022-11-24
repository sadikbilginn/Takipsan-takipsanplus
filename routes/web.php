<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::pattern('locale', '[a-z]{1,2}');
Route::get('/{locale}', 'LocaleController@change')->name('locale.change');

Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
});
/*
password If it is forgotten hash pasword to create
to create
-- key:genarete please.
Route::get('/pass',  function()
{
return Hash::make('123456');
});
*/
//station routes

Route::get('license-problem', function(){
    session()->forget('license_finish_days');
    return view('errors.license-problem');
})->name('errors.license-problem');

Route::get('station/login', 'StationController@loginShow')->name('station.loginShow');
Route::post('station/login', 'StationController@login')->name('station.login');
Route::get('station/logout', 'StationController@logout')->name('station.logout');

Route::group(['middleware' => ['license']],function(){

    Route::get('read', 'StationController@index')->name('station.index');
    Route::get('read2', 'StationController@index2')->name('station.hm');
    Route::get('read3', 'StationController@index3')->name('station.ms');
    Route::get('read4', 'StationController@index4')->name('station.decathlon');
    Route::get('read5', 'StationController@index5')->name('station.hb');
    Route::get('read6', 'StationController@index6')->name('station.target');
    Route::get('read7', 'StationController@index7')->name('station.levis');
    Route::post('msdata', 'StationController@getUpcListForDatatableFromMs')->name('station.msdata');
    Route::post('reReadCarton', 'StationController@reReadCarton')->name('station.reReadCarton');

    Route::get('station/device', 'StationController@device')->name('station.device');
    Route::post('station/device', 'StationController@deviceCheck')->name('station.device.check');
    Route::post('station/setting', 'StationController@settingStore')->name('station.settingStore');
    Route::post('station/consignment', 'StationController@consignmentStore')->name('station.consignmentStore');
    Route::match(['put', 'patch'], 'station/consignment/{consignment}', 'StationController@consignmentUpdate')->name('station.consignment.update');
    Route::post('station/ajax-transactions', 'StationController@ajax')->name('station.ajax');
    Route::post('station/stationviewAjax', 'StationController@stationviewAjax')->name('station.stationviewAjax');
    Route::get('station/selectSor', 'StationController@selectSor')->name('station.selectSor');
    Route::post('station/viewSor', 'StationController@viewSor')->name('station.viewSor');

});

Auth::routes();

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::group(['middleware' => ['permission','auth']],function(){

    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/production', 'HomeController@indexProduction')->name('production');


    Route::get('tabledata', 'HomeController@tableDataAjax')->name('tdurl');

    Route::post('ajax-transactions', 'AjaxController@index')->name('ajax');

    Route::resource('settings', 'SettingsController');
    Route::post('settings/update', 'SettingsController@update')->name('settings.updateAll');

    Route::get('translation/datatable', 'TranslationController@datatable')->name('translation.datatable');
    Route::resource('translation', 'TranslationController');

    Route::resource('permission', 'PermissionController');

    Route::resource('role', 'RoleController');

    Route::get('user/datatable', 'UserController@datatable')->name('user.datatable');
    Route::resource('user', 'UserController');

    Route::get('view_screen/datatable', 'ViewScreenController@datatable')->name('view_screen.datatable');
    Route::resource('view_screen', 'ViewScreenController');

    Route::resource('xml_file_repo', 'XmlFileRepoController');

    Route::get('ms_size_detail/datatable', 'MsSizeDetailController@datatable')->name('ms_size_detail.datatable');
    Route::resource('ms_size_detail', 'MsSizeDetailController');

    Route::get('support/datatable', 'SupportController@datatable')->name('support.datatable');
    Route::resource('support', 'SupportController');

    Route::resource('menu', 'MenuController');
    Route::post('menu/sort', 'MenuController@sorting')->name('menu.sort');

    Route::get('profile', 'ProfileController@show')->name('profile.show');
    Route::get('edit_password', 'ProfileController@edit_password')->name('profile.edit_password');
    Route::match(['put', 'patch'], 'profile/{profile}', 'ProfileController@update')->name('profile.update');
    Route::match(['put', 'patch'], 'update_password/{profile}', 'ProfileController@update_password')->name('profile.update_password');

    Route::resource('read-type', 'ReadTypeController');

    Route::resource('device', 'DeviceController');
    Route::get('device/{device}/devicePassive', 'DeviceController@devicePassive')->name('device.devicePassive');
    Route::get('device/{device}/deviceActive', 'DeviceController@deviceActive')->name('device.deviceActive');

    Route::get('company/{company}/subcompany', 'CompanyController@subCompanyIndex')->name('company.subcompanyindex'); 
    Route::resource('company', 'CompanyController');
    Route::get('company/{company}/device', 'CompanyDeviceController@index')->name('company.device.index'); 
    Route::get('company/{company}/device/create', 'CompanyDeviceController@create')->name('company.device.create');
    Route::post('company/{company}/device/store', 'CompanyDeviceController@store')->name('company.device.store');
    Route::get('company/device/{device}/edit', 'CompanyDeviceController@edit')->name('company.device.edit');
    Route::get('company/device/{device}/devicePassive', 'CompanyDeviceController@devicePassive')->name('company.device.devicePassive');
    Route::get('company/device/{device}/deviceActive', 'CompanyDeviceController@deviceActive')->name('company.device.deviceActive');

    Route::get('company/device/{device}', 'CompanyDeviceController@show')->name('company.device.show');
    Route::match(['put', 'patch'], 'company/device/{device}','CompanyDeviceController@update')->name('company.device.update');
    Route::delete('company/device/{device}', 'CompanyDeviceController@destroy')->name('company.device.destroy');

    Route::resource('consignee', 'ConsigneeController');

    Route::get('order/datatable', 'OrderController@datatable')->name('order.datatable');
    Route::post('order/datatable/details','OrderController@datatableDetails')->name('order.datatable.details');
    Route::resource('order','OrderController');

    Route::get('consignment/datatable', 'ConsignmentController@datatable')->name('consignment.datatable');
    Route::get('consignment/status/{consignment}', 'ConsignmentController@status')->name('consignment.status');
    Route::post('consignment/viewSor', 'ConsignmentController@viewSor')->name('consignment.viewSor');
    Route::post('consignment/getCompanyConsignees', 'ConsignmentController@getCompanyConsignees')->name('consignment.getCompanyConsignees');
    Route::get('consignment/packageZara', 'ConsignmentController@packageZara')->name('consignment.packageZara');
    Route::get('consignment/packageHm', 'ConsignmentController@packageHm')->name('consignment.packageHm');
    Route::get('consignment/packageMs', 'ConsignmentController@packageMs')->name('consignment.packageMs');
    Route::get('consignment/packageLevis', 'ConsignmentController@packageLevis')->name('consignment.packageLevis');
    Route::resource('consignment', 'ConsignmentController');

    /*
    * Rapor Çıktıları
    */
    Route::get('reports','ReportsController@index')->name('reports.index');
    Route::get('reports/package/pdf/{consignmentId}','ReportsController@exportPackagePdf')->name('reports.package.pdf');
    Route::get('reports/package-ms/pdf/{consignmentId}','ReportsController@exportPackageMsExcel')->name('reports.packageMs.pdf');
    Route::get('reports/hnm/pdf/{consignmentId}','ReportsController@exportHNMPdf')->name('reports.hnm.pdf');
    Route::get('reports/hnm-new/pdf/{consignmentId}','ReportsController@exportHNMNewPdf');
    Route::get('reports/model/pdf/{consignmentId}','ReportsController@exportModelPdf')->name('reports.model.pdf');
    Route::get('reports/model-hm/pdf/{consignmentId}','ReportsController@exportModelHmPdf')->name('reports.modelHm.pdf');
    Route::get('reports/model-ms/pdf/{consignmentId}','ReportsController@exportModelMsPdf')->name('reports.modelMs.pdf');
    Route::get('reports/model-hb/pdf/{consignmentId}','ReportsController@exportModelHbPdf')->name('reports.modelHb.pdf');
    Route::get('reports/model-levis/pdf/{consignmentId}','ReportsController@exportModelLevisPdf')->name('reports.modelLevis.pdf');
    Route::get('reports/epc/csv/{consignmentId}','ReportsController@exportEpcCsv')->name('reports.epc.csv');
    Route::get('reports/epc/pdf/{consignmentId}','ReportsController@exportEpcPdf')->name('reports.epc.pdf');
    Route::get('reports/epc-ms/pdf/{consignmentId}','ReportsController@exportEpcMsPdf')->name('reports.epcMs.pdf');
    Route::get('reports/epc/pdfAsc/{consignmentId}','ReportsController@exportEpcPdfAsc')->name('reports.epc.pdfAsc');
    Route::get('reports/epc/pdfCheck/{consignmentId}','ReportsController@exportEpcPdfCheck')->name('reports.epc.pdfCheck');
    Route::get('reports/gtin/pdf/{consignmentId}','ReportsController@exportGtinPdf')->name('reports.gtin.pdf');
    Route::get('reports/deleted-package/pdf/{consignmentId}','ReportsController@exportDeletedPackagePdf')->name('reports.deleted-package.pdf');
    Route::get('reports/deleted-package-ms/pdf/{consignmentId}','ReportsController@exportDeletedPackageMsPdf')->name('reports.deleted-package-ms.pdf');
    Route::resource('/custompermission','CustomPermissionController');

    /*
    * Test Controller
    */
    Route::get('/testPdf','TestController@index')->name('testPdf');
    Route::get('/testPdf/oku','TestController@oku')->name('oku');
    Route::get('/testPdf/bol','TestController@bol')->name('bol');
    Route::get('/testPdf/gonder','TestController@gonder')->name('testgonder');
    Route::get('/test/xml','TestController@xml')->name('xml');



});
Route::prefix('atma')->group(function(){
    Route::resource('site','SiteController',[
        'names' => [
            'index' => "atma.site.index"
        ]
    ]);
    Route::get('tenants','TenantController@viewTenants');
    Route::get('organization','OrganizeController@viewOrganization');
});

Route::get('/consignmentApi', 'ConsignmentsApiController@index');

Route::resource('license', 'LicenseController')->except('show');
Route::get('no-license', 'LicenseController@noLicense')->name('license.noLicense');
Route::get('license/licensed-excel', 'LicenseController@licensedExcel')->name('license.licensedExcel');
Route::get('license/not-licensed-excel', 'LicenseController@notLicensedExcel')->name('license.notLicensedExcel');
