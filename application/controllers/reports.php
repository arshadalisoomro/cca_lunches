<?php

class Reports extends Controller {
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    /**
     **************************
     */
	function index() {
		if (!Session::get('user_logged_in'))
			return;
        $reports_model = $this->loadModel('Reports');
		$this->view->userLunchReport = $reports_model->getUserLunchReport(false);
		$this->view->render_report('reports/lunchreport',true,false);
	}
    /**
     **************************
     */
	public function printlunchreport() {
        $reports_model = $this->loadModel('Reports');
		$this->view->userLunchReport =  $reports_model->getUserLunchReport(true);
		$this->view->render_report('reports/lunchreport',false,true);
	}
}