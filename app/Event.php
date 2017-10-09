<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //

    public function orders()
     {
         return $this->hasMany('App\Order');
     }

     public function tickets()
      {
          return $this->hasMany('App\Ticket','event_id','event_id');
      }


}
