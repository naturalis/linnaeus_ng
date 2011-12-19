<?php // save as smtp_mail.inc

  class smtp_mail {
    var $fp = false;
    var $lastmsg = "";
    var $debug = false;

    function read_line() {
      $ret = false;
      $line = fgets($this->fp, 1024);
      if(preg_match("/^([0-9]+).(.*)$/", $line, $data)==1) {
        $recv_code = $data[1];
        $recv_msg = $data[2];
        $ret = array($recv_code, $recv_msg);
      }
      return $ret;
    }

    function dialogue($code, $cmd) {
      $ret = true;
      fwrite($this->fp, $cmd."\r\n");
      if ($this->debug) {
        echo $cmd.chr(10);
      }
      $line = $this->read_line($this->fp);
      if ($this->debug) {
        echo "$line[0] $line[1]";
      }
      if ($line == false) {
        $ret = false;
        $this->lastmsg = "";
      } else {
        $this->lastmsg = "$line[0] $line[1]";
        if ($line[0] != $code) {
          $ret = false;
        }
      }

      return $ret;
    }

    function error_message() {
      if ($this->debug)  echo "SMTP protocol failure (".$this->lastmsg.")".chr(10);
    }

    function crlf_encode($data) {
      $data .= "\n";
      $data = str_replace("\n", "\r\n", str_replace("\r", "", $data));
      $data = str_replace("\n.\r\n", "\n. \r\n", $data);
      return $data;
    }

    function handle_email($from, $to, $data) {
      $rcpts = explode(",", $to);

      $err = false;
      if(!$this->dialogue(250, "HELO IE-Mailer") || !$this->dialogue(250, "MAIL FROM:<$from>")) {
        $err = true;
      }

      for ($i = 0; !$err && $i < count($rcpts); $i++) {
        if(!$this->dialogue(250, "RCPT TO:<$rcpts[$i]>")) {
          $err = true;
        }
      }

      if($err || !$this->dialogue(354, "DATA") ||
              !fwrite($this->fp, $data) ||
              !$this->dialogue(250, ".") ||
              !$this->dialogue(221, "QUIT")) {
        $err = true;
      }


      if ($err) {
        $this->error_message();
      }

      return $err;

    }

    function connect($hostname) {
      $ret = false;
      $this->fp = @fsockopen($hostname, 25);
      if($this->fp) {
        $ret = true;
      }
      return $ret;
    }

    // send_email() connect to an SMTP server, encodes the message optionally and sends $data. The envelope sender address is $from. A comma separated
    // list of reciepients is expected in $to

    function send_email($hostname, $from, $to, $data, $crlf_encode = 0) {

      if ($this->debug) echo '<!-- smtp_mail class debug:'.chr(10).chr(10);
	  
      if(!$this->connect($hostname)) {
        if ($this->debug) echo 'cannot open socket'.chr(10);
        return false;
      }

      $line = $this->read_line();
      $ret  = false;

      if($line && $line[0] == "220") {
        if($crlf_encode) {
          $data = $this->crlf_encode($data);
        }

        $ret = $this->handle_email($from, $to, $data);
      } else {
        $this->error_message();
      }
      fclose($this->fp);

		if ($this->debug) echo chr(10).chr(10).'-->'.chr(10);

      return $ret;
    }
  }
?>
