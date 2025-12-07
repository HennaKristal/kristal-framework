<?php

define("WEBSITE_ROOT", __DIR__);

if (!file_exists(WEBSITE_ROOT . "/App/Backend/Core/Framework.php"))
{
    exit("Can not find file 'App/Backend/Core/Framework.php'");
}

require_once WEBSITE_ROOT . "/App/Backend/Core/Framework.php";
