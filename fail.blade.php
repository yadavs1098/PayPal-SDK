@extends('web.layouts.app')
@section('title', 'Transaction failure Page')
@section('webcontent')
@include('web.layouts.header')


<!-- start influncers_list page -->
<div class="container">
	<div class="Confirm_heading">
		<h1>Order Campaign</h1>
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
							<h2>YOUR ORDER HAS BEEN CANCELLED.</h2>
							<h5>Your Order is #{{$order['order_no']}}</h5>
							<div class="clearfix"></div>
						</div>

						<div class="col-md-8 col-sm-8 col-sm-offset-2  col-xs-12">
							<h4>Order Details</h4>
							<table class="table">
								<thead>
								<tr>
									<th><span>Order Number</span>&nbsp;&nbsp;<strong>#{{$order['order_no']}}</strong></th>
									<th></th>
									<th class="text-right"><span>Order Placed: {{$order['created_at']}}</span></th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td class="text-right"></td>
									<td class="text-right">Cost Of One Campaign ({{$CAMPAIGN_IDENTIFIER[$order['campaign_details']['campaign_type']]}} Type)</td>
									<td class="text-right">AUD $ {{$order['cost']}}</td>
								</tr>
								<tr>
									<td class="text-right"></td>
									<td class="text-right">GST</td>
									<td class="text-right">AUD $ {{$order['gst']}}</td>
								</tr>
								<tr>
									<td class="text-right"></td>
									<td class="text-right">Commission</td>
									<td class="text-right">AUD $ {{$order['commission']}}</td>
								</tr>								
								<tr>
									<td class="text-right"></td>
									<td class="text-right">Total</td>
									<td class="text-right">AUD $ {{$order['total']}}</td>
								</tr>
								</tbody>
							</table>
							<h4>Payment Details</h4>
							<table class="table">
								<tr>
									<td>
										<strong>Payment Method</strong>
										<p>{{$order['payment_method']}}</p>
										<strong>Contact Email</strong>
										<p>{{$email}}</p>
										<?php $pm_mt=ucfirst($order['payment_method']);
											if($pm_mt=='Invoice'){
										?>
										<p style="margin-bottom:0px;">&nbsp;</p>
										<p style="margin-bottom:0px;"><strong>8 Journeys</p>
										<p style="margin-bottom:0px;"><strong>BSB: 083-748</p>
										<p style="margin-bottom:0px;"><strong>Acct No: 212310940</p>
										<?php } ?>
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

