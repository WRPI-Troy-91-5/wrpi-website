</main>

<!-- Audio player -->
<div>
    <div id="audio-player">
        <audio id="player" src="https://stream.wrpi.org/mp3-320.mp3"></audio>
        <input id="playpause" type="image" src="/resources/img/play.png" onclick="toggle_play();"
               alt="Play/pause button">
        <label for="volume" class='ios-hidden'>Volume<input id="volume" class="slider ios-hidden" type="range" min="0" max="100" value="100"></label>
        
        
        <script>
            var copyLink = () => {
                navigator.clipboard.writeText("https://stream.wrpi.org/mp3-320.mp3");
                document.getElementById("copyconfirm").classList.toggle('blink-in');
                document.getElementById("streamlink").classList.toggle('shake');
                delay(1000).then(() => {document.getElementById("copyconfirm").classList.toggle('blink-in');});
                delay(200).then(() => {document.getElementById("streamlink").classList.toggle('shake');});
            }
            var delay = (ms) => {
                return new Promise(resolve => setTimeout(resolve, ms));
            }
        </script>
        

        <button id="streamlink" onclick="copyLink()" alt="copy stream link"> <img src="resources/img/link.svg"/> </button>
        <div id="copyconfirm"> Copied to clipboard </div>

        <!-- Commented out due to spacing concerns -->
        <!-- <p id="play_indicator">now playing...</p> -->
    </div>
</div>

<!-- Page footer -->
<footer class="footer">
    <div id="footer-left">
        WRPI Troy, 91.5 FM and streaming at WRPI.org
        <br>
        1 WRPI Plz, Troy, NY 12180-3590
        <br>
        See contact page for more details
    </div>
    <div id="footer-right">
        <a href="https://github.com/khealio/wrpi-website">WRPI.org Open Source Project</a>
        <br>
        Hosted with <3 by the WRPI Engineering Dept.
        <br>
        Visit the project to learn more and contribute!
    </div>
</footer>
</body>
</html>
