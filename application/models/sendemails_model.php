<?php

class SendEmailsModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
    */
    public function send_email() {

        function trim_value(&$value) {
            $value = trim($value);
        }

        array_filter($_POST, 'trim_value');
        $postfilter =
            array(
                'fromemail'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
                'subject'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
                'msg'=>array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
            );
        $revised_post_array = filter_var_array($_POST, $postfilter);

        $_SESSION['sendemails_subject'] = $revised_post_array['subject'];
        $_SESSION['sendemails_msg'] = $revised_post_array['msg'];

        if (!(filter_var($revised_post_array['fromemail'], FILTER_VALIDATE_EMAIL))) {
            $_SESSION["feedback_negative"][] = 'Please enter a valid email address.';
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

		$to_email = '';
		$count = 0;
        $sql = "SELECT user_email FROM los_accounts WHERE user_active=1 ";
        switch ($_POST["select-send-mail-to"]) {
			case 1:
                $to_email = $revised_post_array['fromemail'];
				$count = 1;
                break;
            case 2:
                $sql .= "AND user_creation_timestamp IS NOT NULL AND DATE_FORMAT(FROM_UNIXTIME(user_creation_timestamp), '%e') = DATE_FORMAT(NOW(),'%e')";
                break;
            case 3:
                $sql .= "AND total_debits > 0";
                break;
            case 4:
                break;
        }

		if ($to_email == '') {
			$query = $this->db->prepare($sql);
			$query->execute();
			$count = $query->rowCount();
		}
        if ($count > 0) {

            $mail = new PHPMailer;
            $mail -> IsMail();
            $mail -> From = $revised_post_array['fromemail'];
            $mail -> FromName = $revised_post_array['fromemail'];
            $mail -> Subject = $revised_post_array['subject'];
            $mail -> Body = $revised_post_array['msg'];

			if ($to_email == '') {
				$results = $query->fetchAll();
				foreach ($results as $result) {
					$mail->addBCC($result->user_email);
				}
			} else {
				$mail->addBCC($to_email);
			}

            if (!$mail -> Send()) {
                $_SESSION["feedback_negative"][] = 'Error sending mail: ' . $mail -> ErrorInfo;
                return false;
            }
            $_SESSION["feedback_positive"][] = 'There were '.$count.' email(s) sent.';
            unset($_SESSION['sendemails_subject']);
            unset($_SESSION['sendemails_msg']);

            return true;
        } else {
            $_SESSION["feedback_negative"][] = 'No users met the selected criteria.  No emails sent.';
            return false;
        }
    }
}