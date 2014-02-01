<?php

class ContactUsModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
    */
    public function contactUs() {
        function trim_value(&$value) {
            $value = trim($value);
        }
        array_filter($_POST, 'trim_value');
        $postfilter =
            array(
                'sendername'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
                'senderemail'=>array('filter' => FILTER_SANITIZE_EMAIL, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
                'subject'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
                'msg'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
            );
        $revised_post_array = filter_var_array($_POST, $postfilter);
        $senderemail = substr($revised_post_array['senderemail'], 0, 64);

        Session::init();
        Session::set('sendername', $revised_post_array['sendername']);
        Session::set('senderemail', $senderemail);
        Session::set('subject', $revised_post_array['subject']);
        Session::set('msg', $revised_post_array['msg']);

        if (!(filter_var($senderemail, FILTER_VALIDATE_EMAIL))) {
            $_SESSION["feedback_negative"][] = 'Please enter a valid email address.';
            return false;
        }
        if (empty($revised_post_array['sendername'])){
            $_SESSION["feedback_negative"][] = 'Please enter your name.';
            return false;
        }
        if (empty($revised_post_array['subject'])){
            $_SESSION["feedback_negative"][] = 'Please enter a subject line.';
            return false;
        }
        if (empty($revised_post_array['msg'])){
            $_SESSION["feedback_negative"][] = 'Please enter a message.';
            return false;
        }

        if (isset($_POST["captcha"]) AND ($_POST["captcha"] == $_SESSION['captcha'])) {
            //good to go
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_CAPTCHA_WRONG;
            return false;
        }

        $mail = new PHPMailer;
        $mail -> IsMail();
        $mail -> From = $senderemail;
        $mail -> FromName = $revised_post_array['sendername'];
        $mail -> AddAddress(EMAIL_PASSWORD_RESET_FROM_EMAIL);
        $mail -> Subject = $revised_post_array['subject'];
        $mail -> Body = $revised_post_array['msg'];
        if (!$mail -> Send()) {
            $_SESSION["feedback_negative"][] = 'Error sending mail: ' . $mail -> ErrorInfo;
            return false;
        } else {
            $_SESSION["feedback_positive"][] = 'We will get back to you shortly.';
            Session::set('sendername', '');
            Session::set('senderemail', '');
            Session::set('subject', '');
            Session::set('msg', '');
            return true;
        }
    }
}