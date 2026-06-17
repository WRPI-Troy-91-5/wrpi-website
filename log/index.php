<?php
include("../.includes/header.inc.php");
?>
    <!-- Includes styling from the page-specific stylesheet -->
    <link rel="stylesheet" type="text/css" href="../resources/styles/audio-log.css">

    <div class="body-text">
        <!-- Page heading -->
        <h1 class="main-title">
            Audio Log Retriever
        </h1>

        <p class="main-subtitle">
            Use this page to download a specific timeframe of audio from our vast audio log.<br>
        </p>
        <p class="secondary-subtitle">
            DO NOTE: There is a 6 hour maximum limit in terms of the length of the log you wish to request.<br>
            If you wish to request anything longer than this, <a href="/contact">contact</a> a member of the WRPI ecomm!
        </p>
    </div>
    <!-- Main form -->
    <br>
    <form id="datetime-form" method="post">
        <div id="datetime-form-container">
            <div id="datetime-form-start">
                <h2>Starting Timeframe</h2>
                <input required name="start-date" type="date">
                <input required name="start-time" type="time">
            </div>
    
            <div id="datetime-form-end">
                <h2>Ending Timeframe</h2>
                <input required name="end-date" type="date">
                <input required name="end-time" type="time">
            </div>
        </div>

        <br><br>
        <div id="datetime-form-submit">
            <?php
            if(isset($_POST['start-date'])) {
                //
                // Process Form Input Here
                //
                echo "<p>Retrieving audio from " . $_POST["start-date"] . " " . $_POST["start-time"] . " to " . $_POST["end-date"] . " " . $_POST["end-time"] . "...</p>";
                echo "<div id='log-retrieval-output'></div>";
                
            }
            ?>
            <button type="submit" name="submit">Submit</button>
        </div>
    </form>
    <?php if(isset($_POST['start-date'])) { 
    $filename = date('Y-m-d', strtotime(trim($_POST['start-date']))) . "-" . date('H-i-s', strtotime(trim($_POST['start-time'])));
    $filename = $filename . "-" . date('Y-m-d', strtotime(trim($_POST['end-date']))) . "-" . date('H-i-s', strtotime(trim($_POST['end-time']))) . ".mp3";
    echo '
    <script>
        // Create query string to pass parameters to the helper
        const parameters = new URLSearchParams({
            start_date: "' . $_POST['start-date'] . '",
            start_time: "' . $_POST['start-time'] . '",
            end_date:   "' . $_POST['end-date'] . '",
            end_time:   "' . $_POST['end-time'] . '"
        });

        // Create an event source to get the script log from the server using server-sent event data
        const eventSource = new EventSource("/log/retrieve_log_helper.php?" + parameters);

        // Handle messages from the server using this event source
        eventSource.onmessage = (event) => {
            if (event.data == "[EOF] 0") {
                eventSource.close();
                // Redirect to download
                //   - Creates a temp hyperlink with the download attribute
                const download_link = document.createElement("a");
                download_link.href = "/log/retrieved/'. $filename .'";
                download_link.download = "' . $filename . '";
                document.body.appendChild(download_link);
                download_link.click();
                document.body.removeChild(download_link);
                return;
            }
            else if (event.data.includes("[EOF]")) {
                // Exit on error
                log_output.innerHTML += "<p>Exited with error code: " + event.data + "</p>";
                log_output.scrollTop = log_output.scrollHeight;
                eventSource.close();
                return;
            }
            const log_output = document.getElementById("log-retrieval-output");
            if (log_output != null) {            
                log_output.innerHTML += "<p>" + event.data + "</p>";
                log_output.scrollTop = log_output.scrollHeight;
            }
        };
    </script>
    '; } ?>
<?php
include("../.includes/footer.inc.php");
?>
