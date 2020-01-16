<?php

use Illuminate\Support\Facades\Route;


// Home & fallback
Route::get('', 'HomeController@index')->name('index');

Route::fallback('HomeController@fallback');


// Resources
Route::get('resource/{resource}', 'ResourceIndexController@handle')->name('list');

Route::match(['get', 'post'], 'resource/{resource}/create', 'ResourceCreateController@handle')->name('create');

Route::get('resource/{resource}/{id}', 'ResourceDetailController@handle')->name('detail');

Route::match(['get', 'post'], 'resource/{resource}/{id}/edit', 'ResourceUpdateController@handle')->name('edit');

Route::match(['get', 'post'], 'resource/{resource}/{id}/delete', 'ResourceDeleteController@handle')->name('delete');


Route::match(['get', 'post'], 'resource/{resource}/action/{action}', 'ResourceActionController@handle')->name('action');


// Relations
Route::match(['get', 'post'], 'resource/{resource}/{id}/add/{related_resource}', 'AddRelatedController@handle')->name('addRelated');

Route::match(['get', 'post'], 'api/resource/{resource}/{id}/attach/{related_resource}', 'AttachRelatedController@attach')->name('attachRelated');
Route::get('resource/{resource}/{id}/detach/{related_resource}/{related_id}', 'AttachRelatedController@detach')->name('detachRelated');

Route::get('api/resource/{resource}/get-objects', 'ApiController@getObjects')->name('api.getObjects');

