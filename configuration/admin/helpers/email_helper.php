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



		function sendInfoRequest($vals) {
			$str = '<table><tr><td colspan=3><b>Info request via TimBoodle site</b> ('.date('l j F Y, H:i:s').')</td></tr>'.chr(10);

			foreach((array)$vals as $key => $val) {
				if (substr($key,0,1)=='_' && substr($key,0,2)!='__')
					$str .= '<tr valign=top><td width=100>'.substr($key,1).'</td><td>: </td><td>'.$val.'</td></tr>'.chr(10);
			}

			$str .= '</table>'.chr(10);

			$this->send(
				$this->_settings->recipientmail,
				$this->_settings->recipientname,
				'TimBoodle info request',
				$str,
				strip_tags(html_entity_decode($str))
				);
		}


		function send_password($email,$password) {
			$message = '
				<html>
					<body bgcolor=white>
						Username: '.$email.'<br/>
						Password: '.$password.'<br/>
					</body>
				</html>
			';

			$this->send_mail(
				$email,
				$email,
				'Your UniWebPay password',
				$message,
				strip_tags(html_entity_decode($message))
				);
		}

		function sendOssRegistrationConfirmation($url,$rcpt,$name) {
			$str = '<table><tr><td>Klik onderstaande link aan om je inschrijving te bevestigen:</td></tr>'.chr(10);
			$str .= '<tr><td><a href="'.$url.'">'.$url.'</a></td></tr>'.chr(10);
			$str .= '</table>'.chr(10);

			$this->send(
				$rcpt,
				$name,
				'Bevestiging TimBoodle-registratie',
				$str,
				strip_tags(html_entity_decode($str))
				);
		}

		function _detailBlock($song,$licensedata) {
			return '<table>'.
					'<tr><td colspan=2><b>'.$song['artist'].': '.$song['title'].'</b></td></tr>'.chr(10).
					'<tr><td colspan=2></td></tr>'.chr(10).
					'<tr><td colspan=2>met de volgende parameters:</td></tr>'.chr(10).
					'<tr><td width=150>media: </td><td>'.$licensedata['media'].'</td></tr>'.chr(10).
					'<tr><td>gebied: </td><td>'.$licensedata['geo'].'</td></tr>'.chr(10).
					'<tr><td>periode: </td><td>'.$licensedata['period'].'</td></tr>'.chr(10).
					'<tr><td>exclusiviteit: </td><td>'.$licensedata['exclusivity'].'</td></tr>'.chr(10).
					'<tr><td>prijs: </td><td>'.$licensedata['calculatedprice'].'</td></tr>'.chr(10).
					'<tr><td>aanvangsdatum: </td><td>'.$licensedata['startdate'].'</td></tr>'.chr(10).
					'</table>'.chr(10);
		}

		function sendOssPurchaseConfirmation($song,$licensedata,$rcpt,$name) {
			$str =	'Bevestiging van je licentieaanvraag<br/><br/>'.chr(10).
					$this->_detailBlock($song,$licensedata);

			$this->send($rcpt,$name,'TimBoodle - bevestiging licentieaanvraag '.$song['artist'].' - '.$song['title'],$str,strip_tags(html_entity_decode($str)));
		}

		function sendOssPurchaseNotification($user,$song,$licensedata,$rcpt,$name) {
			$str =	'Nieuwe one stop shopping-licentieaanvraag<br/><br/>'.chr(10).
					$this->_detailBlock($song,$licensedata).
					'<br />'.chr(10).
					'<table>'.
					'<tr><td width=150>firstname: </td><td>'.$user['firstname'].'</td></tr>'.chr(10).
					'<tr><td>lastname: </td><td>'.$user['lastname'].'</td></tr>'.chr(10).
					'<tr><td>companyname: </td><td>'.$user['companyname'].'</td></tr>'.chr(10).
					'<tr><td>contactaddress: </td><td>'.$user['contactaddress'].'</td></tr>'.chr(10).
					'<tr><td>phone: </td><td>'.$user['phone'].'</td></tr>'.chr(10).
					'<tr><td>email: </td><td>'.$user['email'].'</td></tr>'.chr(10).
					'<tr><td>id: </td><td>'.$user['id'].'</td></tr>'.chr(10).
					'</table>';

			$this->send($rcpt,$name,'Licentieaanvraag voor '.$song['artist'].' - '.$song['title'],$str,strip_tags(html_entity_decode($str)));
		}
  }

?>