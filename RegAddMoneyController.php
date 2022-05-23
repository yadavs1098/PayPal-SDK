<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Campaign;
use App\Category;
use App\Image;
use App\Brand;
use App\CampaignInfluncer;
use Auth;
use App\Country;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use App\ContentReviewProcess;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use App\RegistrationPayment;
use App\Setting;

use URL;
use Session;
use Redirect;
use Input;
/** All Paypal Details class **/
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use View;
use Helper;
use Mail;
use App\Commission;
use App\RegistrationCharge;

use App\Http\Controllers\Web\BrandAlertController as brandAlertController;

use App\Http\Controllers\Web\InfluencerAlertController as influencerAlertController;

use App\Http\Controllers\Web\HomeController as homeController;

class RegAddMoneyController extends Controller
{
    use AuthenticatesUsers;
    
    private $_api_context;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // parent::__construct();
        session_start();
        /** setup PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
		View::composers([
			'App\Composers\DefaultComposer'  => ['errors.403']
		]);
    }
    
    public  function postPaymentWithpaypal($campaign_id,Request $request){
        if(Auth::guest() || Auth::user()->role != 3){
                return redirect('/login');
                exit;
        }
        try {
            $decrypted_campaign_id = Crypt::decryptString($campaign_id);
        } catch (DecryptException $e) {
            return redirect('campaign')->with('danger', 'Campaign not found. Please try again later');
        }
        $user_id = Auth::user()->id;
        $currentDate = date('Y-m-d');
        $campaignInfluncer = Campaign::where('id','=',$decrypted_campaign_id)->first();
        if($campaignInfluncer){

				$campaignInfluncer = $campaignInfluncer->toArray();
				
				$payment_data = RegistrationCharge::first();
				$cost = $payment_data->amount;;
				$gst = round(($cost*0.10),2); 
				$total = $cost+$gst;
				$currency_type = $payment_data->currency_type;
				$commission_price=0;
				
				$regPayment = new RegistrationPayment;
				$regPayment->user_id = Auth::user()->id;
				$regPayment->order_id = 0;
				$regPayment->ga_campaign_id = $decrypted_campaign_id;
				$regPayment->amount = $cost;
				$regPayment->commission = $commission_price;
				$regPayment->gst = $gst;
				$regPayment->total = $total;
				$regPayment->paid_amount = $total;
				$regPayment->payment_date = now();
				$regPayment->payment_method = 'PayPal';
				$regPayment->currency_type = $currency_type;
				$regPayment->save();
				
				$regPayment->transaction_id = '';
				$regPayment->payment_no = '';
				$regPayment->payment_status = 0;
				$regPayment->order_status = 0;
				
				$RegorderId = $regPayment->id;
				$RegorderNo = $RegorderId + 1000;
				RegistrationPayment::where('id',$RegorderId)->update(['order_id' => $RegorderNo]);
                
                $orderNo=$RegorderNo;
                
                /********************************** PAYPAL payment start ************************/ 
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $item_1 = new Item();
                
                $item_1->setName($orderNo) /** item name **/
                    ->setCurrency($currency_type)
                    ->setQuantity(1)
                    ->setPrice($total); /** unit price **/
                
                $item_list = new ItemList();
                $item_list->setItems(array($item_1));
                $amount = new Amount();
                $amount->setCurrency($currency_type)
                    ->setTotal($total);
                
