<?php

/*

  title:       mime_mail_v2.class
  version:     v2.1b
  date:        26 dec 2001
							 removed all \n\n's to make it work with GoDaddy
  author:      Maarten D. Schermer
  language:    >= PHP4
  purpose:     class for constructing MIME compliant e-mail bodies
  description: PHP class that enables the user to construct mail bodies, plain, html, or both,
               also allowing for regular attachments as well as inline ones (embedded images etc.).
               the created mail body is returned as one big text string, usable by the accompanying
               smtp_class.class.
  references:  http://www.faqs.org/rfcs/rfc822.html  (STANDARD FOR THE FORMAT OF ARPA INTERNET TEXT MESSAGES)
               http://www.faqs.org/rfcs/rfc1521.html (MIME (Multipurpose Internet Mail Extensions) Part One: Mechanisms for Specifying and Describing the Format of Internet Message Bodies)
               http://www.faqs.org/rfcs/rfc2387.html (The MIME Multipart/Related Content-type)

  usage:
    call:        $mail = new mime_mail();
    public vars: $mail->parts        = [array of attachments; see below];
                 $mail->to           = "someone@somewhere.com"; *
                 $mail->toname       = "Ms. Recipient";
                 $mail->cc           = "cc@somewhere.else.com";
                 $mail->bcc          = "cc@somewhere.else.com";
                 $mail->from         = "sender@right.here.com"; *
                 $mail->fromname     = "Mr. Sender";
                 $mail->reply_to     = "answer.me@right.here.com";
                 $mail->reply_toname = 'Mr. Answer Me';
                 $mail->headers      = "optional additional headers";
                 $mail->subject      = "Hello there";
                 $mail->body_plain   = "plain body";
                 $mail->body_html    = "html body";
                 $mail->body         = "plain text";   (for backward compatibility)
                 $this->charsetPlain = 'us-ascii'; **
                 $this->charsetHTML  = 'iso-8859-1'; **
                 $this->transferEnc  = 'quoted-printable'; **

    these variables need to be set to construct the mail; the ones marked * are the only ones
    really required, although there will be no warning if they are left empty (though the actual
    sending of the message will most likely fail).
    the ones marked ** are set default to the values displayed above; they need not to be set, unless
    there is a good reason.
    to add attachments, call:

      $mail->add_attachment($content,$name,$contentType,$inlineName=false);

    where:

      $content    : the content of the attached file. note: this is the actual content, not a reference to a
                    file somewhere. use fopen() & fread() to get file's content.
      $name       : file's name; will be displayed in the recipient's client
      $contentType: MIME-type for this attachment (binaries: "application/octet-stream", jpeg: "images/jpeg",
                    gif: "images/gif", text: "text/plain")
      $inlineName : for embedding files in HTML mail. if the attachment is an inline file, $inlineName should
                    contain the name referenced in the HTML-body. if the attachment is a regular attachment,
                    $inlineName should be set to false. example: if in the HTML body, you embed an image by using:

                    <IMG SRC="cid:top.image">

                    then $inlineName should be "top.image".

    note: standard attachment encoding is base64.

*/

  class mime_mail {
    var $attachments = 0;      # denotes presence of attachment(s)
    var $related = 0;          # denotes presence of inline (embedded) images; requires multipart/related content type
    var $alternative = false;  # denotes presence of both plain and html text; requires multipart/alternative content type
//    var $nagText = "If you can read this text, please start using a MIME-compliant mail client";
    var $nagText = "Uss a MIME-compliant mail client";
    function mime_mail() {
      $this->parts        = array();
      $this->to           = '';
      $this->toname       = '';
      $this->cc           = '';
      $this->bcc          = '';
      $this->from         = '';
      $this->fromname     = '';
      $this->reply_to     = '';
      $this->reply_toname = '';
      $this->headers      = '';
      $this->subject      = '';
      $this->body         = '';
      $this->body_html    = '';
      $this->body_plain   = '';
      $this->charsetPlain = 'us-ascii';
      $this->charsetHTML  = 'iso-8859-1';
      $this->transferEnc  = 'quoted-printable';
    }

    function makeBoundary() {
      return "=====".md5(uniqid(time()));
    }

    function add_attachment($content,$name,$contentType,$inlineName=false) {
      $this->parts[] = array ("contentType" => $contentType,
                              "content"     => $content,
                              "name"        => $name,
                              "inlineName"  => $inlineName
                              );
      if ($inlineName) {
        $this->related++;
      }
      $this->attachments++;
    }

    function inline2RegularAttachments() {
      for($i=sizeof($this->parts)-1;$i>=0;$i--) {
        $this->parts[$i][inlineName]=false;
      }
      $this->related=0;
    }

    function buildAttachment($part) {
      $content = $part["content"];
      $content = chunk_split(base64_encode($content));
      $encoding = "base64";
      $attachment = "Content-Type: ".$part["contentType"].($part["name"]? "; name=\"".$part["name"]."\"" : "")."\n".
                    "Content-Transfer-Encoding: ".$encoding."\n";
      if (!$part[inlineName]) {
        $attachment .= "Content-Disposition: attachment"."\n";
      } else {
        $attachment .= "Content-Disposition: inline; filename=\"$part[name]\""."\n".
                       "Content-ID: <".$part[inlineName].">"."\n";
      }
      return $attachment."\n".$content."\n";
    }

    function buildMultipart($boundary,$inline) {
      for($i=sizeof($this->parts)-1;$i>=0;$i--) {
        if (($inline && $this->parts[$i][inlineName]) || (!$inline && !$this->parts[$i][inlineName])) {
          $multipart .= "\n--$boundary\n".$this->buildAttachment($this->parts[$i]);
        }
      }
      return $multipart."\n";
    }


    function makeHeaders($complete) {
      $mime = '';

      if (!empty($this->from)) {
        $mime .= "From: ".$this->from. "\n";
      }
      if (!empty($this->headers)) {
        $mime .= $this->headers. "\n";
      }
      if ($complete) {
        if (!empty($this->to)) {
          $mime .= "To: $this->to\n";
        }
        if (!empty($this->cc)) {
          $mime .= "CC: $this->cc\n";
        }
        if (!empty($this->bcc)) {
          $mime .= "BCC: $this->bcc\n";
        }
      }
      if (!empty($this->fromname)) {
        $mime .= "From: \"".$this->fromname."\" <".$this->from.">\n";
      }
      if (!empty($this->toname)) {
        $mime .= "To: \"".$this->toname."\" <".$this->to.">\n";
      }
      if (!empty($this->reply_toname)) {
        $mime .= "Reply-To: \"".$this->reply_toname."\" <".$this->reply_to.">\n";
      }
      if (!empty($this->subject)) {
        $mime .= "Subject: $this->subject\n";
      }

      return $mime;
    }

    function get_mail($complete=true) {
      /* backward compatibility patch (v1 had only $this->body) */
      if (!empty($this->body) && empty($this->body_plain)) {
        $this->body_plain = $this->body;
      }

      # FIX 1: no empty body allowed
      if (empty($this->body_plain) && empty($this->body_html)) {
        $this->body_plain = ' ';
      }

      # FIX 2: plain body only can have no inline images: make inline ones into regular ones
      if (empty($this->body_html) && ($this->related > 0)) {
        $this->inline2RegularAttachments();
      }

      /* if both plain and html bodies are present, alternative is set to true */
      $this->alternative = (!empty($this->body_html) && !empty($this->body_plain));

      $boundary    = $this->makeBoundary();
      $boundaryRel = $boundary."_.REL";
      $boundaryAlt = $boundary."_.ALT";

      /* basic headers */
      $mime = $this->makeHeaders($complete);

      /* content type declarations */
      if ($this->attachments==0) {                                                  # no attachments at all
        if (empty($this->body_html)) {                                                # only plain text
          $mime .= "Content-Type: text/plain; charset=\"$this->charsetPlain\"\n".
                   "Content-Transfer-Encoding: $this->transferEnc\n";
        } else
        if (empty($this->body_plain)) {                                               # only html text
          $mime .= "Content-Type: text/html; charset=\"$this->charsetHTML\"\n".
                   "Content-Transfer-Encoding: $this->transferEnc\n";
        } else {                                                                      # plain & html text
          $mime .= "MIME-version: 1.0\n".
                   "Content-Type: multipart/alternative;\n".
                   "  boundary=\"$boundaryAlt\"\n";
        }
      } else
      if ($this->related==0) {                                                      # only regular attachments
        $mime .= "MIME-version: 1.0\n".
                 "Content-Type: multipart/mixed;\n".
                 "  boundary=\"$boundary\"\n";
      } else
      if ($this->attachments==$this->related) {                                     # only inline attachments
        if (empty($this->body_html)) {                                                # only plain text
#         empty HTML and inline attachments should never happen (FIX 2)
        } else
        if (empty($this->body_plain)) {                                               # only html text
          $mime .= "MIME-version: 1.0\n".
                   "Content-Type: multipart/related;\n".
                   "  boundary=\"$boundaryRel\"\n";
        } else {                                                                      # plain & html text
          $mime .= "MIME-version: 1.0\n".
                   "Content-Type: multipart/related;\n".
                   "        type=\"multipart/alternative\";".
                   "        boundary=\"$boundaryRel\"\n";
        }
      } else {                                                                      # regular & inline attachments
        $mime .= "MIME-version: 1.0\n".                                               # all body types
                 "Content-Type: multipart/mixed;\n".
                 "  boundary=\"$boundary\"\n";
      }

      $mime .= "X-UIDL: ". uniqid("")."\n";
//      $mime .= "X-Mailer: AMGATE InterEstate WebMailer v2.2 (PHP4)\n\n";


      /* body creation */
      if ($this->attachments==0) {                                                  # no attachments at all
        if (empty($this->body_html)) {                                                # only plain text
          $mime .= "\n".$this->body_plain."\n";
        } else
        if (empty($this->body_plain)) {                                               # only html text
          $mime .= "\n".$this->body_html."\n";
        } else {                                                                      # plain & html text
          $mime .= "--$boundaryAlt\n".
                   "Content-Type: text/plain; charset=\"$this->charsetPlain\"; format=flowed\n\n".
                   $this->body_plain."\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n".
                   "--$boundaryAlt--\n";
        }
      } else
      if ($this->related==0) {                                                      # only regular attachments
        if (empty($this->body_html)) {                                                # only plain text
          $mime .= "--$boundary\n".
                   "Content-Type: text/plain; charset=\"$this->charsetPlain\"; format=flowed\n\n".
                   $this->body_plain."\n\n";
        } else
        if (empty($this->body_plain)) {                                               # only html text
          $mime .= "--$boundary\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n";
        } else {                                                                      # plain & html text
          $mime .= "--$boundary\n".
                   "Content-Type: multipart/alternative;".
                   "        boundary=\"$boundaryAlt\"\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/plain; charset=\"$this->charsetPlain\"; format=flowed\n\n".
                   $this->body_plain."\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n".
                   "--$boundaryAlt--\n\n";
        }
        $mime .= $this->buildMultipart($boundary,false)."\n\n";                       # regular attachments
        $mime .= "--$boundary--\n\n";
      } else
      if ($this->attachments==$this->related) {                                     # only inline attachments
        if (empty($this->body_html)) {                                                # only plain text
#         empty HTML and inline attachments should never happen (FIX 2)
        } else
        if (empty($this->body_plain)) {                                               # only html text
          $mime .= "--$boundaryRel\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n".
                   $this->buildMultipart($boundaryRel,true)."\n\n".                   # inline attachments
                   "--$boundaryRel--\n\n";
        } else {                                                                      # plain & html text
          $mime .= "--$boundaryRel\n".
                   "Content-Type: multipart/alternative;".
                   "        boundary=\"$boundaryAlt\"\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/plain; charset=\"$this->charsetPlain\"; format=flowed\n\n".
                   $this->body_plain."\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n".
                   "--$boundaryAlt--\n\n".
                   $this->buildMultipart($boundaryRel,true)."\n\n".                   # inline attachments
                   "--$boundaryRel--\n\n";
        }
      } else {                                                                      # regular & inline attachments
        if (empty($this->body_html)) {                                                # only plain text
#         empty HTML and inline attachments should never happen (FIX 2)
        } else
        if (empty($this->body_plain)) {                                               # only html text
          $mime .= "--$boundary\n".
                   "Content-Type: multipart/related;\n".
                   "        type=\"text/html\";\n".
                   "        boundary=\"$boundaryRel\"\n\n".
                   "--$boundaryRel\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n".
                   $this->buildMultipart($boundaryRel,true)."\n\n".                   # inline attachments
                   "--$boundaryRel--\n\n".
                   $this->buildMultipart($boundary,false)."\n\n".                     # regular attachments
                   "--$boundary--\n\n";
        } else {                                                                      # plain & html text
          $mime .= "--$boundary\n".
                   "Content-Type: multipart/related;\n".
                   "        type=\"multipart/alternative\";\n".
                   "        boundary=\"$boundaryRel\"\n\n".
                   "--$boundaryRel\n".
                   "Content-Type: multipart/alternative;\n".
                   "        boundary=\"$boundaryAlt\"\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/plain; charset=\"$this->charsetPlain\"; format=flowed\n\n".
                   $this->body_plain."\n\n".
                   "--$boundaryAlt\n".
                   "Content-Type: text/html; charset=\"$this->charsetHTML\"\n\n".
                   $this->body_html."\n\n".
                   "--$boundaryAlt--\n\n".
                   "--$boundaryRel\n\n".
                   $this->buildMultipart($boundaryRel,true)."\n\n".                  # inline attachments
                   "--$boundaryRel--\n\n".
                   $this->buildMultipart($boundary,true)."\n\n".                     # regular attachments
                   "--$boundary--\n\n";
        }
      }

      return $mime;
    }

    function send() {
      $mime = $this->get_mail(false);
      mail($this->to, $this->subject, "", $mime);
    }

  }

?>