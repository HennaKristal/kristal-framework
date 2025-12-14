<?php declare(strict_types=1); 
namespace Backend\Routes;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Router;

class Routes extends Router
{
    public function __construct()
    {
        // Set handler for home page
        parent::setHomepageHandler("homepageHandler");

        // Register other routes
        parent::addRoute("en/demo", "demoHandler");
        parent::addRoute("fi/esittely", "demoHandler");
        parent::addRoute("sv/demo", "demoHandler");

        // Set handler for cases where no route was found
        parent::setDefaultHandler("pageNotFoundHandler");

        // Let this route bypass maintenance mode
        parent::ignoreMaintenance(["some-api-urls-for-example"]);

        // Let router handle the routes
        parent::handleRoutes();
    }


    public function homepageHandler(): void
    {
        // Render() method will render a page template from your pages folder
        // For example the following line will render content from /App/templates/frontpage.php
        $this->render("frontpage");
    }


    // Variables are passed into the route the following way:
    // "example.com/route/variable1/variable2/..."
    // Just add more variables to accept them as well
    public function demoHandler(string $message = "hello"): void
    {
        // Render content from /App/templates/demo.php and create $message variable that can be used in the template
        $this->render("demo", [
            "message" => esc_html($message),
        ]);
    }

    
    public function pageNotFoundHandler(): void
    {
        // Render content from /App/templates/404.php
        // You could also render home page here
        $this->render("404");
    }
}
