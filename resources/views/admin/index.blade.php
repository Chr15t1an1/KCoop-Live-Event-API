@extends('layout')

@section('content')
<div class="container">

  <button onclick="getShopProducts()" class="btn btn-lg">Check for Live Events</button>
  <button onclick="GetallOrders()" class="btn btn-lg">Refresh All orders</button>


<hr/>

<div class="list-group">
@foreach ($events as $event)
  <a href="/admin/event/{{ $event->event_id }}" class="list-group-item" style="background-color: #FFF;
    COLOR: #000;">
    <h4 class="list-group-item-heading">{{$event->name}}</h4>
    <p class="list-group-item-text"></p>
  </a>
@endforeach
</div>





    <script>


    // public/js/config.js
    // $(function () {
    //     $.ajaxSetup({
    //         headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
    //     });
    // });

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


    //Shoppify client
    var shopClient = ShopifyBuy.buildClient({
      accessToken: 'bef8ee47afa388b0cd5240f7a7003ebd',
      domain: 'knowledgecoop-2.myshopify.com',
      appId: '6'
    });

    function getShopProducts() {
      // Fetch All Live courses and send data to file to be saved
      shopClient.fetchQueryProducts({collection_id: 443497165}).then(function(products) {
      var cats ={};
        for (var i = 0, len = products.length; i < len; i++) {
            //console.log(product[i].attrs);
            event_id = products[i].attrs.product_id;
            title = products[i].attrs.title;
            //capture_data(title,event_id);
            // event_data.push([title,event_id]);
            cats[event_id] = title;
          }
          $.post( "/admin/event", cats );
      });
      }

      function GetallOrders() {
        // Fetch All Live courses and send data to file to be saved
            $.post( "/admin/event/get-events");

        }


    </script>


@endsection
