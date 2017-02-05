<?php

/**
 * 系统后台登录
 */
Route::group(['prefix'=>'admin','namespace'=>'Admin'], function(){
    Route::get('login', 'LoginController@index');
    Route::post('dologin', 'LoginController@dologin');
    Route::get('logout', 'LoginController@logout');
});

/**
 * 系统后台路由
 */
Route::group(['prefix'=>'admin','middleware' =>'AdminAuth','namespace'=>'Admin'], function(){
    //模板路由
    Route::get('/', 'TempProController@index');
    Route::get('temp', 'TempProController@index');
    Route::get('temp/clear', 'TempProController@clearTable');
    Route::post('temp/{id}', 'TempProController@update');
    Route::post('temp/link1/{id}', 'TempProController@setThumb');
    Route::post('temp/link2/{id}', 'TempProController@setLink');
    Route::post('temp/getshow/{id}', 'TempProController@setIsShow');
    Route::post('temp/delete/{id}', 'TempProController@forceDelete');
    Route::post('temp/bg/{id}', 'TempProController@setTempBg');
    Route::resource('temp', 'TempProController');
    Route::group(['prefix'=>'t/{tempid}'], function(){
        Route::get('layer/{layerid}', 'TLayerController@show');
        Route::post('layer/tolayer', 'TLayerController@setLayer');
        Route::post('layer/toattr', 'TLayerController@setAttr');
        Route::post('layer/totext', 'TLayerController@settext');
        Route::post('layer/toimg/{layerid}', 'TLayerController@setImg');
        Route::get('layer/cancel/{layerid}', 'TLayerController@delRedis');
        Route::get('layer/save/{layerid}', 'TLayerController@saveRedisToDB');
        Route::get('{layerid}/layer/setshow/{isshow}', 'TLayerController@setIsShow');
        Route::get('{layerid}/layer/delete', 'TLayerController@setDelete');
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
    //用户产品路由
    Route::resource('product', 'ProductController');
});