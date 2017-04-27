<?php

/**
 * Trida obsahuje metody pro zasilani emailu pomoci tridy PHPMailer
 */
class My_Mailer {

    const SMTPSECURE = 'ssl';
    const HOST = 'smtp-88300.m0.wedos.net';
    const PORT = 465;
    const USERNAME = 'info@yagga.cz';
    const PASSWORD = 'Mimi2LibaYagga';
    const EMAIL_FROM = 'info@yagga.cz';
    const EMAIL_FROM_NAME = 'Yagga.cz';

    private $_smtpsecure;
    private $_host;
    private $_port;
    private $_username;
    private $_password;
    private $_emailFrom;
    private $_emailFromName;

    public function __construct() {
        $this->setSmtp_secure(self::SMTPSECURE);
        $this->setHost(self::HOST);
        $this->setPort(self::PORT);
        $this->setUsername(self::USERNAME);
        $this->setPassword(self::PASSWORD);
        $this->setEmail_from(self::EMAIL_FROM);
        $this->setEmail_from_name(self::EMAIL_FROM_NAME);
    }

    public function setSmtp_secure($smtpSecure) {
        $this->_smtpsecure = $smtpSecure;
    }

    public function getSmtp_secure() {
        return $this->_smtpsecure;
    }

    public function setHost($host) {
        $this->_host = $host;
    }

    public function getHost() {
        return $this->_host;
    }

    public function setPort($port) {
        $this->_port = $port;
    }

    public function getPort() {
        return $this->_port;
    }

    public function setUsername($username) {
        $this->_username = $username;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function setPassword($password) {
        $this->_password = $password;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setEmail_from($emailFrom) {
        $this->_emailFrom = $emailFrom;
    }

    public function getEmail_from() {
        return $this->_emailFrom;
    }

    public function setEmail_from_name($emailFromName) {
        $this->_emailFromName = $emailFromName;
    }

    public function getEmail_from_name() {
        return $this->_emailFromName;
    }

    //funkce na zasialni emailu pomoci tridy phpmailer
    public function sendEmail($to, $subject, $body, $others = array()) {
        include_once("PHPMailer/class.phpmailer.php");
        include_once("PHPMailer/class.smtp.php");

        $mail = new PHPMailer(); // defaults to using php "mail()"

        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true;  // authentication enabled
        $mail->SMTPSecure = $this->getSmtp_secure(); // secure transfer enabled REQUIRED for GMail
        $mail->Host = $this->getHost();
        $mail->Port = $this->getPort();
        $mail->Username = $this->getUsername();   // uživatelské jméno pro SMTP autentizaci
        $mail->Password = $this->getPassword();
        $mail->From = $this->getEmail_from();
        $mail->FromName = $this->getEmail_from_name();

        $mail->AddAddress($to);

        $mail->Subject = $subject;

        $mail->IsHTML(true); // tento řádek je zbytečný, protože níže nastavujeme obsah proměnné AltBody
        $mail->Body = $body;

        $mail->Priority = 1;
        $mail->CharSet = "utf-8";

        if (!empty($others)) {
            if (array_key_exists('attachment', $others)) {
                $mail->addAttachment($others['attachment']);
            }
        }

        if (!$mail->Send()) {
            return 0;
        } else {
            return 1;
        }
    }

}
