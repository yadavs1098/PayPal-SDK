@extends('web.layouts.app')
@section('title', 'Checkout Page')
@section('webcontent')
@include('web.layouts.header')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            
        </div>
        <div class="col-md-6">
            
        </div>
        <div class="col-md-3">
            
        </div>
    </div>
    
</div>
@php $cost=0; @endphp
@foreach($campaignInfluncer['influencers'] as $influencer)
    @php
    if($influencer['status']==1){
        $cost = $cost + $influencer['cost']; 
    }
    @endphp
@endforeach
@php $commission_price = round($cost*floatval($commission->commission_percentage/100),2); @endphp
@php $cost = $cost+$commission_price; @endphp
@php $gst = round(($cost*0.10),2); @endphp
@php $total = $cost+$gst @endphp
<!-- start influncers_list page -->
<div class="container">
	<div class="Confirm_heading">
		<h1>Order Campaign</h1>
	</div>
</div>

<div class="container checkout" style="color: #2b2a27;font-family: GothamRounded-Light;">
	<div class="row ">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="row">
				<div class="col-md-8 col-sm-8 col-xs-12">
					<div class="translation-container" style="background:#fff;"> 
						<div class="box-body">
						
							<div class="col-md-12 col-sm-12 col-xs-12">
								 @if (\Session::get('success'))
									<div class="alert alert-success">
										<p>{{ \Session::get('success') }}</p>
									</div>
								@endif

								@if (\Session::get('danger'))
									<div class="alert alert-danger">
										<p>{{ \Session::get('danger') }}</p>
									</div>
								@endif
								
								@if (\Session::get('paymentinfo'))
									<div class="alert alert-danger">
										<p>{{ \Session::get('paymentinfo') }}</p>
									</div>
								@endif
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
							
							
							
								 
                                <h3 style="margin-bottom: 20px;">Payment Information</h3>
								<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
								
								
								<div class="panel panel-default">
									<div class="panel-heading" role="tab" id="headingTwo">
									  <h4 class="panel-title">
										<label class="panel-action" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo" for="creditcard" style="width: 100%; cursor: pointer;">
										  <input type="radio" name="payment_method" value="payment_cc" id="creditcard">
										  <span class="checkmark"></span>&nbsp;&nbsp;
											<img src="https://www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg" title="Paypal" name="Paypal" alt="Paypal" height="24">
										</label>
									  </h4>
									</div>
									
									<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
									  <div class="panel-body">
											By clicking on the "Place Order" button you will be redirect to Paypal for secure payment.
										  
													<form style="padding-top: 20px;" class="form-horizontal" method="POST" id="paypal-form" role="form" action="{{url('paypal')}}/{{$campaignInfluncer['encrypted_id']}}" >
													{{csrf_field()}}
													<input type="submit" name="Place Order" value="Place Order" class="btn nxt-btn pull-right" id="placeOrder">
													</form>
												
									  </div>
									</div>
								  </div>

								
								<div class="panel panel-default">
									<div class="panel-heading" role="tab" id="headingThree">
									  <h4 class="panel-title">
										<label class="panel-action" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree" for="creditcard" style="width: 100%; cursor: pointer;">
										  <input type="radio" name="payment_method" value="payment_cc" id="creditcard">
										  <span class="checkmark"></span>&nbsp;&nbsp;
											<img src="{{URL::to('/')}}/public/website/images/credit-card.png" title="Credit Card" name="Credit Card" alt="Credit Card" height="24">
										</label>
									  </h4>
									</div>
									<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
									  <div class="panel-body">
											

											<div class="alert alert-danger validation-msg" style="display:none;"></div>	
											
                    <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation"
                                                     data-cc-on-file="false"
                                                    data-stripe-publishable-key="pk_live_key"
                                                    id="payment-form">
                        @csrf
  
                        <div class='form-row row'>
                            <div class='col-xs-12 form-group required'>
                                <label class='control-label'>Name on Card</label> <input
                                    class='form-control' size='30' type='text' pattern="[a-zA-Z][a-zA-Z ]{2,}">
                            </div>
                        </div>
  
                        <div class='form-row row'>
                            <div class='col-xs-12 form-group card required'>
                                <label class='control-label'>Card Number</label> <input
                                    autocomplete='off' class='form-control card-number' size='20'
                                    type='text' >
                            </div>
                        </div>
  
                        <div class='form-row row'>
                            <div class='col-xs-12 col-md-4 form-group cvc required'>
                                <label class='control-label'>CVC</label> <input autocomplete='off'
                                    class='form-control card-cvc' placeholder='ex. 311' size='4'
                                    type='text'>
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                <label class='control-label'>Expiration Month</label> <input
                                    class='form-control card-expiry-month' placeholder='MM' size='2'
                                    type='text'>
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                <label class='control-label'>Expiration Year</label> <input
                                    class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                    type='text'>
                            </div>
                        </div>
  
                        <div class='form-row row'>
                            <div class='col-md-12 error form-group hide'>
                                <div class='alert-danger alert'>Please correct the errors and try
                                    again.</div>
                            </div>
                        </div>
  
                        <div class="row">
                            <div class="col-xs-12">
                                <input type='hidden' value='{{$user_data->firstname}}' name='firstname'>
                                <input type='hidden' value='Test' name='lastname'>
                                <input type='hidden' value='{{$user_data->email}}' name='email'>
                                <input type='hidden' value='{{$amount}}' name='amount'>
                                <input type='hidden' value='{{$campaign_id}}' name='campaign_id'>
                                <input type='hidden' value='{{$currency_type}}' name='currency_type'>
                                <input type='hidden' value='{{$payment_type}}' name='payment_type'>
                                <button class="btn btn-primary btn-lg btn-block" type="submit">Place Order (@if($currency_type=='INR')&#x20B9;@endif @if($currency_type=='USD' || $currency_type=='AUD')&#36;@endif{{$amount}})</button>
                            </div>
                        </div>
                          
                    </form>
												
									  </div>
									</div>
								  </div>
								  
								  <?php if($user_data->invoice_allow=='Y'){ ?>
								  <div class="panel panel-default">
									<div class="panel-heading" role="tab" id="headingFour">
									  <h4 class="panel-title">
										<label  class="panel-action" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="true" aria-controls="collapseOne" for="paypal" style="width: 100%;cursor: pointer;">
										  <input type="radio" checked="checked" name="payment_method" value="" id="paypal">
										  <span class="checkmark"></span>&nbsp;&nbsp;Payment via invoice
										</label>
									  </h4>
									</div>
									<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
									  <div class="panel-body">
										By clicking on the "Place Order" button your order will be placed on Gaibo and we will send you the payment link manually. 
										 <form class="form-horizontal" method="POST" id="manual-order-form" role="form" action="{{ route('stripe.post') }}" >
										 
										 
										{{csrf_field()}}
										
											<input type='hidden' value='{{$user_data->firstname}}' name='firstname'>
											<input type='hidden' value='Test' name='lastname'>
											<input type='hidden' value='{{$user_data->email}}' name='email'>
											<input type='hidden' value='{{$amount}}' name='amount'>
											<input type='hidden' value='{{$campaign_id}}' name='campaign_id'>
											<input type='hidden' value='{{$currency_type}}' name='currency_type'>
											<input type='hidden' value='invoice' name='payment_type'>
										
										
										<input type="hidden" value="mannual" name="mannual"/>
										<input type="submit" name="Place Order" value="Place Order" class="btn btn-primary btn-lg pull-right" id="placeOrder">
										</form>
									 </div>
									</div>
								  </div>
								  <?php } ?>
								 
								</div>
								
								
								 
								 
								 
								<div class="form-group">
                                                                    <p class="sighup pull-left">By clicking "Place Order", I agree to the <a style="color: #b67f08;" href="{{url('brand-terms-and-conditions')}}" target="_blank">Terms and Conditions</a></p>						 
								</div>
								</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				@include('web.invoice.sidelayout')
			</div>
		</div>
	</div>
</div>
<!-- end influncers_list page -->
 

  
<script type="text/javascript">
$(function() {
    var $form         = $(".require-validation");
  $('form.require-validation').bind('submit', function(e) {
    var $form         = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('hide');
 
        $('.has-error').removeClass('has-error');
    $inputs.each(function(i, el) {
      var $input = $(el);
      if ($input.val() === '') {
        $input.parent().addClass('has-error');
        $errorMessage.removeClass('hide');
        e.preventDefault();
      }
    });
  
    if (!$form.data('cc-on-file')) {
      e.preventDefault();
      Stripe.setPublishableKey($form.data('stripe-publishable-key'));
      Stripe.createToken({
        number: $('.card-number').val(),
        cvc: $('.card-cvc').val(),
        exp_month: $('.card-expiry-month').val(),
        exp_year: $('.card-expiry-year').val()
      }, stripeResponseHandler);
    }
  
  });
  
  function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            // token contains id, last4, and card type
            var token = response['id'];
            // insert the token into the form so it gets submitted to the server
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
  
});
</script>
@include('web.layouts.footer')

@endsection




