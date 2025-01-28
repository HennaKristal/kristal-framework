# Kristal Framework

Kristal Framework is a PHP-based architecture designed to help developers create websites with a clean and straightforward MVC environment.
The framework is publicly available under the MIT License, granting individuals the latitude to utilize, modify, and distribute it as they deem appropriate.



## Important

This project was initiated as a learning exercise to delve into the mechanics of PHP frameworks while allowing for some creative experimentation.
It is not designed for use in production environments. Please be advised that the framework may contain security vulnerabilities, and there is the possibility that updates to the framework may cease without notice.
As such, it is advised to use this framework strictly for exploratory or experimental applications.



## Server Requirements

* Operating System: Linux is recommended (although other OS might work).
* Web Server: Apache or LiteSpeed with .htaccess support is ideal (other servers may vary in compatibility).
* PHP: Minimum version 7.4 ideally 8.x
* Composer: Necessary for managing backend dependencies.



## Database Requirements

* PDO PHP Extension: Ensure it is enabled and functional.
* Database Engine: MySQL or MariaDB is preferred (other databases might work, but compatibility is not guaranteed).



## Installing

1. Rename 'config-example.php' to 'config.php' and configure the framework settings.
2. Upload the framework files to a server or local development environment.
3. Run 'composer install --prefer-dist --optimize-autoloader' at the root folder to install dependencies from composer.json file.

After completing these steps you should now be able to access the built-in demo page by going to your server's URL.



## Troubleshooting

If you have issues with navigating to pages, go to .htaccess file and change redirect path at the routing controller section from relative path

    RewriteRule ^(.+)$ index.php [L]
    
to a full path

    RewriteRule ^(.+)$ https://www.example.com/index.php [L]

As this is a personal project, compatibility with all systems out-of-the-box is not guaranteed.
Should you encounter any issues while utilizing the framework,
please do not hesitate to reach out via email at hennakristal@outlook.com.
Your feedback will aid in both resolving your issue and further refining the framework.



## Documentation

Documentation for the framework can be found [here](https://framework.hennakristal.com)
