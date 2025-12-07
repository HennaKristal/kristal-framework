<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Debug output for variables
// ------------------------------------------------------------------------------------------------
function debug($value, $name = null)
{
    if (PRODUCTION_MODE || !ENABLE_DEBUG_DISPLAY)
        return;

    ?>
    <style>
        .kristal-debug-block {
            font-family: Helvetica, Arial, sans-serif !important;
            background-color: #f1f1f1 !important;
            color: black !important;
            margin: 14px !important;
            padding: 18px 26px !important;
            border: 2px solid #007700 !important;
            line-height: 25px !important;
            white-space: pre-wrap !important;
        }
        .kristal-debug-title {
            color: black !important;
            font-weight: bold !important;
            margin-bottom: 10px !important;
            display: block !important;
        }
    </style>
    <?php

    echo "<div class='kristal-debug-block'>";

    if (!empty($name))
    {
        echo "<span class='kristal-debug-title'>Debugging: $" . sanitizeString($name) . "</span>";
    }

    echo htmlspecialchars(var_export($value, true), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

    echo "</div>";
}


// ------------------------------------------------------------------------------------------------
// Logging
// ------------------------------------------------------------------------------------------------
function debuglog($message, $severity = "Debug")
{
    if (!ENABLE_DEBUG_LOG)
        return;

    $time = date("Y-m-d H:i:s e");

    if (is_array($message))
    {
        $message = print_r($message, true);
    }

    $cleanMessage = "[" . $time . "] " . $severity . ": " . $message . "\n";

    error_log($cleanMessage, 3, DEBUG_LOG_PATH);
}
