@extends('layout')

@section('content')

<style>
td {
    max-width: 130px;
    overflow: scroll;
}


</style>

<div class="container">





<h1>{{$eventName}}</h1>
<script>
//This JS shows one resend email option per order
<?php echo "var x=".json_encode($order_ids_and_Frequancy); ?>



  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$().ready(function(){

	$.each(x, function (index, value) {
	  //console.log(index);
	  var orderId = index;
	  var frequ = value;

	  var count = 0;

	  $('.'+orderId).each(function(index, value ) {
			count = count+1;
		  if(count != frequ) {
				$(this).fadeOut("fast", function(){
					   $(this).replaceWith("Order ID: "+orderId);
					});

			}
		});

	});

});

</script>


<a href="/admin"><button class="btn btn-success">
 <i class="fa fa-chevron-circle-left" aria-hidden="true"></i>
 Back to all events
</button></a>

<a href="/admin/export/csv/{{$eventId}}"><button class="btn btn-primary">
<i class="fa fa-cloud-download" aria-hidden="true"></i>
 Downlaod Roster
</button></a>
<hr/>

<a href="https://knowledgecoop-2.myshopify.com/admin/products/{{$eventId}}" target="_blank"><button class="btn btn-primary">
<i class="fa fa-tag" aria-hidden="true"></i>
 View Shopify Product
</button></a>

<a href="/admin/event/with-archived/{{$eventId}}"><button class="btn btn-info">
  <i class="fa fa-cogs" aria-hidden="true"></i>
 View Archived Orders.
</button></a>

<a href="/admin/event/{{$eventId}}"><button class="btn btn-success">
  <i class="fa fa-cogs" aria-hidden="true"></i>
 View without archived Orders.
</button></a>

<hr/>
<table id="myTable" class="table table-bordered table-striped table-hover table-condensed table-responsive">
	<thead>
		<tr>
			<th>
        Archive Order
			</th>
            <th>
				Shopify Order
			</th>
			<th>
				Registration Compleate
			</th>
			<th>
				Dietary Restrictions
			</th>
			<th>
				First Name
			</th>
			<th>
				Last Name
			</th>
			<th>
				NMLS ID
			</th>
            <th>
				Email
			</th>
            <th>
				Company
			</th>
            <th>
				Resend Email
			</th>

            <th>
				Edit Order
			</th>

		</tr>
	</thead>
	<tbody>
<?php $count = 0; ?>
@foreach ($tickets as $ticket)
<?php $count = $count+1; ?>


    @if($ticket->is_archived != 1)
    <tr>
    @else
    <tr style="background-color: #8080807a;">
    @endif


  <td>

    @if($ticket->is_archived != 1)
    <button onClick="archiveOrder({{$ticket->order_id}})" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
    @else
  <button onClick="unarchiveOrder({{$ticket->order_id}})" class="btn btn-primary"><i class="fa fa-recycle" aria-hidden="true"></i></button>
    @endif



  </td>



  <td>
    <a href="/admin/tickets/redirect/{{$ticket->order_id}}"><button class="btn btn-success">Shopify Order</button></a>
  </td>


  <td>

    @if($ticket->is_claimed > 0)
    <span style="background-color:#2e6da4;" class="badge">Yes</span>
    @else
    <span style="background-color:#d9534f;" class="badge">No</span>
    @endif

  </td>
  <td>
    {{$ticket->dietary_restrictions}}
  </td>
  <td>
    {{$ticket->FN}}
  </td>
  <td>
    {{$ticket->LN}}
  </td>
  <td>
    {{$ticket->NMLS_id}}
  </td>
   <td>
    {{$ticket->email}}
  </td>
   <td>
    {{$ticket->company}}
  </td>

   @if($ticket->is_claimed > 0)
  <td>
    <i style="font-size: 30px;" class="fa fa-check-square" aria-hidden="true"></i>
  </td>
    @else
   <td class ="">
    <button onClick="emailReminder({{$ticket->order_id}})" class="btn btn-block btn-primary {{$ticket->order_id}}"><i class="fa fa-envelope" aria-hidden="true"></i> Order {{$ticket->order_id}}
</button>
 	</td>
    @endif

  <td>
    <a href="/admin/tickets/register/{{$ticket->order_id}}"><button class="btn btn-danger btn-block"><i style="color:#FFF;" class="fa fa-pencil" aria-hidden="true"></i></button></a>
  </td>

</tr>

@endforeach

</tbody>
</table>
<hr/>
<?php
print "Total count: ".$count;
?>


<script>

function emailReminder(orderId) {

        // Fetch All Live courses and send data to file to be saved
            //$.post( "/admin/email/send-reminder/"+orderId);
            $.post( "/admin/email/send-reminder/"+orderId, function(data) {
                   alert( data );
                });
        }


  function archiveOrder(orderId) {

    if (confirm('Are you sure you want to archive all tickets in this order')) {
      $.post( "/admin/order/archive/"+orderId, function(data) {
             alert( data );
          });
      }
  }


  function unarchiveOrder(orderId) {

    if (confirm('Are you sure you want to unarchive all tickets in this order')) {
      $.post( "/admin/order/unarchive/"+orderId, function(data) {
             alert( data );
          });
      }
  }





</script>

</div>

@endsection
