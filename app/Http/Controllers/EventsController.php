<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	 //Return admin index /dash
     public function index()
     {
         $events = Event::all();
         return view('admin.index',compact('events'));
     }   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 
	 //Show tickets of a specific event
    public function show($product_id)
    {
      $event = Event::with('tickets')->where('event_id', '=', $product_id)->get();
	  
	 
	  
	 	if($event->isEmpty()){
			 return response()->view('errors.missing', [], 404);
			}
	 
		
		//Strip for tickets
		$event = $event[0];
		$eventId = $event->event_id;
		//	Event Name
		$eventName = $event->name;
		
	  $tickets = $event->tickets;
		//decending
		//$tickets = $tickets->sortBy('order_id',SORT_REGULAR, true);
		//Asending
		$tickets = $tickets->sortBy('order_id');
		
		//Event Tag
		$eventTag = substr($event->name,0,7);
		
		////////
		$order_ids=array();
		foreach($tickets as $tic){
			$order_ids[] = $tic->order_id;
			}
		
		$order_ids_and_Frequancy = array_count_values($order_ids);
			
	  
      return view('admin.event',compact('eventName','tickets','eventTag','order_ids_and_Frequancy','eventId'));
    }

   
   
   
   
   
    //Export event to csv
    public function exportToCsv($event_id)
    {
	
	$event = Event::with('tickets')->where('event_id', '=', $event_id)->get();
	
	
	//Strip for tickets
		$event = $event[0];
		
	$eventName = $event->name;
	
	 $tickets = $event->tickets;
		//decending
		//$tickets = $tickets->sortBy('order_id',SORT_REGULAR, true);
		//Asending
		$tickets = $tickets->sortBy('order_id');
	
	
	$filename = $eventName."roster.csv";
	//$filename = "roster.csv";
	
    $handle = fopen($filename, 'w+');
    
	fputcsv($handle, array('First Name', 'Last Name', 'NMLS ID', 'Is claimed','Dietary Restrictions','email','company'));

    foreach($tickets as $ticket) {
		if($ticket->is_claimed > 0){
			$is_claimed ="Yes";
			}else{
				$is_claimed ="No";
				}
			
        fputcsv($handle, array($ticket->FN, $ticket->LN, $ticket->NMLS_id,$is_claimed,$ticket->dietary_restrictions,$ticket->email,$ticket->company));
    }

    fclose($handle);
		
		return \Response::download($filename);
		return redirect()->back();
	}
   

}
