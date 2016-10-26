<?php

define( 'JPATH___BASE', realpath(dirname(__FILE__).'/../' ));
include_once JPATH___BASE . "/configuration.php";
require_once('PHPMailerAutoload.php');

class sendMail{
    private $mail = false;
    private $setfrom = false;

    public function __construct() {
        $this->initConfig();
    }

    private function initConfig(){
        $this->mail = new PHPMailer;
        $config = new JConfig();

        $this->mail->isSMTP();
        $this->mail->Host = $config->smtphost;
        $this->mail->Username = $config->smtpuser;
        $this->mail->Password = $config->smtppass;
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = $config->smtpport;
        $this->mail->WordWrap = 50;
        $this->mail->isHTML(true);
    }

    public function setTo($to){
        $this->mail->addAddress($to, $to);
    }

    public function setSubject($subject){
        $this->mail->Subject = $subject;
    }

    public function setMessage($message){
        $this->mail->Body = $message;
    }

    public function setFrom($email, $name = false){
        if(!$name){
            $config = new JConfig();
            $this->mail->setFrom($email, $config->fromname);
        } else {
            $this->mail->setFrom($email, $name);
        }
        $this->setfrom = true;
    }

    public function Send($error = false){
//        return true;
        if(!$this->setfrom){
            $config = new JConfig();
            $this->mail->setFrom($config->smtpuser, $config->fromname);
        }

        $this->mail->msgHTML($this->mail->Body);
        if(!$this->mail->send()) {
            if($error){
                return $this->mail->ErrorInfo;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

}