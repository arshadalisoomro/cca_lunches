<?php

class SendEmails extends Controller {
    function __construct() {
        parent::__construct();
    }
    /**
     **************************
     */
    function index() {
        if (!Session::get('user_logged_in'))
            return;
        $this->view->render('sendemails/index');
    }
    /**
     **************************
     */
    function domail() {
        $sendemails_model = $this->loadModel('SendEmails');
        if ($sendemails_model->send_email())
            $this->view->render('sendemails/mailsent');
        else
            $this->view->render('sendemails/index');
    }
}
