<?php

class ContactUs extends Controller {
    function __construct() {
        parent::__construct();
    }
    /**
     **************************
     */
    function index() {
        //$contactus_model = $this->loadModel('ContactUs');
        $this->view->render('contactus/index');
    }
    /**
     **************************
     */
    function contactus() {
        $contactus_model = $this->loadModel('ContactUs');
        if ($contactus_model->contactUs())
            $this->view->render('contactus/mailsent');
        else
            $this->view->render('contactus/index');
    }
}
