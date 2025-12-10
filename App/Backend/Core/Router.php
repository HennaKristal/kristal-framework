<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\PHPJS;
use voku\helper\HtmlMin;

class Router
{
    private $registered_routes = [];
    private $default_route_handler = "";
    private $rendered_view = "";
    private $content_last_modified_time = "";
    private $ignore_maintenance_routes = array();

    protected function addRoute($route_name, $handler)
    {
        $this->registered_routes[$route_name] = $handler;
    }

    protected function setHomepageHandler($handler)
    {
        if (ENABLE_LANGUAGES)
        {
            $available_languages = AVAILABLE_LANGUAGES;

            foreach ($available_languages as $language)
            {
                $this->registered_routes[$language . "/"] = $handler;
            }

            return;
        }

        $this->registered_routes[""] = $handler;
    }

    protected function setDefaultHandler($handler)
    {
        $this->default_route_handler = $handler;
    }

    protected function ignoreMaintenance($routes)
    {
        foreach ($routes as $route)
        {
            $this->ignore_maintenance_routes[] = $route;
        }
    }

    protected function handleRoutes()
    {
        // Parse route and variables from url
        $url_request = $this->getURLRequest();

        // Display maintenance if needed
        if (!in_array($url_request["page"], $this->ignore_maintenance_routes))
        {
            if (MAINTENANCE_MODE && !Session::has("maintenance_access_granted"))
            {
                $this->renderMaintenancePage();
            }
        }

        // Generate sitemap.xml
        if ($this->ShouldSitemapBeRegenerated())
        {
            $this->generateSitemap();
        }

        // Call default route handler if requested route was not found
        if (!isset($this->registered_routes[$url_request['page']]))
        {
            call_user_func_array([$this, $this->default_route_handler], $url_request['variables']);
            return;
        }
        
        // Return exception if requested route handler doesn't exist 
        if (!method_exists($this, $this->registered_routes[$url_request['page']]))
        {
            if (PRODUCTION_MODE)
            {
                exit();
            }
            else
            {
                exit("Route: '" . $url_request['page'] . "' has handler '" . $this->registered_routes[$url_request['page']] . ", but this handler function is not available.");
            }
        }

        // Call the correct route
        call_user_func_array([$this, $this->registered_routes[$url_request['page']]], $url_request['variables']);
    }

