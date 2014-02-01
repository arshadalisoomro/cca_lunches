<?php

class Account extends Controller {
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    /**
     **************************
     */

	function index() {
		if (!isset($_SESSION['account_id'])) {
			return;
        }
		
        $account_model = $this->loadModel('Account');

        $account_model->populateModelVars();
        $this->view->account_name = Session::get('account_name');
		$this->view->ordertables = $account_model->populateOrderTables();
		$this->view->paymentrows = $account_model->populatePaymentRows();
		$this->view->firstnames = $account_model->getFirstNames();
		$this->view->numorders = $account_model->getNumOrders();
		$this->view->totaldebits = $account_model->getTotalDebits();
		$this->view->numpayments = $account_model->getNumPayments();
		$this->view->confirmedcredits = $account_model->getConfirmedCredits();
		$this->view->balancerow = $account_model->getBalanceRow();
		$this->view->paypalextrarow = $account_model->getPayPalExtraRow();
		$this->view->balance = $account_model->getBalance();
		$this->view->amttopay = $account_model->getAmtToPay();
		
		$paymentMessage = Session::get('paymentmessage');
		if (!empty($paymentMessage)) {
			$this->view->paymentmessage = $paymentMessage;
		} else
			$this->view->paymentmessage = '';

		Session::set('paymentmessage','');
		$this->view->render('account/myaccount');
	}
    /**
     **************************
     */
	public function pay() {

		if (!isset($_POST['amtToPay'])) {
            $_SESSION["feedback_negative"][] = 'Something has gone wrong.  Please log out and try again.  If the problem persists please contact Support.';
			$this->index();
			return;
		}
	
		$paymentAmount = str_replace('$','',$_POST['amtToPay']);
		$currencyCodeType='USD';
		$paymentType='Sale';
		$returnURL = urlencode(URL.'account/completePayment');
		$cancelURL = urlencode(URL.'account/index');
		$items_str = "&L_NAME0=CCA%20Lunch%20Order&L_AMT0=".$paymentAmount."&L_QTY0=1";
		$nvpstr = "&NOSHIPPING=1&ALLOWNOTE=0&AMT=".$paymentAmount."&PAYMENTACTION=".$paymentType."&RETURNURL=".$returnURL."&CANCELURL=".$cancelURL."&CURRENCYCODE=".$currencyCodeType."&LOCALECODE=US".$items_str;
		$resArray=$this->hash_call("SetExpressCheckout",$nvpstr);
		$ack = strtoupper($resArray["ACK"]);
		if($ack==PAYPAL_ACK_SUCCESS){
			Session::set('amtToPay',$paymentAmount);
			$token = urldecode($resArray["TOKEN"]);
			$payPalURL = PAYPAL_URL.$token;
			header('Location: '. $payPalURL) ;
		} else  {
			$this->myaccount();
		}
	}
    /**
     **************************
     */
	private function nvpHeader() {
		$nvpHeaderStr = "&PWD=".urlencode(PAYPAL_API_PASSWORD)."&USER=".urlencode(PAYPAL_API_USERNAME)."&SIGNATURE=".urlencode(PAYPAL_API_SIGNATURE);
		return $nvpHeaderStr;
	}
    /**
     **************************
     */
	private function deformatNVP($nvpstr) {
		$intial=0;
		$nvpArray = array();
		while(strlen($nvpstr)){
			$keypos= strpos($nvpstr,'=');
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
		}
		return $nvpArray;
	}
    /**
     **************************
     */
	private function hash_call($methodName,$nvpStr) {
		$nvpheader=$this->nvpHeader();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,PAYPAL_API_ENDPOINT);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //TODO TRUE
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$nvpStr=$nvpheader.$nvpStr;
		//check if version is included in $nvpStr else include the version.
		if(strlen(str_replace('VERSION=', '', strtoupper($nvpStr))) == strlen($nvpStr)) {
			$nvpStr = "&VERSION=" . urlencode(PAYPAL_VERSION) . $nvpStr;
		}
		$nvpreq="METHOD=".urlencode($methodName).$nvpStr;
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
		$response = curl_exec($ch);
		$nvpResArray=$this->deformatNVP($response);
		if (curl_errno($ch))
            $_SESSION["feedback_negative"][] = curl_error($ch);
		curl_close($ch);
		if (isset($nvpResArray["L_LONGMESSAGE0"]))
            $_SESSION["feedback_negative"][] = $nvpResArray["L_LONGMESSAGE0"];
		return $nvpResArray;
	}
    /**
     **************************
     */
	public function completePayment() {
		Session::set('paymentmessage','');
		$token = urlencode($_GET['token']);
		$nvpstr="&TOKEN=".$token;
		$resArray=$this->hash_call("GetExpressCheckoutDetails",$nvpstr);
		$ack = strtoupper($resArray["ACK"]);
		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			$currencyCodeType='USD';
			$paymentType='Sale';
			$paymentAmount = Session::get('amtToPay');
			$payerID = urlencode($_GET['PayerID']);
			$serverName = urlencode($_SERVER['SERVER_NAME']);
			$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currencyCodeType.'&IPADDRESS='.$serverName ;
			$resArray=$this->hash_call("DoExpressCheckoutPayment",$nvpstr);
			$ack = strtoupper($resArray["ACK"]);
			if($ack != 'SUCCESS' && $ack != 'SUCCESSWITHWARNING'){
				$this->myaccount();
			} else {
                $account_model = $this->loadModel('Account');
                $account_model->savePayment($resArray);
                $common_model = $this->loadModel('Common');
                $common_model->updateCredits(Session::get('account_id'));
				Session::set('paymentmessage','PayPal payment complete.  Thank you!');
				header('Location: '.URL.'account/index') ;
			}
		} else  {
			$this->myaccount();
		}
	}
}