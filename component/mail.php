<?php

class mail extends component{
	function convert_to($source,$target_encoding,$encoding  = false)
	{

		if(!$encoding){
    // detect the character encoding of the incoming file
			$encoding = mb_detect_encoding( $source, "auto" );
		}


    // escape all of the question marks so we can remove artifacts from
    // the unicode conversion process

		$target = str_replace( "?", "[question_mark]", $source );

    // convert the string to the target encoding
		$target = mb_convert_encoding( $target, $target_encoding, $encoding);

    // remove any question marks that have been introduced because of illegal characters
		$target = str_replace( "?", " ", $target );

    // replace the token string "[question_mark]" with the symbol "?"
		$target = str_replace( "[question_mark]", "?", $target );

		return $target;
	}

 public function replaceAccents($x)
	{
		if($x) {
			$x = htmlentities($x, ENT_QUOTES, "UTF-8");
			$x = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil);/','$1',$x);
			$x = preg_replace('/&[^;]*;/',  ' ', $x);
			$x = html_entity_decode($x);			
		}
		return $x;
	}


   function htmlSend($to, $subject, $body,$fromAddress, $fromName){

    	$mime_boundary 	= md5(time());

    	//$encodage = "ISO-8859-1";

		$encodage = "UTF-8";

  	$subject=$this->convert_to($subject,$encodage);

  	$body=$this->convert_to($body,$encodage);

	 	# Common Headers
    	$eol = "\r\n";
    	$mime_boundary 	= md5(time().$fromName);

	 	# Common Headers
    	$headers .= "From: " . $fromName."<".$fromAddress.">".$eol;
  	$headers .= "Reply-To: ".$fromName."<".$fromAddress.">".$eol;   // these two to set reply address
  	$headers .= "Message-ID: <".time()."-".$fromAddress.">".$eol;
	 	$headers .= "X-Priority: 3".$eol;          // These two to help avoid spam-filters
	 	$headers .= "X-Mailer: PHPMailer v".phpversion().$eol;          // These two to help avoid spam-filters
	 	$headers .= "Return-Path: ".$fromName."<".$fromAddress.">".$eol; 

	 	# Boundry for marking the split & Multitype Headers
	 	$headers .= 'MIME-Version: 1.0'.$eol;//.$eol;
	 	$headers .= "Content-Type: multipart/alternative; boundary=\"".$mime_boundary."\"".$eol.$eol;
	 	
	 	# Open the first part of the mail
	 	$msg = "--".$mime_boundary.$eol;
	 	
	 	$htmlalt_mime_boundary = $mime_boundary."_htmlalt"; //we must define a different MIME boundary for this section

	 	$msg .= 'Content-Type: text/plain; charset = "'.$encodage.'"'.$eol;
	 	$msg .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
	 	$string =  strip_tags(substr($body, (strpos($body, "<table"))));
		$string = str_replace("\r", "\n", $string);    // --- replace with empty space
	   // --- replace with space
		$string = str_replace("\t", "\n", $string); 


		$aString = explode("\n",$string);

		$string ='';
		foreach($aString as $line){
			if(strlen($line)){
				$string .= $line."\n\n";
			}
		}


		//$str = str_replace(' ','',$str);
	 //	$msg .= preg_replace("/[^A-Za-z0-9 ]/", '', );
		$msg .= $string.$eol.$eol;

	 	//die($msg)str_replace("\n\n", "",);// Content-Type : multipart/mixed ; , limite et Content-Transfer-Encoding : base64	
	 	# HTML Version
		$msg .= "--".$mime_boundary.$eol;
		$msg .= 'Content-Type: text/html; charset = "'.$encodage.'"'.$eol;
		$msg .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$msg .= $body.$eol.$eol;



		if ($attachments !== false) {
			for($i=0; $i < count($attachments); $i++) {
				if (is_file($attachments[$i]["file"])) {  
	 		    # File for Attachment
					$file_name = substr($attachments[$i]["file"], (strrpos($attachments[$i]["file"], "/")+1));

					$handle=fopen($attachments[$i]["file"], 'rb');
					$f_contents=fread($handle, filesize($attachments[$i]["file"]));
	 		    $f_contents=chunk_split(base64_encode($f_contents));    //Encode The Data For Transition using base64_encode();
	 		    $f_type=filetype($attachments[$i]["file"]);
	 		    fclose($handle);

	 		    # Attachment
	 		    $msg .= "--".$mime_boundary.$eol;
	 		    $msg .= "Content-Type: ".$attachments[$i]["content_type"]."; name=\"".$file_name."\"".$eol;  // sometimes i have to send MS Word, use 'msword' instead of 'pdf'
	 		    $msg .= "Content-Transfer-Encoding: base64".$eol;
	 		    $msg .= "Content-Description: ".$file_name.$eol;
	 		    $msg .= "Content-Disposition: attachment; filename=\"".$file_name."\"".$eol.$eol; // !! This line needs TWO end of lines !! IMPORTANT !!
	 		    $msg .= $f_contents.$eol.$eol;
	 		  }
	 		}
	 	}
	 	
	 	# Finished
	 	$msg .= "--".$mime_boundary."--".$eol.$eol;  // finish with two eol's for better security. see Injection.
	 	
	 	# SEND THE EMAIL
	 	ini_set('sendmail_from',$fromAddress);  // the INI lines are to force the From Address to be used !
	 	$mail_sent = mail($to, $this->replaceAccents($subject), $msg, $headers, "-f$fromAddress");
	 	
	 	ini_restore('sendmail_from');
	 	return $mail_sent;

	 }


function send($to, $subject, $body,$fromAddress, $fromName){
	 	console('body',$body);

		 	# Common Headers
    	$eol = "\r\n";
	 	# Common Headers
    	$headers = "From: " . $fromName."<".$fromAddress.">".$eol;
  	$headers .= "Reply-To: ".$fromName."<".$fromAddress.">".$eol;   // these two to set reply address
  	$headers .= "Message-ID: <".time()."-".$fromAddress.">".$eol;
	 	$headers .= "X-Priority: 3".$eol;          // These two to help avoid spam-filters
	 	$headers .= "X-Mailer: PHPMailer v".phpversion().$eol;          // These two to help avoid spam-filters
	 	$headers .= "Return-Path: ".$fromName."<".$fromAddress.">".$eol; 

	 	# SEND THE EMAIL
	 	ini_set('sendmail_from',$fromAddress);  // the INI lines are to force the From Address to be used !
	 	$mail_sent = mail($to, $this->replaceAccents($subject), $body, $headers, "-f$fromAddress");
	 	console('send', $to, $subject, $body,$fromAddress, $fromName,$mail_sent);

	 	ini_restore('sendmail_from');
	 	return $mail_sent;

	 }

	}

	?>