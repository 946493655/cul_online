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
    Route::get('h/s/{cate}', 'HomeController@index');       //s代表检索
    Route::resource('h', 'HomeController');
});


/**
 * 用户房间
 */
Route::group(['prefix'=>'u/{uid}','namespace'=>'Mmeber'], function(){
    Route::get('{cate}', 'HomeController@index');
    Route::resource('/', 'HomeController');
});