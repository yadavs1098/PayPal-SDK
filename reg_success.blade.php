@extends('web.layouts.app')
@section('title', 'Transaction Success Page')
@section('webcontent')
@include('web.layouts.header')

<!-- start influncers_list page -->
<div class="container">
	<div class="Confirm_heading">
		<h1>{{ucfirst($order['campaign_name'])}} Campaign Registered</h1>
	</div>
</div>

<div class="container">
	<div class="row ">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 customer-account">
					<div class="translation-container" style="background:#fff;"> 
						<div class="box-body text-center">
							<p>&nbsp;</p>
							<i class="fa fa-smile-o fa-5x" aria-hidden="true"></i>
							<h4>Campaign is sucessfully registered.</h4>
							<h5>Your Order is #{{$order['order_id']}}</h5>
                            <div class="clearfix"></div>
						</div>

						<div class="col-md-8 col-sm-8 col-sm-offset-2  col-xs-12">
							<h4>Order Details</h4>
							<table class="table">
								<thead>
								<tr>
									<th><span>Order Number</span>&nbsp;&nbsp;<strong>#{{$order['order_id']}}</strong></th>
									<th></th>
									<th class="text-right"><span>Order Placed: {{$order['created_at']}}</span></th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td class="text-right"></td>
									<td class="text-right">Registration Fee</td>
									<td class="text-right">AUD $ {{number_format($order['amount'],2)}}</td>
								</tr>
								<tr>
									<td class="text-right"></td>
									<td class="text-right">GST</td>
									<td class="text-right">AUD $ {{number_format($order['gst'],2)}}</td>
								</tr>
								<!--<tr>
									<td class="text-right"></td>
									<td class="text-right">Commission</td>
									<td class="text-right">AUD $ {{$order['commission']}}</td>
								</tr>-->							
								<tr>
									<td class="text-right"></td>
									<td class="text-right">Total</td>
									<td class="text-right">AUD $ {{number_format($order['total'],2)}}</td>
								</tr>
								</tbody>
							</table>
							<h4>Payment Details</h4>
							<table class="table">
								<tr>
									<td>
										<strong>Payment Method</strong>
										<p>Stripe</p>
										<strong>Contact Email</strong>
										<p>{{$email}}</p>
									</td>
									<td>
										<strong>&nbsp;</strong>
										<p>&nbsp;</p>
									</td>
									<td>
										<strong>&nbsp;</strong>
										<p>&nbsp;</p>
										
									</td>
								</tr>
							</table>
							<p>&nbsp;</p>
                                                        <div class="checkout">
                                                            <a href="{{url('/')}}" class="btn nxt-btn pull-right">Go Home</a>
                                                            <a href="{{url('reg-invoice')}}/{{base64_encode($order['user_id'])}}/{{base64_encode($order['cam_id'])}}" class="btn nxt-btn pull-right">Invoice</a>
                                                        </div>
							<p>&nbsp;</p>
						<div class="clearfix"></div>
						</div>	
						
						<div class="clearfix"></div>						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- end influncers_list page -->

@include('web.layouts.footer')

@endsection

