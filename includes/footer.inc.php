</main>

<!-- Audio player -->
<div>
    <div id="audio-player">
        <audio id="player" src="https://stream.wrpi.org/mp3-320.mp3"></audio>
        <input id="playpause" type="image" src="/wrpi-website/resources/img/play.png" onclick="toggle_play();" alt="Play/pause button">
        <label for="volume">Volume<input id="volume" class="slider" type="range" min="0" max="100" value="100"></label>

        <!-- Commented out due to spacing concerns -->
        <!-- <p id="play_indicator">now playing...</p> -->
    </div>
</div>

<!-- Page footer -->
<footer class="footer">
    WRPI Troy, 91.5 FM and streaming at WRPI.org
    <br>
    1 WRPI Plz, Troy, NY 12180-3590
    <br>
    See contact page for more details
</footer>
</body>
</html>