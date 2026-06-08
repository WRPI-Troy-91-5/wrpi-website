<?php
date_default_timezone_set("America/New_York");
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("X-Accel-Buffering: no");

// Define the standard file descriptors
$descriptors = [
    0 => ["pipe", "r"],
    1 => ["pipe", "w"],
    2 => ["pipe", "w"]
];

// Open python script with popen in order to read the command line output of the script
$process = proc_open("/bin/python ./retrieve_log.py " . $_GET["start_date"] . " " . $_GET["start_time"] . " " . $_GET["end_date"] . " " . $_GET["end_time"], $descriptors, $pipes);

// Handle if the process was not started correctly
if (!is_resource($process)) {
    echo "data: Failed to start process\n\n";
    exit;
}

// Set output pipes to non-blocking to prevent halting when the pipe is not empty
stream_set_blocking($pipes[1], false);
stream_set_blocking($pipes[2], false);

// Write the output of the command to the log output box
while (true) {
    $stdout = fgets($pipes[1]);
    $stderr = fgets($pipes[2]);

    if ($stdout) {
        echo "data: " . htmlspecialchars($stdout) . "\n\n";
        ob_flush();
        flush();
    }
    if ($stderr) {
        echo "data: " . htmlspecialchars($stderr) . "\n\n";
        ob_flush();
        flush();
    }

    // If process is finished executing, break from loop
    $status = proc_get_status($process);
    if (!$status['running'] && !$stdout && !$stderr) {
        break;
    }

    // Sleep to avoid high idle usage
    usleep(10000);
}

// Close the standard pipes
foreach ($pipes as $pipe) fclose($pipe);

// CLose the process pipe
proc_close($process);

// Clear the buffer if anything remains
if (ob_get_contents()) {
    ob_end_flush();
}

// Notify the PHP script that execution has completed
echo "data: [EOF]\n\n";
?>
