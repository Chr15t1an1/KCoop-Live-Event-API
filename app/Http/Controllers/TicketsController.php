<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Ticket;
use App\Event;


class TicketsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

	 //Create tockets for all orders that have not been created
    public static function create()
    {
        //Check for all orders where tickets have not been created and create them
        $orders_with_no_tickets = Order::where('tickets_created', '=', 0)->get();

        // return $orders_with_no_tickets;
        foreach ($orders_with_no_tickets as $order) {
          // Get Number of tickets
          $num_of_ticket_in_orders = $order->num_tickets;
          $n = $num_of_ticket_in_orders;
                for ($x = 0; $x < $n; $x++) {
                      $a = new Ticket;
                      $a->event_id = $order->event_id;
                      $a->order_id = $order->id;
                      $a->is_claimed = 0;
                      $a->save();
                  }
          //Saving tickets created

          $order->tickets_created = "1";
          $order->save();

        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	// Show tickets for an Order
    public function show($key)
    {

		$order = Order::with('tickets')->where('secret_key', '=', $key)->get();
		$itemCount = $order->count();
		if($itemCount < 1){
			return response()->view('errors.missing', [], 404);
			}else{
				$tickets = $order[0]->tickets;
		return view('public.ticket',compact('tickets'));
				}
    }




	// Show tickets for an Order
    public function adminShow($orderId)
    {
		$orderObject = Order::with('tickets')->find($orderId);

		$tickets = $orderObject->tickets;
		return view('admin.adminTicket',compact('tickets'));

    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	 //Update order with ticket info

    public function update(Request $request, $key)
    {
      //Get order and tickets
      $order = Order::with('tickets')->where('secret_key', '=', $key)->get();
      	//Isolate tickets
		$tickets = $order[0]->tickets;

		//Filter out claimed tickets
		$tickets_notClaimed = array();
		foreach($tickets as $ticket){
			if ($ticket->is_claimed > 0){
				continue;
				}
			$tickets_notClaimed[] = $ticket;
			}


		$tickets = $tickets_notClaimed;

      	//Capture post data
        $postdatas = $request->all();
        //Separate the post array into tickets
        $ticket_data = array();

        foreach ($postdatas as $key => $postdata ) {
          $ticket_id = preg_replace("/[^0-9,.]/", "", $key);
          $ticket_data[$ticket_id][$key] =  $postdata;
        }

		?><pre style="display:none"> <?php ?> </pre><?php


        foreach ($tickets as $k => $ticket) {
          $ticket_object_id = $ticket->id;

          $post_data_array = $ticket_data[$ticket_object_id];

		  //Validation
		   $this->validate($request, [
        'firstname-'.$ticket_object_id => 'required',
        'lastname-'.$ticket_object_id => 'required',
		'nmls_id-'.$ticket_object_id => 'required',
		'dietary_restrictions-'.$ticket_object_id => 'required',
   				 ]);
          //Update Ticket info
          $ticket->FN = $post_data_array['firstname-'.$ticket_object_id];
           $ticket->LN = $post_data_array['lastname-'.$ticket_object_id];
           $ticket->NMLS_id = $post_data_array['nmls_id-'.$ticket_object_id];
           $ticket->dietary_restrictions = $post_data_array['dietary_restrictions-'.$ticket_object_id];
		   //New Inputs
		   $ticket->email = $post_data_array['email-'.$ticket_object_id];
           $ticket->company = $post_data_array['company-'.$ticket_object_id];


           $ticket->is_claimed = 1;
           $ticket->save();

        }

        //Check Ordeders for compleated tickets

         return view('public.thank_you');


    }




public function adminUpdate(Request $request, $orderId)
    {
		$orderObject = Order::with('tickets')->find($orderId);
		$tickets = $orderObject->tickets;


		//return $orderObject;


		/*//Filter out claimed tickets
		$tickets_notClaimed = array();
		foreach($tickets as $ticket){
			if ($ticket->is_claimed > 0){
				continue;
				}
			$tickets_notClaimed[] = $ticket;
			}
		*/

		//$tickets = $tickets_notClaimed;

      	//Capture post data
        $postdatas = $request->all();

		//return $postdatas;

        //Separate the post array into tickets
        $ticket_data = array();

        foreach ($postdatas as $key => $postdata ) {
          $ticket_id = preg_replace("/[^0-9,.]/", "", $key);
          $ticket_data[$ticket_id][$key] =  $postdata;
        }

		?><pre style="display:none"> <?php ?> </pre><?php


        foreach ($tickets as $k => $ticket) {
          $ticket_object_id = $ticket->id;

          $post_data_array = $ticket_data[$ticket_object_id];

		  //Validation
		   $this->validate($request, [
        'firstname-'.$ticket_object_id => 'required',
        'lastname-'.$ticket_object_id => 'required',
		'nmls_id-'.$ticket_object_id => 'required',
		'dietary_restrictions-'.$ticket_object_id => 'required',
   				 ]);
          //Update Ticket info
          $ticket->FN = $post_data_array['firstname-'.$ticket_object_id];
           $ticket->LN = $post_data_array['lastname-'.$ticket_object_id];
           $ticket->NMLS_id = $post_data_array['nmls_id-'.$ticket_object_id];
           $ticket->dietary_restrictions = $post_data_array['dietary_restrictions-'.$ticket_object_id];
		   //New Inputs
		   $ticket->email = $post_data_array['email-'.$ticket_object_id];
           $ticket->company = $post_data_array['company-'.$ticket_object_id];


           $ticket->is_claimed = 1;
           $ticket->save();

        }

        //Check Ordeders for compleated tickets

         return back();//view('public.thank_you');


    }




//adminGoToOrder
public function adminGoToOrder($orderId){
		$orderObject = Order::find($orderId);
		$order_id = $orderObject->shopify_order_id;
		$baseUrl = 'https://knowledgecoop-2.myshopify.com/admin/orders/';
		header('Location:'.$baseUrl.$order_id);
	}

}
