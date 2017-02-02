<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::post('api/v1', function(){echo json_encode([]);exit;});


/**
 * 作品大厅
 */
Route::group(['prefix'=>'/','namespace'=>'Home'], function(){
    Route::get('/', 'HomeController@index');
    Route::get('s/{cate}', 'HomeController@index');       //s代表检索
    Route::resource('h', 'HomeController');
});


/**
 * 用户登录
 */
Route::group(['prefix'=>'login'], function(){
    Route::get('/', 'LoginController@index');
    Route::post('dologin', 'LoginController@dologin');
    Route::get('logout', 'LoginController@logout');
});

/**
 * 用户房间
 */
Route::group(['prefix'=>'u','middleware' =>'MemberAuth','namespace'=>'Member'], function(){
    Route::get('product/s/{cate}', 'HomeController@index');
    Route::get('product/apply/{tempid}', 'HomeController@getApply');
    Route::post('product/{id}', 'HomeController@update');
    Route::post('product/link/{id}', 'HomeController@set2Link');
    Route::get('product/delete/{id}', 'HomeController@forceDelete');
    Route::resource('product', 'HomeController');
});


/**************************************************/

/**
 * 系统后台登录
 */
Route::group(['prefix'=>'admin','namespace'=>'Admin'], function(){
    Route::get('login', 'LoginController@index');
    Route::post('dologin', 'LoginController@dologin');
    Route::get('logout', 'LoginController@logout');
});

/**
 * 系统后台
 */
Route::group(['prefix'=>'admin','middleware' =>'AdminAuth','namespace'=>'Admin'], function(){
    Route::get('/', 'TempProController@index');
    Route::get('temp/clear', 'TempProController@clearTable');
    Route::post('temp/{id}', 'TempProController@update');
    Route::post('temp/link/{id}', 'TempProController@set2Link');
    Route::post('temp/isshow/{id}/{isshow}', 'TempProController@setIsShow');
    Route::post('temp/delete/{id}', 'TempProController@forceDelete');
    Route::resource('temp', 'TempProController');
    Route::group(['prefix'=>'t/{tempid}'], function(){
        Route::get('layer/{layerid}', 'TLayerController@show');
        Route::post('layer/tolayer', 'TLayerController@setLayer');
        Route::post('layer/toattr', 'TLayerController@setAttr');
        Route::post('layer/totext', 'TLayerController@settext');
        Route::post('layer/toimg/{layerid}', 'TLayerController@setImg');
        Route::get('layer/cancel/{layerid}', 'TLayerController@delRedis');
        Route::get('layer/save/{layerid}', 'TLayerController@saveRedisToDB');
        Route::get('{layerid}/layer', 'TLayerController@index');
        Route::resource('layer', 'TLayerController');
    });
    Route::get('temp/preview/{id}', 'TempProController@getPreview');
    Route::get('temp/preview/layers/{id}', 'TempProController@getPreTemp');
    Route::group(['prefix'=>'t/{tempid}/{layerid}'], function(){
        Route::post('frame/toattr', 'TFrameController@selAttr');
        Route::post('frame/setval', 'TFrameController@setKeyVal');
        Route::get('frame/cancel', 'TFrameController@delRedis');
        Route::get('frame/save', 'TFrameController@saveRedisToDB');
        Route::post('frame/delete', 'TFrameController@delete');
        Route::resource('frame', 'TFrameController');
        Route::get('prelayer', 'TFrameController@getPreLayer');
        Route::get('keyvals', 'TFrameController@getKeyVals');
    });
});