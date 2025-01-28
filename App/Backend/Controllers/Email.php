<?php namespace Backend\Controllers;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\PHPJS;
use Backend\Core\Mailer;

class Email
{
    private $mailer;


    public function __construct()
    {
        $this->mailer = new Mailer();
    }


    public function sendMail($receiver, $title, $message)
    {
        if (empty($receiver))
        {
            $html = '<h4>Please provide a receiver before submitting.</h4>';
            PHPJS::addScript(
                "$('#send-email-feedback').html('$html').css('color', 'orange');" .
                "$('#receiver').val('$receiver');" .
                "$('#title').val('$title');" .
                "$('#message').val('$message');"
            );
            return;
        }

        if (empty($title))
        {
            $html = '<h4>Please provide a title before submitting.</h4>';
            PHPJS::addScript(
                "$('#send-email-feedback').html('$html').css('color', 'orange');" .
                "$('#receiver').val('$receiver');" .
                "$('#title').val('$title');" .
                "$('#message').val('$message');"
            );
            return;
        }

        if (empty($message))
        {
            $html = '<h4>Please provide a message before submitting.</h4>';
            PHPJS::addScript(
                "$('#send-email-feedback').html('$html').css('color', 'orange');" .
                "$('#receiver').val('$receiver');" .
                "$('#title').val('$title');" .
                "$('#message').val('$message');"
            );
            return;
        }


        // Send email
        $result = $this->mailer->send(
            $receiver,
            $title,
            getAppLocale() . "/feedback.php",
            ["message" => $message]
        );


        if ($result)
        {
            // Successful feedback
            $html = '<h4>Email sent successfully.</h4>';
            PHPJS::addScript("$('#send-email-feedback').html('$html').css('color', 'green');");
        }
        else
        {
            // Failed to send feedback
            $html = '<h4>An error occured while sending the email, please try again later.</h4>';
            PHPJS::addScript(
                "$('#send-email-feedback').html('$html').css('color', 'red');" .
                "$('#receiver').val('$receiver');" .
                "$('#title').val('$title');" .
                "$('#message').val('$message');"
            );
        }
    }
}