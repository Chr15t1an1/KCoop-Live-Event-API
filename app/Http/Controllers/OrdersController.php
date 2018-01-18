<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use File;
use Hash;
use App\Event;
use App\Order;

class OrdersController extends Controller
{



public static function archiveAllOlderOrderandTickets()
  {

// $check = array();
try {
// get all orders that are not currently archived.
// $allOrders = Order::all();
$allOrders = Order::whereNull('is_archived')->get();

// Loop through each order Object
    foreach ($allOrders as $order) {

      // get shopify order object
      $shopify_order_object = unserialize($order->order_object);
      // Get year of purchase
      $shopify_order_created_at_timestamp = $shopify_order_object->created_at;
      $timestamp_obj = new Carbon( $shopify_order_created_at_timestamp );
      $year = $timestamp_obj->year;

    // If year is not this year archive
      if ($year < 2018) {
          static::archiveOrderandTickets($order->id);
      }
    }


    return "Archived all past years orders.";

  } catch (\Exception $e) {
    \Bugsnag::notifyError('issue archiveAllOlderOrderandTickets', $e);
    return "Issue archiving all past years orders.";
  }

  }



public static function archiveOrderandTickets($orderID)
{

try {
  // Get all orders tickets
  $order = Order::with('tickets')->where('id', '=', $orderID)->first();

  // Mark order as archived
  $tickets = $order->tickets;
  $order->is_archived = 1;
  $order->save();

  // Mark tickets as archived
  foreach ($tickets as $ticket) {
    $ticket->is_archived = 1;
    $ticket->save();
  }

  return "Order and tickets archived";

} catch (\Exception $e) {
  \Bugsnag::notifyError('issue with archiveOrderandTickets Order ID - '.$orderID, $e);
  return "Issue with archiving order and tickets ".$orderID;
}


}



public static function unArchiveOrderandTickets($orderID)
{

try {
  // Get all orders tickets
  $order = Order::with('tickets')->where('id', '=', $orderID)->first();

  // Mark order as archived
  $tickets = $order->tickets;
  $order->is_archived = NULL;
  $order->save();

  // Mark tickets as archived
  foreach ($tickets as $ticket) {
    $ticket->is_archived = NULL;
    $ticket->save();
  }

  return "Order and tickets unarchived";

} catch (\Exception $e) {
  \Bugsnag::notifyError('issue.UNarchiving Order and tickets', $e);
  return "Issue with archiving Order and tickets";
}


}




//Checks if an order contains a live event
    public static function isLiveEventorder($singleOrderObject)
    {
        $x = $singleOrderObject;
        //Get Cart
        $cart = $x->line_items;
        //Get product ids
        $product_ids = [];
        foreach ($cart as $item) {
          $product_ids[] = $item->product_id;
         }
      //Check for qualifying purchase
      foreach ($product_ids as $line_item_id) {
        if (Event::where('event_id', '=', $line_item_id)->exists()) {
          return true;
        }

        return false;

    }
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

	 //Creates new order
    public static function createNewOrder($singleOrderObject)
    {
      $x = $singleOrderObject;


        // Check if order exists in DB
      if (Order::where('shopify_order_id', '=', $x->id)->exists()) {
	  //Cutting off check
	  return false; // should log
      }
      // Get only Products we care about
      $cart = $x->line_items;
      //Get product ids
      $All_product_ids_in_cart = [];
      $product_ids_of_live_events_in_order =[];
      foreach ($cart as $item) {
        $All_product_ids_in_cart[] = $item->product_id;
       }
      //Check for qualifying purchase
      foreach ($All_product_ids_in_cart as $line_item_id) {
      if (Event::where('event_id', '=', $line_item_id)->exists()) {
        $product_ids_of_live_events_in_order[] = $line_item_id;
      }

      // Get quantity of tickets
      $lineitems_with_Quantity = array();
      foreach ($cart as $item_in_cart) {
      $lineitems_with_Quantity[$item_in_cart->product_id] = $item_in_cart->quantity;
      }

        if(isset($x->customer->first_name)){
        $cx_name = $x->customer->first_name;
        } else{
        $cx_name = "N/A";
          };
         $a = new Order;
         //Are nullable to account for manual orders
         $a->email = $x->contact_email;
         $a->first_name = $cx_name;
         $a->shopify_order_id =$x->id;
         $a->event_id = $product_ids_of_live_events_in_order[0];
         $a->tickets_created = 0;
         $a->num_tickets = $lineitems_with_Quantity[$product_ids_of_live_events_in_order[0]];
         $a->num_tickets_claimed = 0;
         $a->registration_compleate = 0;
         $a->secret_key = str_replace("/","",Hash::make($x->id));
         $a->order_object = serialize($x);
         $a->save();
         //Returning Object for email
         return $a;

}


    }
	//Processes all historic orders

    public function ProcessArchive($filename)  {
      $contents = File::get($filename);
      // Getting all ordrs
      $orders_object = json_decode($contents);

      // Removing wrapping orders object
      $orders_object = $orders_object->orders;

      // // Check for Qualifying purchases
      $qualified_purchases = array();
        foreach ($orders_object as $key => $order) {
        if(static::isLiveEventorder($order)){
          $qualified_purchases[] = $order;
        }
      }
      // //Create orders
      foreach ($qualified_purchases as $key => $single_qualified_purchases) {
       $order_object = static::createNewOrder($single_qualified_purchases);


      # Remove send Email functionality for /admin "Refresh all orders"
    // if(is_object($order_object)){
		// EmailController::send($order_object);
		// }
      }
    }

//Get all Orders from shopify
    public function GetallOrders()
    {
      try {



      // ///Get totlal number of orders.
      $url = "https://8f2ad2acc06a59b6c9cb500028ff58bb:3c620d20a60aa74839f265d8ba6286f4@knowledgecoop-2.myshopify.com";
      $cmd = '/admin/orders/count.json';
      $order_count = file_get_contents($url.$cmd);
      $count = json_decode($order_count);
      $count = $count->count;
	  //There is a limit of 50 so we need to account for it
      $num_of_pages= ceil($count/50);
      $page = 1;
      $cmd = '/admin/orders.json';
            for ($i=0; $i <=$num_of_pages ; $i++) {
              $filename = "orders/".$page."-all-orders.txt";
              $a = '?limit=50=&page='.$page;
              $page = $page+1;
              $content = file_get_contents($url.$cmd.$a);
              File::put($filename,$content);
      	  }

      //ProcessArchive files
      $dir    = 'orders/';
      $files1 = scandir($dir);

      foreach ($files1 as $file) {
        // print_r($file."<hr/>");
        if(strlen($file)<=2){
          continue;
        }
        static::ProcessArchive($dir.$file);
      }
      //Create tickets!!!!!!
      TicketsController::create();

      return "Orders received and processed.";

    } catch (\Exception $e) {
      	\Bugsnag::notifyError('issue processing orders', $e);
      return "Error with getting processing/orders";
    }

    }

}
