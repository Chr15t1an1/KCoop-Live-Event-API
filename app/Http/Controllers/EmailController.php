<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use File;


class EmailController extends Controller
{

#Send email to user
 public static function send($order_object)
   {
	 //Check Log to see if user has recived email before
	  $a = static::checkLog($order_object);
	 if($a){
    //  e-mail found
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

// If there is an error sending the message send me an email with shopify order id
          try {
            Mail::send('emails.send',$data, function ($message) use ($email)
            {
                $message->from('info@knowledgecoop.com', 'Knowledge Coop');
                $message->to($email);
            $subject = "Please complete registration";
            $message->subject($subject);
            });
          } catch (\Exception $e) {


            	\Bugsnag::notifyError('issue.sending.email', $e);

              static::notify_admin_baduser_data($orderObject->shopify_order_id);
          }



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
      // Respond to ajax request with message. Log errors.
          try {

            Mail::send('emails.send',$data, function ($message) use ($email)
            {
                $message->from('info@knowledgecoop.com', 'Knowledge Coop');
                $message->to($email);
            //$message->to('ccampbell@sapphirebd.com');
            $subject = "Please complete registration";
            $message->subject($subject);
            });

            //return response()->json(['message' => 'Request completed']);
              return "Email Sent";


          } catch (\Exception $e) {

            // \Bugsnag::notifyError('issue.sending.email', $e);
          //  \Bugsnag::leaveBreadcrumb('Something happened!');
          \Bugsnag::notifyError('Email Error', 'Manual send not working Email Controller line 159');

            static::notify_admin_baduser_data($orderObject->shopify_order_id);

            return "Request resulted in error";

            //response()->json(['message' => 'Request resulted in error']);
          }


   }



//
// public static function TestSend(){
//
// $data = array();
//
//   Mail::send('emails.test',$data, function ($message)
//   {
//     $message->from('info@knowledgecoop.com', 'Knowledge Coop');
//     //$message->to($email);
//   $message->to('ccampbell@sapphirebd.com');
//   $subject = "Please complete registration";
//   $message->subject($subject);
//   });
//
// }


// Notify me if order is not processed correctly
public static function notify_admin_baduser_data($shopifyOrderId){

$data = array('orderId' => $shopifyOrderId,);

  Mail::send('emails.notify_admin',$data, function ($message)
  {
    $message->from('info@knowledgecoop.com', 'Knowledge Coop');
    //$message->to($email);
  $message->to('christian@knowledgecoop.com');
  $subject = "! Yo issue with - Please complete registration";
  $message->subject($subject);
  });

}



}