                $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('Your order number is '.$orderNo);
                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(URL::to('/reg-paypal-status')) /** Specify return URL **/
                    ->setCancelUrl(URL::to('/reg-paypal-status'));
                $payment = new Payment();
                $payment->setIntent('Sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirect_urls)
                    ->setTransactions(array($transaction));
                    /** dd($payment->create($this->_api_context));exit; **/
                try {
                    $payment->create($this->_api_context);
                } catch (\PayPal\Exception\PPConnectionException $ex) {
                    if (\Config::get('app.debug')) {
                        \Session::put('error','Connection timeout');
                        return Redirect::route('addmoney.paywithpaypal');
                        /** echo "Exception: " . $ex->getMessage() . PHP_EOL; **/
                        /** $err_data = json_decode($ex->getData(), true); **/
                        /** exit; **/
                    } else {
                        \Session::put('error','Some error occur, sorry for inconvenient');
                        return Redirect::route('addmoney.paywithpaypal');
                        /** die('Some error occur, sorry for inconvenient'); **/
                    }
                }
                
                foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());
        Session::put('order_id',$RegorderId);
        Session::put('campaign_id',$campaign_id);
        
        
        if(isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        \Session::put('error','Unknown error occurred');
		//session_destroy('quoteId');
        return Redirect::route('addmoney.paywithpaypal');
        
        
        
        
        
            
            
        }else{
            return redirect('campaign')->with('danger', 'Campaign not found. Please try again later');
        }
        
    }
    
     public function getPaymentStatus()
    {
         if(Auth::guest() || Auth::user()->role != 3){
                return redirect('/login');
                exit;
        }
        
		/** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        $order_id = Session::get('order_id');
        $campaign_id = Session::get('campaign_id');
        
        if(empty($order_id)){
            return redirect('regcheckout/payment/'.$campaign_id)->with('paymentinfo', $payment_id);
        }
        
        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');
        Session::forget('order_id');
        Session::forget('campaign_id');
        
        if (empty($_GET['PayerID']) || empty($_GET['token'])) {
                $paymentStatus = 3;
                $orderstatus = 4; 
				return redirect('regcheckout/payment/'.$campaign_id)->with('danger', 'Transaction Failed. Please try again.');
        }else{
                $payment = Payment::get($payment_id, $this->_api_context);
                $execution = new PaymentExecution();
                $execution->setPayerId($_GET['PayerID']);
                $result = $payment->execute($execution, $this->_api_context);
                if ($result->getState() == 'approved') { 
                        $paymentStatus = 2;
                        $orderstatus = 3; // 'Payment Success'
                }else{
                        $paymentStatus = 3;
                        $orderstatus = 4;  // 'Payment Faild'
						return redirect('regcheckout/payment/'.$campaign_id)->with('danger', 'Transaction Failed. Please try again.');
                }
        }
		 
        
        // $orderAmnt
        $order = RegistrationPayment::where('id',$order_id)->first();
        if(!$order){
            return redirect('regcheckout/payment/'.$campaign_id)->with('danger', 'Something went wrong with your order. Please try again.');
        }
        $orderAmnt = $order->total;
		
		
		$decrypted_campaign_id = Crypt::decryptString($campaign_id);
		$campaignData = Campaign::find($decrypted_campaign_id);
		$campaignData->payment_status = '1';
		$campaignData->save();
		
		
		 $setting = Setting::first();
        $siteEmail = $setting->robot_email;
        $ccEmail = $setting->admin_email;
        $officeaddress  = $setting->office_address;
        $supportemail 	= $setting->support_email;
        $recipientName  = auth()->user()->firstname; 
        $recipient  	= auth()->user()->email;
        
		
		$payment_data = RegistrationCharge::first();
		$currency_type = $payment_data->currency_type;
		
        
        $data = array('name'=>auth()->user()->firstname,
			   'email'=>auth()->user()->email,
			   'title'=>'Your order has been confirmed',
			   'campaign'=>$campaignData,
			   'order'=>$order,
			   'orderNo'=>$order->order_id,
			   'orderdate'=>date('Y-m-d h:i:s'),
			   'paymentMethod'=>'PayPal',
			   'id'=>Crypt::encryptString($order_id),
			   'currencyIcon'=>$currency_type,
			   'officeaddress'=>$officeaddress,
			   'supportemail'=>$supportemail
		);
		Mail::send('emails.orders.reg_sucessinvoice', $data, function($message) use($recipient, $siteEmail, $recipientName,$ccEmail){
			$message->to($recipient, $recipientName)->subject('Gaibo Influencers Campaign: Your order has been confirmed.');
			$message->cc($ccEmail, 'Gaibo - Team');
			$message->from($siteEmail,'Marketplace For Influencer'); 
		});
		
		if (Mail::failures()) {
			Log::channel('paypalpaymentprocess')->info('orders.success Email not sent to user...');
		}else{
			Log::channel('paypalpaymentprocess')->info('orders.success Email sent to user for order number: '.$order_id);
		}
				
				
        
        $orderupdate = RegistrationPayment::where('id',$order_id)->update(['payment_status'=>$paymentStatus,'paid_amount'=>$orderAmnt,'order_status' => $orderstatus,'transaction_id'=>$payment_id,'payment_date'=>date('Y-m-d H:i:s'),'payment_method'=>'PayPal']);
        
          return redirect('/checkout/reg_success/'.Crypt::encryptString($order_id));
    }
}
