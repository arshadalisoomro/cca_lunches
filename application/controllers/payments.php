<?php

class Payments extends Controller {
    function __construct() {
		parent::__construct();
		Auth::handleLogin();
	}
    /**
     **************************
    */
	function index() {
        if (!Session::get('user_logged_in'))
            return;
        $payments_model = $this->loadModel('Payments');
		$this->view->accountnames = $payments_model->getAccountNames();
		$this->view->render('payments/payments');
	}
    /**
     **************************
    */
	function getpaymentsbody() {
        $payments_model = $this->loadModel('Payments');
		echo $payments_model->getPaymentsBody($_GET['account_id']);
	}
    /**
     **************************
    */
	function deletepayment() {
        $payments_model = $this->loadModel('Payments');
        $payments_model->deletePayment($_GET['id']);
        $common_model = $this->loadModel('Common');
        $common_model->updateCredits($_GET['account_id']);
		echo $payments_model->getPaymentsBody($_GET['account_id']);
	}
    /**
     **************************
    */
	function addeditpayment() {
        $payments_model = $this->loadModel('Payments');
		$payDesc = filter_var(trim($_POST['payDesc']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $payments_model->addEditPayment($_POST['id'],$_POST['account_id'],$_POST['payMeth'],$_POST['payAmt'],$payDesc,$_POST['dateYMD']);
        $common_model = $this->loadModel('Common');
        $common_model->updateCredits($_POST['account_id']);
		echo $payments_model->getPaymentsBody($_POST['account_id']);
	}
}