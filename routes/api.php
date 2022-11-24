<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ConsignmentsApiController;
use App\Http\Controllers\Atma\OrganizeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::apiResource('consignment', 'ConsignmentsApiController');
//Route::resource('consignmentApi/fileUpload', 'ConsignmentsApiController@store');

//Route::resource('fileUpload', 'ConsignmentsApiController');
Route::post('fileUpload', [ConsignmentsApiController::class, 'store']);

Route::prefix('/organize/organization/api')->group(function(){
    # versiyon 2.0 endpointler
    /* 
    ------> Duruma göre buradaki 2.0 endpointler kullanılabilir...
    
    
    */
    Route::get('v{version}/{organization}/{tenant}/sites',"TenantController@getAllSitesOfTenant")->name("atma.sites.list");
    Route::get('v{version}/{organization}/tenants',"TenantController@getAllTenants");
    # versiyon 2.0 endpointler
    
    # versiyon 1.0 endpointler
    
    Route::get('v{version}/{organization}',"OrganizeController@getOrganizationDetails");
    Route::get('v{version}/{organization}/{tenant}/{site}/attributes',"OrganizeController@getSiteAttribute");
    Route::get('v{version}/{organization}/{tenant}/sites/{site}',"OrganizeController@getSiteByIdentifier");
    Route::post('v{version}/{organization}/{tenant}/sites',"SiteController@createSite")->name("atma.sites.create");
    Route::post('v{version}/{organization}/{tenant}/{site}/create-attributes-requests',"OrganizeController@createSiteAttribute");
    Route::post('v{version}/{organization}/{tenant}/{site}/delete-attributes-requests',"OrganizeController@deleteSiteAttribute");
    Route::post('v{version}/{organization}/{tenant}/sites/batch-requests',"OrganizeController@getMultipleSiteByIdentifier");
    Route::put('v{version}/{organization}/{tenant}/sites/{site}',"OrganizeController@updateSite");
    
    # versiyon 1.0 endpointler
});