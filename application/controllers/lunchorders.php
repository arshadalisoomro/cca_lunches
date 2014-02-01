<?php

class LunchOrders extends Controller {
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    /**
     **************************
     */
    public function index() {
        if (!Session::get('user_logged_in'))
            return;
        $lunchorders_model = $this->loadModel('LunchOrders');
        $this->view->lunchdates = $lunchorders_model->getLunchDates();
        $this->view->render('lunchorders/index');
    }
    /**
     **************************
     */
    function getlunchorders() {
        $lunchorders_model = $this->loadModel('LunchOrders');
        echo $lunchorders_model->getLunchOrders($_GET['dateYMD']);
    }
    /**
     **************************
     */
    function setstatusscheduled() {
        $lunchorders_model = $this->loadModel('LunchOrders');
        $lunchorders_model->setStatusScheduled($_GET['dateYMD']);
        $common_model = $this->loadModel('Common');
        $common_model->updateAllAccountsCreditsAndDebits();
        echo $lunchorders_model->getLunchOrders($_GET['dateYMD']);
    }
    /**
     **************************
     */
    function setstatusordered() {
        $lunchorders_model = $this->loadModel('LunchOrders');
        $lunchorders_model->setStatusOrdered($_GET['dateYMD']);
        $common_model = $this->loadModel('Common');
        $common_model->updateAllAccountsCreditsAndDebits();
        echo $lunchorders_model->getLunchOrders($_GET['dateYMD']);
    }
}