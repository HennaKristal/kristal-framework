<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Bootstrap
// ------------------------------------------------------------------------------------------------
if (ENABLE_DEBUG)
{
    kristal_setDebugLevels();

    ini_set("display_errors", ENABLE_DEBUG_DISPLAY ? "1" : "0");
    ini_set("display_startup_errors", ENABLE_DEBUG_DISPLAY ? "1" : "0");
    ini_set("log_errors", ENABLE_DEBUG_LOG ? "1" : "0");
    ini_set("error_log", DEBUG_LOG_PATH);

    if (ENABLE_DEBUG_DISPLAY)
    {
        set_error_handler("kristal_errorHandler");
        register_shutdown_function("kristal_fatalErrorHandler");
    }
}
else
{
    ini_set("display_errors", "0");
    ini_set("display_startup_errors", "0");
    ini_set("log_errors", "0");
    error_reporting(0);
}


// ------------------------------------------------------------------------------------------------
// PHP error level configuration
// ------------------------------------------------------------------------------------------------
function kristal_setDebugLevels()
{
    $level = E_ALL;

    if (DEBUG_IGNORE_WARNINGS)
    {
        $level &= ~(E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING);
    }
    if (DEBUG_IGNORE_NOTICES)
    {
        $level &= ~(E_NOTICE | E_USER_NOTICE);
    }
    if (DEBUG_IGNORE_DEPRECATED)
    {
        $level &= ~(E_DEPRECATED | E_USER_DEPRECATED);
    }
    if (DEBUG_IGNORE_STRICT)
    {
        $level &= ~(E_STRICT);
    }

    error_reporting($level);
}


// ------------------------------------------------------------------------------------------------
// Error handler
// ------------------------------------------------------------------------------------------------
function kristal_errorHandler($type, $message, $file, $line)
{
    // Map to readable labels
    if ($type === E_WARNING || $type === E_USER_WARNING || $type === E_CORE_WARNING || $type === E_COMPILE_WARNING)
    {
        if (DEBUG_IGNORE_WARNINGS) { return; }
        $label = "Warning";
    }
    elseif ($type === E_NOTICE || $type === E_USER_NOTICE)
    {
        if (DEBUG_IGNORE_NOTICES) { return; }
        $label = "Notice";
    }
    elseif ($type === E_DEPRECATED || $type === E_USER_DEPRECATED)
    {
        if (DEBUG_IGNORE_DEPRECATED) { return; }
        $label = "Deprecated";
    }
    elseif ($type === E_STRICT)
    {
        if (DEBUG_IGNORE_STRICT) { return; }
        $label = "Strict";
    }
    else
    {
        $label = "Error";
    }

    kristal_errorOutput($label, $message, $file, $line);
}


// ------------------------------------------------------------------------------------------------
// Debug output for warnings and non-fatal errors
// ------------------------------------------------------------------------------------------------
function kristal_errorOutput($label, $message, $file, $line)
{
    if (ENABLE_DEBUG_LOG)
    {
        debuglog($message . " in " . $file . " on line " . $line, $label);
    }

    if (PRODUCTION_MODE)
    {
        return;
    }

    ?>
    <style>
        .kristal-warning-block {
            font-family: Helvetica, Arial, sans-serif !important;
            background-color: #fff3cd !important;
            color: black !important;
            margin: 14px !important;
            padding: 18px 26px !important;
            border: 2px solid orange !important;
        }
        .kristal-warning-title {
            color: black !important;
            font-weight: bold !important;
            margin-bottom: 6px !important;
            display: block !important;
        }
    </style>
    <?php

    echo "<div class='kristal-warning-block'>";
    echo "<span class='kristal-warning-title'>" . $label . ":</span>";
    echo sanitizeString($message) . "<br>";
    echo "Occurred on line " . sanitizeString($line) . " in file " . sanitizeString($file);
    echo "</div>";
}


// ------------------------------------------------------------------------------------------------
// Fatal error handler
// ------------------------------------------------------------------------------------------------
function kristal_fatalErrorHandler()
{
    $error = error_get_last();

    if (!$error)
    {
        return;
    }

    $isFatal =
        $error["type"] === E_ERROR ||
        $error["type"] === E_PARSE ||
        $error["type"] === E_CORE_ERROR ||
        $error["type"] === E_COMPILE_ERROR ||
        $error["type"] === E_RECOVERABLE_ERROR ||
        $error["type"] === E_USER_ERROR;

    if (!$isFatal)
    {
        return;
    }

    // Clear output buffer safely
    if (ob_get_level() > 0)
    {
        ob_end_clean();
    }

    // Production mode
    if (PRODUCTION_MODE)
    {
        ?>
        <style>
            body {
                background-color: #f1f1f1 !important;
                font-family: Helvetica, Arial, sans-serif !important;
            }
            .kristal-fatal-block {
                background-color: white !important;
                color: black !important;
                border: 2px solid #aa0000 !important;
                width: 60% !important;
                margin: 80px auto !important;
                padding: 30px !important;
                text-align: center !important;
            }
        </style>
        <div class="kristal-fatal-block">
            A critical error has occurred. Please contact the site administrator.
        </div>
        <?php
        return;
    }

    // Development mode
    ?>
    <style>
        body {
            background-color: #f1f1f1 !important;
            font-family: Helvetica, Arial, sans-serif !important;
        }
        .kristal-fatal-block {
            background-color: white !important;
            color: black !important;
            border: 2px solid #aa0000 !important;
            width: 60% !important;
            margin: 80px auto !important;
            padding: 30px !important;
        }
        .kristal-code-block {
            background-color: #f9f9f9 !important;
            color: black !important;
            border: 1px solid #cccccc !important;
            padding: 14px !important;
            margin-top: 20px !important;
            overflow-x: auto !important;
            font-family: Courier New, monospace !important;
            white-space: pre !important;
        }
        .kristal-highlight-line {
            background-color: #ffdddd !important;
            font-weight: bold !important;
        }
    </style>
    <?php

    echo "<div class='kristal-fatal-block'>";
    echo "<strong>Fatal Error:</strong> " . sanitizeString($error["message"]) . "<br><br>";
    echo "Occurred on line " . sanitizeString($error["line"]) . " in file " . sanitizeString($error["file"]);

    $lines = @file($error["file"]);
    if ($lines)
    {
        $start = max(0, $error["line"] - 11);
        $end = min(count($lines), $error["line"] + 10);

        echo "<div class='kristal-code-block'>";
        for ($i = $start; $i < $end; $i++)
        {
            $number = $i + 1;
            $safeLine = htmlspecialchars($lines[$i]);

            if ($number === $error["line"])
            {
                echo "<div class='kristal-highlight-line'>" . $number . ": " . $safeLine . "</div>";
            }
            else
            {
                echo "<div>" . $number . ": " . $safeLine . "</div>";
            }
        }
        echo "</div>";
    }

    echo "</div>";
}
