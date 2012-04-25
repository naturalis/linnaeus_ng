<?php

	include('class.mime_mail_v2.2b.php');
	include('class.smtp_mail.php');

	class EmailHelper {

		/*
		
		'mailto_address'
		'mailto_name'
		'mailfrom_address'
		'mailfrom_name'
		'subject'
		'plain'
		'html'
		'cc'
		'bcc'
		'attachments' => array('content','name','contentType')
		'smtp_server'
		'debug' bool
		
		*/

		function send($params) {

			$mail = new mime_mail();
			
			$mail->to = isset($params['mailto_address']) ? $params['mailto_address'] : null;
			$mail->toname = isset($params['mailto_name']) ? $params['mailto_name'] : null;
			$mail->from = isset($params['mailfrom_address']) ? $params['mailfrom_address'] : null;
			$mail->fromname =isset($params['mailfrom_name']) ? $params['mailfrom_name'] : null;
			$mail->subject = isset($params['subject']) ? $params['subject'] : null;
			$mail->body_plain   = isset($params['plain']) ? $params['plain'] : null;
			$mail->body_html    = isset($params['html']) ? $params['html'] : null;
			if (isset($params['cc'])) $mail->cc = $params['cc'];
			if (isset($params['bcc'])) $mail->bcc = $params['bcc'];

			if (isset($params['attachments'])) {

				foreach((array)$params['attachments'] as $key => $val) {

					$mail->add_attachment($val['content'],$val['name'],$val['contentType']);

				}

			}
			
			$mail->headers = "X-Mailer: Linnaeus NG";

			$smtp_server = isset($params['smtp_server']) ? $params['smtp_server'] : null;

			if (
				is_null($mail->to) || 
				is_null($mail->from) ||
				(is_null($mail->body_plain) && is_null($mail->body_html)) ||
				is_null($smtp_server)
			) return false;

			$data = $mail->get_mail();
			$smtp = new smtp_mail;
			$smtp->debug = isset($params['debug']) ? $params['debug'] : false;

			return $smtp->send_email($smtp_server, $mail->from, $mail->to, $data);
	
		}
	
	}

?>