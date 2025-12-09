<?php

define("WEBROOT", __DIR__);

if (!file_exists(WEBROOT . "/App/Backend/Core/Initialize.php"))
{
    exit("Could not load framework core, please check index.php file.");
}

require_once WEBROOT . "/App/Backend/Core/Initialize.php";
