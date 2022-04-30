<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(); // ['register' => false]

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');

    Route::get('/home', 'HomeController@index')->name('home');
    // Invoices

    Route::group(['prefix' => 'invoices'], function () {

        Route::get('paid', 'InvoicesController@paid')->name('invoices.paid');
        Route::get('unPaid', 'InvoicesController@unPaid')->name('invoices.unPaid');
        Route::get('paidPartial', 'InvoicesController@paidPartial')->name('invoices.paidPartial');
        Route::get('archive', 'InvoicesController@archive')->name('invoices.archive');
        Route::delete('archiving', 'InvoicesController@archiving')->name('invoices.archiving');

        Route::get('details/{section_id}', 'InvoicesDetailsController@edit')->name('invoices.details');
        Route::get('edit/{invoice_id}', 'InvoicesController@edit')->name('invoices.edit');
        Route::get('editPaymentStatus/{invoice_id}', 'InvoicesController@editPaymentStatus')->name('invoices.editPaymentStatus');
        Route::post('updatePaymentStatus', 'InvoicesController@updatePaymentStatus')->name('invoices.updatePaymentStatus');
        Route::get('previewPrint/{invoice_id}', 'InvoicesController@previewPrint')->name('invoices.previewPrint');
        Route::get('export', 'InvoicesController@export')->name('invoices.export');

        // Attachments
        Route::get('view_file/{invoice_number}/{file_name}', 'InvoicesDetailsController@openFile')->name('invoices.view_file');
        Route::get('download_file/{invoice_number}/{file_name}', 'InvoicesDetailsController@downloadFile')->name('invoices.download_file');
        Route::post('delete_file', 'InvoicesDetailsController@deleteFile')->name('invoices.delete_file');
        Route::post('attachments/upload', 'InvoicesAttachmentsController@upload')->name('invoices.attachments.upload');
    });

    Route::resource('invoices', 'InvoicesController');

    Route::resource('sections', 'SectionsController');
    Route::resource('products', 'ProductsController');
    Route::post('/section/{id}', 'InvoicesController@getProduct');

    Route::get('/{page}', 'AdminController@index');
});
