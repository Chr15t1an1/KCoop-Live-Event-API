<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use File;


class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


//Send email to user
 public static function send($order_object)
   {
	   
	   //Check Log to see if user has recived email before
	  $a = static::checkLog($order_object);  
	 if($a){//e-mail found
	 return true;
   }
   //
	   $orderObject = $order_object;
	  if(empty($orderObject->first_name)){
            $name = "There";
      }else {
        $name = $orderObject->first_name;
      }
      $key = $orderObject->secret_key;
	  
	  $email = $order_object->email;
	 
	 
	   $data = array('name' => $name, 'key' => $key, 'email'=> $email,);
	 
       Mail::send('emails.send',$data, function ($message) use ($email)
       {
           $message->from('info@knowledgecoop.com', 'Knowledge Coop');
           $message->to($email);
		   $subject = "Please complete registration";
		   $message->subject($subject);
       });

       return response()->json(['message' => 'Request completed']);
   }

//Check Log to see if user has recived email before
	public static function checkLog($order_object){	
		$filename = "emails/log.txt";
		//Get file
		$recoveredData = File::get($filename);
		$recoveredArray = unserialize($recoveredData);
		
		#If file is empty then set as blank array
		if(!$recoveredArray){
			$recoveredArray = array();
			}
		
			$shopify_order_id = $order_object->shopify_order_id;
		
		//Get Data
		if(empty($order_object->email)){
				$email = "null";
		  }else {
			$email = $order_object->email;
		  }
		
	
		$data_array = array($shopify_order_id => $email);
		
		//Returns true if email or oder Id have been found
		$a = static::in_array_r($data_array,$recoveredArray);
		
		if($a){
			//Send Error Email to me
			
			
			\Bugsnag::notifyError('Dup.Email.attempt', 'Email '.$email.'/// Order-ID '.$shopify_order_id);
		

      		 return true;
			 
			}else{
				
		$recoveredArray[] = $data_array;
		$serializedData = serialize($recoveredArray);
		File::put($filename,$serializedData);
		
				
				}
	
	
	
	
		
		
		
		
		
		
		
		}



	public static function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && static::in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}




public static function ManualSend($orderId)
   {
	   
	   //return $orderId;
	   //Check Log to see if user has recived email before
	  //$a = static::checkLog($order_object); 
	  
	 // if($a){//e-mail found
//	   return true;
//	  }
 $orderObject = \App\Order::find($orderId);
	  
	  //return $orderObject;
	  
	  
	 // $orderObject = $order_object;
	  if(empty($orderObject->first_name)){
            $name = "There";
      }else {
        $name = $orderObject->first_name;
      }
      $key = $orderObject->secret_key;

	 
	 
	 
	 $email = $orderObject->email;
	 
	 
	   $data = array('name' => $name, 'key' => $key, 'email'=> $email,);
	 
       Mail::send('emails.send',$data, function ($message) use ($email)
       {
           $message->from('info@knowledgecoop.com', 'Knowledge Coop');
           $message->to($email);
		   //$message->to('ccampbell@sapphirebd.com');
		   $subject = "Please complete registration";
		   $message->subject($subject);
       });

	 
	

       return response()->json(['message' => 'Request completed']);
   }


}