    private function getURLRequest()
    {
        // Parse page name and variables from the URL
        $root_url = str_replace("index.php", "", $_SERVER["PHP_SELF"]);
        $url_full = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = substr($url_full, strlen($root_url));
        $params = explode("/", $url);

        // Handle multilingual support
        if (ENABLE_LANGUAGES)
        {
            // Get Available languages and turn them to lower case
            $available_languages = AVAILABLE_LANGUAGES;
            $available_languages = array_map('strtolower', $available_languages);

            // Get language and page from URL
            $language = isset($params[0]) ? strtolower($params[0]) : "";
            $page = isset($params[1]) ? strtolower($params[1]) : "";

            // Redirect to default language if the language was not valid
            if (!in_array($language, $available_languages))
            {
                setAppLocale(DEFAULT_LANGUAGE);
                redirect(route($url));
            }

            $page = $language . "/" . $page;
            unset($params[0], $params[1]);
            setAppLocale($language);
        }
        else
        {
            $page = strtolower($params[0]);
            unset($params[0]);
        }

        // Sanitize the page variable
        $page = htmlspecialchars($page, ENT_QUOTES, 'UTF-8');

        // Validate and sanitize URL variables
        $variables_from_url = [];
        foreach ($params as $key => $value)
        {
            $variables_from_url[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        // Set "" and "/" to be the same
        if ($page === "/")
        {
            $page = "";
        }

        return ['page' => $page, 'variables' => $variables_from_url];
    }

    private function ShouldSitemapBeRegenerated()
    {
        if (!AUTO_COMPILE_SITEMAP) { return false; }

        // Get modified dates of sitemap and routes
        $sitemap_last_mod_time = file_exists("sitemap.xml") ? filemtime("sitemap.xml") : 0;
        $this->content_last_modified_time = filemtime(WEBROOT . '/Routes/Routes.php');

        // Get the latest modified date from pages folder
        foreach (glob(WEBROOT . '/App/Pages/*.php') as $file)
        {
            $page_last_modified = filemtime($file);

            if ($page_last_modified > $this->content_last_modified_time)
            {
                $this->content_last_modified_time = $page_last_modified;
            }
        }

        // Check is the content been modified after last sitemap generation
        return $this->content_last_modified_time > $sitemap_last_mod_time;
    }

    private function generateSitemap()
    {
        $sitemap = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
    
        foreach ($this->registered_routes as $route => $handler)
        {
            $url = $sitemap->addChild('url');
            $url->addChild('loc', htmlspecialchars(BASE_URL . rtrim($route, '/') . '/'));
            $url->addChild('lastmod', date('c', $this->content_last_modified_time));
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', '1.00');
        }
    
        // Create a DOMDocument and import the SimpleXMLElement
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $domElement = dom_import_simplexml($sitemap);
        $domElement = $dom->importNode($domElement, true);
        $domElement = $dom->appendChild($domElement);
    
        // Save the formatted XML
        $dom->save('sitemap.xml');
    }

    private function minifyHTML($buffer)
    {
        $minified_HTML = new HtmlMin();
        return $minified_HTML->minify($buffer);
    }

    protected function render($page, $variables = array())
    {
        // Make sure only one view can be called
        if (!empty($this->rendered_view))
            return;

        echo "<!-- Page Generated by Kristal Framework -->\n";

        // Include variables passed by the route function
        if (!empty($variables))
        {
            extract($variables);
        }
        
        // Include metadata from config.php
        $kristal_metadata = METADATA;

        // Start HTML Minification
        if (MINIFY_HTML)
        {
            ob_start(array($this, "minifyHTML"));
        }

        // Include header
        if (!file_exists(page("Base/header.php")))
        {
            if (PRODUCTION_MODE)
            {
                debuglog("Route: '" . $url_request['page'] . "' has handler '" . $this->registered_routes[$url_request['page']] . ", butthishandler function is not available.");
                exit();
            }
            else
            {
                exit("Route: '" . $url_request['page'] . "' has handler '" . $this->registered_routes[$url_request['page']] . ", butthishandler function is not available.");
            }
        }
        include_once page("Base/header.php");

        // Make sure page is a php file
        $page = ensurePHPExtension($page);

        // Include the requested page
        if (!file_exists(page($page)))
        {
            if (PRODUCTION_MODE)
            {
                debuglog("Tried to render page: " . $page . ", but template was not found at " . page($page) . ".");
            }
            else
            {
                exit("Tried to render page: " . $page . ", but template was not found at " . page($page) . ".");
            }
        }
        else
        {
            include_once page($page);
        }

        // Render PHP created javascript variables and code
        PHPJS::release();

        // Render footer
        if (!file_exists(page("Base/footer.php")))
        {
            if (PRODUCTION_MODE)
            {
                debuglog("Failed to build, " . page("Base/footer.php") . " was not found.");
            }
            else
            {
                exit("Failed to build, " . page("Base/footer.php") . " was not found.");
            }
        }
        else
        {
            include_once page("Base/footer.php");
        }

        // End HTML Minification
        if (MINIFY_HTML)
        {
            ob_end_flush();
        }
        
        $this->rendered_view = $page;
    }

    private function renderMaintenancePage()
    {
        if (Session::has("maintenance_access_granted"))
            return;

        if (ENABLE_MAINTENANCE_LOGIN)
        {
            $authenticationFailed = false;
            $authenticationAttemptLimitReached = false;
            $authenticationLockoutLabel = "";
            $loginAttempts = Session::get("maintenance_login_attempts", 1);
            $lockoutStartedAt = Session::get("maintenance_lockout_started_at", null);
    
            // Check have we reached login attempt count
            if ($loginAttempts >= MAINTENANCE_LOCKOUT_LIMIT)
            {
                $authenticationAttemptLimitReached = true;
    
                if ($lockoutStartedAt === null)
                {
                    $lockoutStartedAt = time();
                    Session::add("maintenance_lockout_started_at", $lockoutStartedAt);
                }
            }
    
            // Handle lockout countdown
            if ($authenticationAttemptLimitReached)
            {
                $remainingLockoutSeconds = MAINTENANCE_LOCKOUT_CLEAR_TIME - (time() - $lockoutStartedAt);
        
                if ($remainingLockoutSeconds > 0)
                {
                    if ($remainingLockoutSeconds > 60)
                    {
                        $authenticationLockoutLabel = floor($remainingLockoutSeconds / 60) . " min";
                    }
                    else
                    {
                        $authenticationLockoutLabel = $remainingLockoutSeconds . " s";
                    }
                }
                else
                {
                    $loginAttempts = 0;
                    $authenticationAttemptLimitReached = false;
                    Session::remove("maintenance_failed_attempts");
                    Session::remove("maintenance_lockout_started_at");
                }
            }
    
            // Handle authentication request
            if (!$authenticationAttemptLimitReached && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["maintenance-password"]))
            {
                if ($_POST["maintenance-password"] === MAINTENANCE_PASSWORD)
                {
                    Session::add("maintenance_access_granted", "Granted");
                    return;
                }
                else
                {
                    $authenticationFailed = true;
                    $loginAttempts++;
                    Session::add("maintenance_login_attempts", $loginAttempts);
                }
            }
        }
        
        // Render maintenance page
        $maintenancePagePath = WEBROOT . "/App/Pages/maintenance.php";
    
        if (!file_exists($maintenancePagePath))
        {
            if (PRODUCTION_MODE)
            {
                exit("Site is under maintenance");
            }
            else
            {
                exit("Maintenance page is missing. It should be located at " . $maintenancePagePath);
            }
        }

        include $maintenancePagePath;
        PHPJS::release();
        exit;
    }
}
