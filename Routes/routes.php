<?php defined("ACCESS") or exit("Access Denied");

use Backend\Core\Router;
use Backend\Controllers\ThemeController;

class Routes extends Router
{
    public function __construct()
    {
        // Activate Router
        parent::__construct();

        // Set handler for home page
        parent::setHomepageHandler("homepageHandler");
        parent::addRoute("en/home", "homepageHandler");
        parent::addRoute("fi/etusivu", "homepageHandler");
        parent::addRoute("sv/hem", "homepageHandler");

        // Register other routes
        parent::addRoute("en/demo", "demoHandler");
        parent::addRoute("fi/esittely", "demoHandler");
        parent::addRoute("sv/demo", "demoHandler");

        // Set handler to for cases where no route was found
        parent::setDefaultHandler("pageNotFoundHandler");

        // These routes ignore maintenance
        parent::ignoreMaintenance(["some-api-urls-for-example"]);

        // Let router handle the routes
        parent::handleRoutes();
    }

    function homepageHandler()
    {
        // Render() method will render a page template from your pages folder
        // For example the following line will render content from /App/Pages/frontpage.php
        $this->render("frontpage");
    }

    // Variables are passed into the route the following way:
    // "example.com/route/variable1/variable2/..."
    // Just add more variables to accept them as well
    function demoHandler()
    {
        // Render content from App/Pages/demo.php and create $message variable that can be used in the template
        $this->render("demo", [
            "message" => "hello!",
        ]);
    }

    function pageNotFoundHandler()
    {
        // Render content from App/Pages/404.php
        // You could also render home page here
        $this->render("404");
    }
}

// Initialize routes
new Routes();
