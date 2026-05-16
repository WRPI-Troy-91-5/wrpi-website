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
    <form id="datetime-form">
        <div id="datetime-form-container">
            <div id="datetime-form-start">
                <h2>Starting Timeframe</h2>
                <input type="date">
                <input type="time">
            </div>
    
            <div id="datetime-form-end">
                <h2>Ending Timeframe</h2>
                <input type="date">
                <input type="time">
            </div>
        </div>

        <br><br>
        <div id="datetime-form-submit">
            <button>Submit</button>
        </div>
    </form>

<?php
include("../.includes/footer.inc.php");
