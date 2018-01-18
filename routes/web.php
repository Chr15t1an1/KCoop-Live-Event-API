<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/


#// Consumer facing
// Redirects to live CE page
Route::get('/', function () {
	//Bugsnag::notifyError('ErrorType', 'Test Error');
    // Validate the request...
    //return redirect()->;
	return redirect('https://www.knowledgecoop.com/pages/live-ce');

});



//Show all tickets for an order
Route::get('/tickets/register/{key}','TicketsController@show');
// Update tickets with ticket info
Route::post('/tickets/register/{key}','TicketsController@update');

#// ADMIN facing
// Admin index
Route::get('/admin', 'EventsController@index')->middleware('auth');

// Get shopify products
//Ajax request to handle post info sent from admin page.
Route::post('/admin/event', 'TransactionsController@catchEvents')->middleware('auth');

//Get all archived orders form shoppify and process them.
Route::post('/admin/event/get-events', 'OrdersController@GetallOrders')->middleware('auth');


//Show all un-Archived tickets for an order
Route::get('/admin/event/{product_id}', 'EventsController@show')->middleware('auth');

//Show all tickets for an order including Archived
Route::get('/admin/event/with-archived/{product_id}', 'EventsController@showWarchived')->middleware('auth');





////Process Tickets for Archive orders
Route::get('/orders/archive-process-tickets','TicketsController@create')->middleware('auth');
////Process Archive orders
Route::get('/orders/archive-process', 'OrdersController@ProcessArchive')->middleware('auth');




### Manually Edit  Orders
Route::get('/admin/tickets/redirect/{orderId}', 'TicketsController@adminGoToOrder')->middleware('auth');
Route::get('/admin/tickets/register/{orderId}', 'TicketsController@adminShow')->middleware('auth');
Route::post('/admin/tickets/register/{orderId}', 'TicketsController@adminUpdate')->middleware('auth');




/******/
###Archive Orders
#Un-Archive individual order.
Route::post('/admin/order/unarchive/{ticketId}', 'OrdersController@unArchiveOrderandTickets')->middleware('auth');


#Archive individual order.
Route::post('/admin/order/archive/{orderId}', 'OrdersController@archiveOrderandTickets')->middleware('auth');


#Archive all old orders.
Route::post('/admin/order/archive-all/old', 'OrdersController@archiveAllOlderOrderandTickets')->middleware('auth');
/******/



////Send Email
Route::post('/admin/email/send-reminder/{id}', 'EmailController@ManualSend')->middleware('auth');




//Route::post('/admin/export/csv/{id}', 'EventsController@exportToCsv')->middleware('auth');
Route::get('/admin/export/csv/{id}', 'EventsController@exportToCsv')->middleware('auth');

//Admin routes
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
