<?php

class Calendar extends Controller {
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    /**
     **************************
     */
	public function index() {
		if (!isset($_SESSION['account_id'])) {
			return;
        }
		
        $calendar_model = $this->loadModel('Calendar');
		if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN)
			$this->view->amountdue = $calendar_model->getAmountDue();
		$this->view->accountnames = $calendar_model->getAccountNames();
		
		$start_date = new DateTime();
		$day_of_week = $start_date->format("w");
		$start_date->modify("-$day_of_week day");

		$end_date = new DateTime();
		$day_of_week = 7-$day_of_week;
		$end_date->modify("+$day_of_week day");

		$this->view->lunchesTableWeek = $calendar_model->getLunchesTableWeek(
			Session::get('account_id'),$start_date->format("Y-m-d"),$end_date->format("Y-m-d"));
		
		$this->view->render('calendar/orderlunches',false);
	}
    /**
     **************************
     */
    public function getaccountid() {
        if (!isset($_SESSION['account_id'])) {
            echo 0;
            return 0;
        } else
            echo Session::get('account_id');
    }
    /**
     **************************
     */
	public function getlunchestable() {
		if ((isset($_GET['account_id'])) && (isset($_GET['startDateYMD'])) && (isset($_GET['endDateYMD']))) {
			$calendar_model = $this->loadModel('Calendar');
			echo $calendar_model->getLunchesTableWeek($_GET['account_id'],$_GET['startDateYMD'],$_GET['endDateYMD']);
		} else {
            echo 'error';
        }
	}
    /**
     **************************
     */
	public function getorderdetails() {
        if ( (isset($_GET['userID'])) && (isset($_GET['dateYMD'])) ) {
            $calendar_model = $this->loadModel('Calendar');
		    echo $calendar_model->getOrderDetails($_GET['userID'],$_GET['dateYMD']);
        } else {
            echo 'error';
        }
	}
    /**
     **************************
     */
	public function saveorder() {
		$res = array();	
		$orderID = 0;
		if (isset($_POST['chk']))
			$chk = $_POST['chk'];	
		else
			$chk = 0;
        $calendar_model = $this->loadModel('Calendar');
        $calendar_model->saveOrder($_POST['rb'],$chk,$_POST['dateYMD'],$_POST['userID'],$_POST['orderID'],$_POST['account_id']);
        $common_model = $this->loadModel('Common');
        $common_model->updateDebits($_POST['account_id']);
		$res[] = $calendar_model->getOrderCell($_POST['userID'],$_POST['dateYMD'],$orderID);
		$res[] = $orderID;
		if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN)
			$res[] = $calendar_model->getAmountDue();
		echo json_encode($res);
	}
}