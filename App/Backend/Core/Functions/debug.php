<?php defined("ACCESS") or exit("Access Denied");

// ------------------------------------------------------------------------------------------------
// Debug output for variables
// ------------------------------------------------------------------------------------------------

function debug($value, $name = null)
{
    if (PRODUCTION_MODE)
    {
        return;
    }

    ?>
    <style>
        .kristal-debug-block {
            font-family: Helvetica, Arial, sans-serif !important;
            background-color: #f1f1f1 !important;
            color: black !important;
            margin: 14px !important;
            padding: 18px 26px !important;
            border: 2px solid #87c1f5 !important;
            line-height: 25px !important;
            white-space: pre-wrap !important;
        }
        .kristal-debug-title {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }
    </style>
    <?php

    echo "<div class='kristal-debug-block'>";

    if (!empty($name))
    {
        echo "<span class='kristal-debug-title'>Debugging: " . sanitizeString($name) . "</span>";
    }

    echo htmlspecialchars(var_export($value, true), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

    echo "</div>";
}

// ------------------------------------------------------------------------------------------------
// Logging
// ------------------------------------------------------------------------------------------------

function debugLog($message, $severity = "Debug")
{
    if (!ENABLE_DEBUG_LOG)
    {
        return;
    }

    $time = date("Y-m-d H:i:s e");

    if (is_array($message))
    {
        $message = print_r($message, true);
    }

    $cleanMessage = "[" . $time . "] " . $severity . ": " . $message . "\n";

    error_log($cleanMessage, 3, DEBUG_LOG_PATH);
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
        debugLog($message . " in " . $file . " on line " . $line, $label);
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
            font-weight: bold;
            margin-bottom: 6px;
            display: block;
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
            body { background-color: #f1f1f1; font-family: Helvetica, Arial, sans-serif; }
            .kristal-fatal-block {
                background-color: white;
                border: 2px solid #e5e6e7;
                width: 60%;
                margin: 80px auto;
                padding: 30px;
                text-align: center;
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
        body { background-color: #f1f1f1; font-family: Helvetica, Arial, sans-serif; }
        .kristal-fatal-block {
            background-color: white;
            border: 2px solid #e5e6e7;
            width: 60%;
            margin: 80px auto;
            padding: 30px;
        }
        .kristal-code-block {
            background-color: #f9f9f9;
            border: 1px solid #cccccc;
            padding: 14px;
            margin-top: 20px;
            overflow-x: auto;
            font-family: Courier New, monospace;
            white-space: pre;
        }
        .kristal-highlight-line {
            background-color: #ffdddd;
            font-weight: bold;
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
