<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Tab title -->
    <title>WRPI Troy, 91.5 FM</title>

    <!-- Ensures basic compatability/behavior predictability -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Includes styling from style.css and Google Fonts -->
    <link rel="stylesheet" type="text/css" href="../resources/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cabin">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Gloock">

    <!-- Favicon -->
    <link rel='icon' href='../resources/favicon.ico' type='image/x-icon'>
</head>

<body>
<!-- Banner -->
<div class="main-banner">
    <script src="../resources/dropdown.js"></script>
    <div class="main-banner-content">
        <div class="home-logo">
            <!-- Logo -->
            <a href="../">
                <img class="home-logo" src='../resources/logo.png' alt="WRPI Logo">
            </a>
        </div>

        <div class="dropdown">
            <button onclick="openDropdown('links-1')" class="dropbtn">Engage</button>
            <div class="dropdown-content" id="links-1">
                <a href="../listen/">How To Listen</a>
                <a href="../about/">About Us</a>
            </div>
        </div>

        <div class="dropdown">
            <button onclick="openDropdown('links-2')" class="dropbtn">Reach Out</button>
            <div class="dropdown-content" id="links-2">
                <a href="../afterdark/">After Dark</a>
                <a href="../wgoh/">What's Going On Here?</a>
                <a href="../contact/">Contact Info</a>
            </div>
        </div>
    </div>
</div>