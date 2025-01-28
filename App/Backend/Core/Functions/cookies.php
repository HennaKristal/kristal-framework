<?php defined("ACCESS") or exit("Access Denied");

setcookie(COOKIE_NAME, bin2hex(random_bytes(16)), [
    'expires' => time() + COOKIE_EXPIRE_TIME,
    'path' => "/",
    'secure' => PRODUCTION_MODE ? true : false, // Ensures the cookie is only sent over HTTPS
    'httponly' => true,                         // Ensures the cookie is not accessible by client-side scripts
]);
