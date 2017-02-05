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


/*********
 * 系统后台
 */
include('Routes/routes_admin.php');