@extends('layout')

@section('content')

    @if (count($errors) > 0)
    
   <div class="row">
    <div class="container">
    <div class="col-lg-4 col-lg-offset-4 alert alert-danger">
    <h2>Ooops.... There are some form errors</h2>
    	<ul>
        	@foreach($errors->all() as $error)
            <?php 
			
			$error = preg_replace('/[-]/', '', $error); ?>
        	<li>{{$words = preg_replace('/[0-9]+/', '', $error)}}</li>
            @endforeach 
        </ul>
    </div>
    </div>
    </div>
    
    @endif

<a href="/admin/event/{{$tickets[0]->event_id}}"><button class="btn btn-danger"> Back</button></a>

<div class="container">
  <div class="col-lg-offset-1 col-lg-10">
    <form method="POST" action="{{Request::url()}}" class="form-horizontal">
      <fieldset>
        <!-- Form Name -->
        <legend>Edit Records</legend>
        @foreach ($tickets as $ticket)
        
        
          @if($ticket->is_claimed = 0)     
                <!-- Text input-->
                <input style="display:none;" id="ticketId" name="ticketId-{{ $ticket->id }}" value="{{ $ticket->id }}"/>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="firstname">First Name</label>
                  <div class="col-md-4">
                    <input id="firstname" name="firstname-{{ $ticket->id }}" type="text" placeholder="Jane" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="lastname">Last Name</label>
                  <div class="col-md-4">
                    <input id="lastname" name="lastname-{{ $ticket->id }}" type="text" placeholder="Doe" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="nmls_id">NMLS ID</label>
                  <div class="col-md-4">
                    <input id="nmls_id" name="nmls_id-{{ $ticket->id }}" type="text" placeholder="8675309" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="nmls_id">Email</label>
                  <div class="col-md-4">
                    <input id="nmls_id" name="email-{{ $ticket->id }}" type="text" placeholder="you@domain.com" class="form-control input-md" required>
                  </div>
                </div>
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="nmls_id">Company</label>
                  <div class="col-md-4">
                    <input id="nmls_id" name="company-{{ $ticket->id }}" type="text" placeholder="ACME inc" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Textarea -->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="textarea">Dietary Restrictions</label>
                  <div class="col-md-4">
                    <textarea class="form-control" placeholder="I like all food!!" id="textarea" name="dietary_restrictions-{{ $ticket->id }}"></textarea>
                  </div>
                </div>
                <hr/>
         
 
 		@else
 
				         <!-- Text input-->
                <input style="display:none;" id="ticketId" name="ticketId-{{ $ticket->id }}" value="{{ $ticket->id }}"/>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="firstname">First Name</label>
                  <div class="col-md-4">
                    <input id="firstname" name="firstname-{{ $ticket->id }}" type="text" value="{{ $ticket->FN }}" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="lastname">Last Name</label>
                  <div class="col-md-4">
                    <input id="lastname" name="lastname-{{ $ticket->id }}" type="text" value="{{ $ticket->LN }}" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="nmls_id">NMLS ID</label>
                  <div class="col-md-4">
                    <input id="nmls_id" name="nmls_id-{{ $ticket->id }}" type="text" value="{{ $ticket->NMLS_id }}" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="nmls_id">Email</label>
                  <div class="col-md-4">
                    <input id="nmls_id" name="email-{{ $ticket->id }}" type="text" value="{{ $ticket->email}}" class="form-control input-md" required>
                  </div>
                </div>
                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="nmls_id">Company</label>
                  <div class="col-md-4">
                    <input id="nmls_id" name="company-{{ $ticket->id }}" type="text" value="{{ $ticket->company }}" class="form-control input-md" required>
                  </div>
                </div>
                
                <!-- Textarea -->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="textarea">Dietary Restrictions</label>
                  <div class="col-md-4">
                    <textarea class="form-control" id="textarea" name="dietary_restrictions-{{ $ticket->id }}">{{ $ticket->dietary_restrictions }}</textarea>
                  </div>
                </div>
                <hr/>
 
 
 		@endif
 
 
    
    @endforeach
    {{ csrf_field() }} 
  	 <button id="submit" class="btn btn-lg btn-primary" type="submit">Submit</button>
      </fieldset>
    </form>
  </div>
</div>
  
    
@endsection