<?php

// uses: https://github.com/Synchro/PHPMailer

require '../../../../vendor/autoload.php';

class EmailHelper {
	
	private $mail;
	private $SMTPDebug; // 3
	private $host;
	private $SMTPAuth=true;
	private $username;
	private $password;
	private $SMTPSecure='TLS'; // 'ssl'
	private $port;
	private $sender;
	private $address=[];
	private $replyTo;
	private $cc=[];
	private $bcc=[];
	private $subject;
	private $body;
	private $altBody;
	private $attachment=[];
				
	public function __construct()
	{
		$this->mail = new PHPMailer\PHPMailer\PHPMailer;
	}

	public function setSMTPDebug( $level )
	{
		$this->SMTPDebug=$level;
	}

	public function setHost( $host )
	{
		$this->host=$host;
	}

	public function setSMTPAuth( $state )
	{
		$this->SMTPAuth=$state;
	}

	public function setUsername( $username )
	{
		$this->username=$username;
	}

	public function setPassword( $password )
	{
		$this->password=$password;
	}

	public function setSMTPSecure( $type )
	{
		$this->SMTPSecure=$type;
	}

	public function setPort( $port )
	{
		$this->port=$port;
	}

	public function setSender( $sender )
	{
		$this->sender=$sender; // [ address [, name] ]
	}

	public function addRecipient( $address )
	{
		$this->address[]=$address; // [ address [, name] ]
	}

	public function addReplyTo( $sender )
	{
		$this->replyTo=$sender; // [ address [, name] ]
	}

	public function addCC( $address )
	{
		$this->cc[]=$address; // [ address [, name] ]
	}

	public function addBCC( $address )
	{
		$this->bcc[]=$address; // [ address [, name] ]
	}

	public function addSubject( $txt )
	{
		$this->subject=$txt;
	}

	public function addBody( $txt )
	{
		$this->body=$txt;
	}

	public function addAltBody( $txt )
	{
		$this->altBody=$txt;
	}

	public function addAttachment( $attachment )
	{
		$this->attachment[]=$attachment; // [ path [, display name] ]
	}

	public function send()
	{
		$this->mail->SMTPDebug = $this->SMTPDebug;
		$this->mail->isSMTP();
		$this->mail->Host = $this->host;
		$this->mail->SMTPAuth = $this->SMTPAuth;
		$this->mail->Username = $this->username;
		$this->mail->Password = $this->password;
		$this->mail->SMTPSecure = $this->SMTPSecure;
		$this->mail->Port = $this->port;
		$this->mail->setFrom( $this->sender[0], @$this->sender[1] );

		foreach((array)$this->address as $address)
		{
			$this->mail->addAddress( $address[0], @$address[1] );
		}

		$this->mail->addReplyTo( $this->replyTo );
		foreach((array)$this->cc as $address)
		{
			$this->mail->addCC( $address[0], @$address[1] );
		}

		foreach((array)$this->bcc as $address)
		{
			$this->mail->addBCC( $address[0], @$address[1] );
		}

		foreach((array)$this->attachment as $attachment)
		{
			$this->mail->addAttachment(  $attachment[0], @$attachment[1] );
		}

		$this->mail->isHTML(true);
		
		$this->mail->Subject = $this->subject;
		$this->mail->Body    = $this->body;
		$this->mail->AltBody = $this->altBody;
		
		if( !$this->mail->send() )
		{
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
			return false;
		} 
		else
		{
			return true;
		}
	}
}

	/*
	//$mail->SMTPDebug = 3;                               // Enable verbose debug output
	
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'user@example.com';                 // SMTP username
	$mail->Password = 'secret';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to
	
	$mail->setFrom('from@example.com', 'Mailer');
	$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
	$mail->addAddress('ellen@example.com');               // Name is optional
	$mail->addReplyTo('info@example.com', 'Information');
	$mail->addCC('cc@example.com');
	$mail->addBCC('bcc@example.com');
	
	$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML
	
	$mail->Subject = 'Here is the subject';
	$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
	*/