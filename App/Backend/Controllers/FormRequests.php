<?php namespace Backend\Controllers;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\FormRequest;
use Backend\Controllers\ThemeController;
use Backend\Controllers\LanguageController;
use Backend\Controllers\Email;


class FormRequests extends FormRequest
{
    public function __construct()
    {
        // Protected function can only be called when parent::__construct() is called with ["allow_protected_calls" => true] parameter
        // You can specify your own condition in the IF statement if you want to access protected functions from form requests
        // For example Session::get("logged_in") or Session::get("role") === "admin"
        if (false)
        {
            // Allow form requests to access protected functions
            parent::__construct(["allow_protected_calls" => true]);
        }
        else
        {
            // Allow form requests to access only public functions
            parent::__construct(["allow_protected_calls" => false]);
        }
    }


    // Form Request for changing theme
    public function change_theme($request) // $request variable contains all data sent by the form
    {
        $theme_controller = new ThemeController();
        $theme_controller->changeTheme($request["theme"]);
    }


    // Form Request for changing language
    public function change_language($request)
    {
        $language_controller = new LanguageController();
        $language_controller->changeLanguage($request["language"]);
    }


    // Form Request for sending email
    public function send_email($request)
    {
        $email = new Email();
        $email->sendMail($request["receiver"], $request["title"], $request["message"]);
    }

    // Protected functions can only be called when the parent class is constructed with 'true' parameter or internally from other functions
    protected function xxxxxxxx($request)
    {
        // ...
    }


    // Private functions can only be called internally from other functions within this class
    private function xxxxxxx($request)
    {
        // ...
    }
}
