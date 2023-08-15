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
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
	Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
	Route::get('/settings', 'App\Http\Controllers\SettingsController@index')->name('settings.index');
	Route::put('/settings', 'App\Http\Controllers\SettingsController@update')->name('settings.update');
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::resource('customers', 'App\Http\Controllers\CustomerController');
	Route::post('/add/customer', 'App\Http\Controllers\CustomerController@addCustomer')->name('customer.add');
	Route::get('/customer/{id}', 'App\Http\Controllers\CustomerController@getCustomer')->name('customer.get');
	Route::post('/customer/{id}', 'App\Http\Controllers\CustomerController@updateCustomer')->name('customer.update');
	Route::get('/customers-list', 'App\Http\Controllers\CustomerController@getCustomersList')->name('customers.list');
	Route::resource('items', 'App\Http\Controllers\ItemController');
	Route::post('/add/item', 'App\Http\Controllers\ItemController@addItem')->name('item.add');
	Route::get('/item/{id}', 'App\Http\Controllers\ItemController@getItem')->name('item.get');
	Route::post('/item/{id}', 'App\Http\Controllers\ItemController@updateItem')->name('item.update');
	Route::get('/items-list', 'App\Http\Controllers\ItemController@getItemsList')->name('items.list');
	Route::resource('invoices', 'App\Http\Controllers\InvoiceController');
	Route::post('/add/invoice', 'App\Http\Controllers\InvoiceController@addInvoice')->name('invoice.add');
	Route::get('/invoice/{id}', 'App\Http\Controllers\InvoiceController@getInvoice')->name('invoice.get');
	Route::post('/invoice/{id}', 'App\Http\Controllers\InvoiceController@updateInvoice')->name('invoice.update');
	Route::get('/invoices-list', 'App\Http\Controllers\InvoiceController@getInvoicesList')->name('invoices.list');
	Route::resource('recurring-invoices', 'App\Http\Controllers\RecurringInvoiceController');
	Route::post('/add/recurring-invoice', 'App\Http\Controllers\RecurringInvoiceController@addRecurringInvoice')->name('re-invoice.add');
	Route::get('/recurring-invoice/{id}', 'App\Http\Controllers\RecurringInvoiceController@getRecurringInvoice')->name('re-invoice.get');
	Route::post('/recurring-invoice/{id}', 'App\Http\Controllers\RecurringInvoiceController@updateRecurringInvoice')->name('re-invoice.update');
	Route::resource('estimates', 'App\Http\Controllers\EstimateController');
	Route::post('/add/estimate', 'App\Http\Controllers\EstimateController@addEstimate')->name('estimate.add');
	Route::get('/estimate/{id}', 'App\Http\Controllers\EstimateController@getEstimate')->name('estimate.get');
	Route::post('/estimate/{id}', 'App\Http\Controllers\EstimateController@updateEstimate')->name('estimate.update');
	Route::get('/convert-to-invoice/{id}', 'App\Http\Controllers\EstimateController@convertToInvoice')->name('estimate.convert');
	Route::resource('payments', 'App\Http\Controllers\PaymentController');
	Route::post('/add/payment', 'App\Http\Controllers\PaymentController@addPayment')->name('payment.add');
	Route::get('/payment/{id}', 'App\Http\Controllers\PaymentController@getPayment')->name('payment.get');
	Route::post('/payment/{id}', 'App\Http\Controllers\PaymentController@updatePayment')->name('payment.update');
	Route::get('/profile', 'App\Http\Controllers\ProfileController@edit')->name('profile.edit');
	Route::put('/profile', 'App\Http\Controllers\ProfileController@update')->name('profile.update');
	Route::put('/profile/password', 'App\Http\Controllers\ProfileController@password')->name('profile.password');
});