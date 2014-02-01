<?php

class Schedule extends Controller {
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
		$this->view->render('schedule/schedulelunches',false);
	}
    /**
     **************************
     */
	public function getscheduletable() {
        $schedule_model = $this->loadModel('Schedule');
		echo $schedule_model->getSchedule($_GET['startDateYMD']);
	}
    /**
     **************************
     */
	public function getscheduledate() {
        $schedule_model = $this->loadModel('Schedule');
		echo $schedule_model->getScheduleDate($_GET['dateYMD']);
	}
    /**
     **************************
     */
	public function getproviders() {
        $schedule_model = $this->loadModel('Schedule');
		echo $schedule_model->getProviders();
	}
    /**
     **************************
     */
	public function getmenuitems() {
        $schedule_model = $this->loadModel('Schedule');
		echo $schedule_model->getMenuItems();
	}
    /**
     **************************
     */
	public function getnlemodal() {
        $schedule_model = $this->loadModel('Schedule');
		echo $schedule_model->getNLEModal($_GET['nleid'],$_GET['orders'],$_GET['dateYMD']);
	}
    /**
     **************************
     */
	public function savesched() {
        $schedule_model = $this->loadModel('Schedule');
		$addmsg = filter_var(trim($_POST['addmsg']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$ecmsg = filter_var(trim($_POST['ecmsg']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$provider = -999;
		if (isset($_POST['provider']))
			$provider = $_POST['provider'];
        $schedule_model->saveSched($provider,$_POST['dateYMD'],$addmsg,$ecmsg,intval($_POST['numOrders']));
		echo $schedule_model->getSchedule($_POST['startDateYMD']);
	}
    /**
     **************************
     */
	public function savenle() {
        $schedule_model = $this->loadModel('Schedule');
		$reason = filter_var(trim($_POST['reason']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$desc = filter_var(trim($_POST['desc']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$teacherID = -999;
		if (isset($_POST['teacherid']))
			$teacherID = intval($_POST['teacherid']);
        $schedule_model->saveNLE(intval($_POST['nleid']),$teacherID,$_POST['dateYMD'],$reason,$desc);
		echo $schedule_model->getSchedule($_POST['startDateYMD']);
	}
    /**
     **************************
     */
	public function deletenle() {
        $schedule_model = $this->loadModel('Schedule');
        $schedule_model->deleteNLE(intval($_GET['nleid']));
		echo $schedule_model->getSchedule($_GET['startDateYMD']);
	}
}