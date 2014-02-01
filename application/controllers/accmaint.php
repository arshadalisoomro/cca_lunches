<?php

class AccMaint extends Controller {
	function __construct() {
		parent::__construct();
		Auth::handleLogin();
	}
    /**
     **************************
     */
	public function index() {
        if (!Session::get('user_logged_in'))
            return;
        $accmaint_model = $this->loadModel('AccMaint');
		$this->view->teachers = $accmaint_model->getTeachers();
		$this->view->tableBody = $accmaint_model->getAccountsTableBody(0);
		$this->view->render('accmaint/accmaint');
	}
    /**
     **************************
     */
	public function saveuser() {
		function trim_value(&$value) {
    		$value = substr(trim($value),0,50);
		}

		array_filter($_POST, 'trim_value');
		$postfilter = 
			array(
            	'fname'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
            	'lname'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
        	);
        $rvp = filter_var_array($_POST, $postfilter);
		if (empty($rvp['fname'])) {
			echo 'Error: Please enter a First Name.';
			return;
		}
		if (empty($rvp['lname'])) {
			echo 'Error: Please enter a Last Name.';
			return;
		}
		if ($_POST["utype"] == 1) {
			if ($_POST["tid"] < 2) {
				echo 'Error: Please select a Teacher.';
				return;
			}
		}

        $accmaint_model = $this->loadModel('AccMaint');
		if ($_POST["user_id"] == 0)
			$error = $accmaint_model->newUser($_POST['account_id'],$rvp['fname'],$rvp['lname'],$_POST['utype'],$_POST['tid']);
		else
			$error = $accmaint_model->updateUser($_POST['account_id'],$_POST['user_id'],$rvp['fname'],$rvp['lname'],$_POST['utype'],$_POST['tid'],$_POST['ato']);
		if (empty($error))
			echo $accmaint_model->getAccountsTableBody($_POST["account_id"]);
		else
			echo $error;
	}
    /**
     **************************
     */
	public function deleteuser() {
        $accmaint_model = $this->loadModel('AccMaint');
        $accmaint_model->deleteUser($_GET["userid"]);
		echo $accmaint_model->getAccountsTableBody($_GET["account_id"]);
	}
    /**
     **************************
     */
	public function deleteaccount() {
        $accmaint_model = $this->loadModel('AccMaint');
        $accmaint_model->deleteAccount($_GET["account_id"]);
		echo $accmaint_model->getAccountsTableBody($_GET["account_id"]);
	}
    /**
     **************************
     */
	public function saveaccount() {
		
		function trim_value(&$value) {
    		$value = substr(trim($value),0,64);
		}
			
		array_filter($_POST, 'trim_value');
		$postfilter = 
			array(
            	'aname_acc'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH), 
            	'email'=>array('filter' => FILTER_SANITIZE_EMAIL, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
            	'uname'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
        	);
        $rvp = filter_var_array($_POST, $postfilter);
		if (empty($rvp['aname_acc'])) {
			echo 'Error: Please enter an Account name.';
			return;
		}
		if (empty($rvp['email'])) {
			echo 'Error: Please enter a valid Email address.';
			return;
		}
		if (empty($rvp['uname'])) {
			echo 'Error: Please enter a User Name.';
			return;
		}
        $accmaint_model = $this->loadModel('AccMaint');
		$account_id = $_POST["account_id"];
		if ($account_id == 0)
			$error = $accmaint_model->newAccount($rvp['aname_acc'],$rvp['uname'],$rvp['email'],$account_id);
		else
			$error = $accmaint_model->updateAccount($account_id,$rvp['aname_acc'],$rvp['uname'],$rvp['email'],$_POST["atype"],$_POST["uactive"],$_POST["nnorders"]);
		if (empty($error))
			echo $accmaint_model->getAccountsTableBody($account_id);
		else
			echo $error;
	}
}