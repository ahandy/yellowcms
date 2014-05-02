<?php
class Mail {
	public function __construct() {
		require_once 'phpmailer/PHPMailerAutoload.php';
		$this -> mail = new PHPMailer;
	}

	public function sendMail($to, $subject, $body, $attachment = NULL, $isHTML = FALSE, $img = FALSE) {
		// making the connection over smtp
		// $this -> mail -> isSMTP();

		// setting the host
		$this -> mail -> Host = get_setting('smtp_host');

		// setting the port
		$this -> mail -> Port = get_setting('smtp_port');

		$this -> mail -> SMTPAuth = true;

		$this -> mail -> IsHTML($isHTML);

		// login credentials
		$this -> mail -> Username = get_setting('smtp_user');
		$this -> mail -> Password = get_setting('smtp_pass');

		// $this -> mail -> SMTPSecure = 'ssl';

		// headers
		$this -> mail -> From = get_setting('smtp_from');
		$this -> mail -> FromName = get_setting('smtp_from_name');

		$this -> mail -> addAddress($to);

		$this -> mail -> Subject = $subject;

		if(!is_bool($img)) {
			$this -> mail -> AddEmbeddedImage($img, 'img');
		}
		$this -> mail -> Body = $body;

		// Checking if there is an attachment
		if(!is_null($attachment)) {
			$this -> mail -> addAttachment($attachment);
		}

		if(!$this -> mail -> send()) {
			echo $this -> mail -> ErrorInfo;
			return false;
		}

		return true;
	}
}