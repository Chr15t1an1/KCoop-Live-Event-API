<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{


  public function tickets()
   {
       return $this->hasMany('App\Ticket');
   }

   public function event()
    {
        return $this->belongsTo('App\Event');
    }

}
