<?php
// Error handler area
set_error_handler("customError");

// It will run whenever an error is detected
function customError($errno, $errstr)
{
    log_this("Error: [$errno] $errstr", "err");
    die();
}

// Logs all activities in the log file
function log_this($content = '', $type = "log")
{
    $log = date("d-m-y H:i:s") . PHP_EOL;
    $log .= "user: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
    $log .= "Browser: " . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
    $log .= "Request: " . $_SERVER['REQUEST_METHOD'] . PHP_EOL;
    foreach ($_GET as $key => $value)
        {
        $log .= $key . ': ' . $value . PHP_EOL;
    }
    if (strlen($content) > 0)
        $log .= $content . PHP_EOL;
    $log .= "-------------------------" . PHP_EOL;
    file_put_contents('./logs/' . $type . '_' . date("ymd") . '.txt', $log, FILE_APPEND);
}
?>