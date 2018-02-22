<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Event;

class TransactionsController extends Controller
{
    public function catchPostfromShoppify(){



    //Catch Request
		$request = Request();

    $payload = $request->all();

    $payload_string = json_encode($payload);

    $payload_object = json_decode($payload_string);


//Sending Object to
    if(OrdersController::hasLiveEventorder($payload_object)){


      // dd('test');
      // dd($payload_object);

		//returning bool should be returning object.
	  //This was because it was checking for duplicate
	 //Shopify orders
	  $order_object = OrdersController::createNewOrder($payload_object);

    $order_objects = $order_object;

      if(!$order_objects){
		// Order has been processed before
		  return 'Thank you';
    }



	      TicketsController::create();
       //// Send Email to Order-er



       $a =  EmailController::send($order_objects);
    }




}

//Takes in post paylod from Shoppify Buy button API (called client side)
public function catchEvents(){
  //Take Ajax Request
  $request = Request();
  $DirtyPayload = $request->getContent();

///Function works but try carch doesnt !!!!!
try {
  // Process payload into an array with the product id as the key and product title as the value
    $payload = urldecode($DirtyPayload);
    $x = explode( '&' , $payload);
    $allClasses = [];
        foreach ($x as $class) {
        $xl = explode( '=' , $class);
        $allClasses[$xl[0]]=$xl[1];
      }
   static::saveEvents($allClasses);

   return "Items loaded sucessfully | Refresh page.";

} catch (Exception $e) {
    //return $e;

    \Bugsnag::notifyError('Error getting Shopify products', 'aaaaa'.$e);
    return "Request resulted in error";
    }
}




public function saveEvents($args)
{
 foreach ($args as $proId => $title) {
   if (Event::where('event_id', '=', $proId)->exists()) {
   continue;
   }else {
      $a = new Event;
      $a->name = $title;
      $a->event_id =$proId;
      $a->save();
   }

 }

}




}
