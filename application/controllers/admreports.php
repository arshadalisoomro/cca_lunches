<?php

class AdmReports extends Controller {
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
        $admreports_model = $this->loadModel('AdmReports');
		$this->view->lunchdates = $admreports_model->getLunchDates();
		$this->view->accounts = $admreports_model->getAccounts();
		$this->view->render_report('admreports/admreports',true,false);
	}
    /**
     **************************
     */
	public function getreportdates() {
        $admreports_model = $this->loadModel('AdmReports');
		echo $admreports_model->getReportDates($_GET['reportid']);
	}
    /**
     **************************
     */
	public function getadmreport() {
        $admreports_model = $this->loadModel('AdmReports');
		switch ($_GET['reportid']) {
			case 1: echo $admreports_model->getLunchOrdersReportByProvider($_GET['dateYMD'],false);
			break;
			case 2: echo $admreports_model->getLunchOrdersReportByStudentStaff($_GET['dateYMD'],false);
			break;
			case 3: echo $admreports_model->getAccountBalanceReport(false);
			break;
			case 4: echo $admreports_model->getAccountDetailsReport($_GET['account_id'],false);
			break;
		}	
	}
    /**
     **************************
     */
	public function printadmreport($reportid,$p2 = null) {
        $admreports_model = $this->loadModel('AdmReports');
		switch ($reportid) {
			case 1: echo $admreports_model->getLunchOrdersReportByProvider($p2,true);
			break;
			case 2: echo $admreports_model->getLunchOrdersReportByStudentStaff($p2,true);
			break;
			case 3: echo $admreports_model->getAccountBalanceReport(true);
			break;
			case 4: echo $admreports_model->getAccountDetailsReport($p2,true);
			break;
		}
	}
}