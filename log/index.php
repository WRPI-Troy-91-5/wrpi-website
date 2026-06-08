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
            Use this page to download a specific timeframe of audio from our vast audio log.
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
    <script>
        // Create query string to pass parameters to the helper
        const parameters = new URLSearchParams({
            start_date: "<?php echo $_POST['start-date'] ?>",
            start_time: "<?php echo $_POST['start-time'] ?>",
            end_date:   "<?php echo $_POST['end-date'] ?>",
            end_time:   "<?php echo $_POST['end-time'] ?>"
        });

        // Create an event source to get the script log from the server using server-sent event data
        const eventSource = new EventSource("/log/retrieve_log_helper.php?" + parameters);

        // Handle messages from the server using this event source
        eventSource.onmessage = (event) => {
            if (event.data == "[EOF]") {
                eventSource.close();
                return;
            }
            const log_output = document.getElementById("log-retrieval-output");
            if (log_output != null) {            
                log_output.innerHTML += "<p>" + event.data + "</p>";
            }
        };
    </script>
<?php
include("../.includes/footer.inc.php");
?>
