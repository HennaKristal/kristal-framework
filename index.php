<?php

define("WEBROOT", __DIR__);

if (!file_exists(WEBROOT . "/App/Backend/Core/Initialize.php"))
{
    exit("Can not find file 'App/Backend/Core/Initialize.php'");
}

require_once WEBROOT . "/App/Backend/Core/Initialize.php";
