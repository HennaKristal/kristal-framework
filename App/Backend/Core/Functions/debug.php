<?php defined("ACCESS") or exit("Access Denied");


function debug($variable, $name = null)
{
    if (PRODUCTION_MODE) { return; }

    ?>
    <style>
        .kristal-debug-div {
            font-family: Helvetica, Arial, sans-serif !important;
            background-color: #f1f1f1 !important;
            color: black !important;
            margin: 10px !important;
            padding: 15px 25px !important;
            border: 2px solid lightblue !important;
            line-height: 25px !important;
        }
    </style>
    <?php

    echo "<pre class='kristal-debug-div'>";

    if (isset($name))
    {
        echo "<strong>Debugging:</strong> $name<br><br>";
    }

    echo var_export($variable, true);
    echo "</pre>";
}


function debugLog($message, $severity = "Debug")
{
    if (!ENABLE_DEBUG_LOG) { return; }
    
    $time = date('d-M-Y H:i:s e');

    if (is_array($message))
    {
        $message = print_r($message, true);
    }

    $log = "[$time] $severity:  $message\n";
    error_log($log, 3, DEBUG_LOG_PATH);
}


function kristal_setDebugLevels()
{
    $error_level = E_ALL;

    if (DEBUG_IGNORE_WARNINGS)
    {
        $error_level &= ~(E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING);
    }
    if (DEBUG_IGNORE_NOTICES)
    {
        $error_level &= ~(E_NOTICE | E_USER_NOTICE);
    }
    if (DEBUG_IGNORE_DEPRECATED)
    {
        $error_level &= ~(E_DEPRECATED | E_USER_DEPRECATED);
    }
    if (DEBUG_IGNORE_STRICT)
    {
        $error_level &= ~(E_STRICT);
    }

    error_reporting($error_level);
}


function kristal_errorHandler($error_type, $message, $file, $line)
{
    if ($error_type === E_WARNING || $error_type === E_CORE_WARNING || $error_type === E_COMPILE_WARNING || $error_type === E_USER_WARNING)
    {
        if (DEBUG_IGNORE_WARNINGS) { return; }
        $type = "Warning";
    }
    elseif ($error_type === E_NOTICE || $error_type === E_USER_NOTICE)
    {
        if (DEBUG_IGNORE_NOTICES) { return; }
        $type = "Notice";
    }
    elseif ($error_type === E_DEPRECATED || $error_type === E_USER_DEPRECATED)
    {
        if (DEBUG_IGNORE_DEPRECATED) { return; }
        $type = "Deprecated";
    }
    elseif ($error_type === E_STRICT)
    {
        if (DEBUG_IGNORE_STRICT) { return; }
        $type = "Strict";
    }
    else
    {
        $type = "Error";
    }

    kristal_warningHandler($type, $message, $file, $line);
}


function kristal_warningHandler($type, $message, $file, $line)
{
    if (ENABLE_DEBUG_LOG)
    {
        $time = date('d-M-Y H:i:s e');
        $log = "[$time] $type:  $message in $file on line $line\n";
        error_log($log, 3, DEBUG_LOG_PATH);
    }

    if (!PRODUCTION_MODE)
    {
        ?>
        <style>
            .kristal-warning-div {
                font-family: Helvetica, Arial, sans-serif !important;
                background-color: #fff3cd !important;
                color: black !important;
                margin: 10px !important;
                padding: 15px 25px !important;
                border: 2px solid orange !important;
            }
        </style>
        <?php

        echo "<div class='kristal-warning-div'>";
        echo "<strong>$type:</strong> $message<br>";
        echo "$type occurred on line $line in the file $file<br>";
        echo "</div>";
    }
}


function kristal_fatalErrorHandler()
{
    $error = error_get_last();

    if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_USER_ERROR)))
    {
        // Check if output buffering is active before attempting to clean
        if (ob_get_level() > 0) { ob_end_clean(); }
        
        ?>
        <style>
            html, body {
                background-color: #f1f1f1;
                font-family: Helvetica, Arial, sans-serif;
                color: black;
            }

            .fatal-error-div {
                background-color: white;
                border: 2px solid #e5e6e7;
                width: 56%;
                margin: 80px 0px 0px 20%;
                padding: 30px 2%;
                word-wrap: break-word;
            }

            @media only screen and (max-width: 1000px) {
                .fatal-error-div {
                    width: 74%;
                    margin: 80px 0px 0px 10%;
                    padding: 30px 3%;
                }
            }

            @media only screen and (max-width: 400px) {
                .fatal-error-div {
                    width: 80%;
                    margin: 80px 0px 0px 5%;
                    padding: 30px 5%;
                }
            }

            .prodcution {
                text-align: center;
            }
            
            .code-snippet {
                font-family: "Courier New", monospace;
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                padding: 10px;
                margin-top: 20px;
                text-wrap: nowrap;
                overflow-y: hidden;
            }

            .code-snippet .highlight {
                background-color: #ffcccb;
                font-weight: bold;
                display: inline-table;
                padding-bottom: 10px;
                padding-top: 10px;
                font-size: 105%;
            }
        </style>
        <?php

        if (PRODUCTION_MODE)
        {
            echo "<div class='fatal-error-div prodcution'>";
            echo "A critical error has occurred on this website. Please notify the site owner about this issue.";
            echo "</div>";
        }
        else
        {
            echo "<div class='fatal-error-div development'>";
            echo "<strong>Fatal Error:</strong> {$error['message']}";
            echo "<br><br>";
            echo "Fatal error occurred on line {$error['line']} in the file {$error['file']}";

            echo "<div class='code-snippet'>";
            $file_lines = file($error['file']);
            $start_line = max(0, $error['line'] - 11);
            $end_line = min(count($file_lines), $error['line'] + 10);
            for ($i = $start_line; $i < $end_line; $i++)
            {
                $line_number = $i + 1;
                $highlight_class = ($line_number === $error['line']) ? 'highlight' : '';
                $indented_line = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', htmlentities($file_lines[$i]));
                $indented_line = str_replace("  ", '&nbsp;&nbsp;', $indented_line);
                echo "<div class='{$highlight_class}'>" . $line_number . ": " . $indented_line . "</div>";
            }
            echo "</div>";

            echo "</div>";
        }
    }
}


if (ENABLE_DEBUG)
{
    kristal_setDebugLevels();

    ini_set('display_errors', ENABLE_DEBUG_DISPLAY ? '1' : '0');
    ini_set('display_startup_errors', ENABLE_DEBUG_DISPLAY ? '1' : '0');
    ini_set('log_errors', ENABLE_DEBUG_LOG ? '1' : '0');
    ini_set('error_log', DEBUG_LOG_PATH);

    if (ENABLE_DEBUG_DISPLAY)
    {
        set_error_handler("kristal_errorHandler");
        register_shutdown_function("kristal_fatalErrorHandler");
    }
}
else
{
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '0');
    error_reporting(0);
}
