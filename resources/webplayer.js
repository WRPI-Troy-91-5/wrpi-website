// Global variables needed for pulsing "now playing..." text
let play_text_fade_percent = 0;
let play_text_fade_percent_inc = false;
const play_text_fade_percent_max = 100;
const play_text_fade_percent_min = 40;
const play_text_fade_percent_step = 1;

function toggle_play() {
    const player = document.getElementById("player");
    const play_button = document.getElementById("playpause");
    const play_text = document.getElementById("play_indicator");

    if (player.paused) {
        player.play();
        play_button.src = "./resources/pause.png";
        play_text_fade_percent = 100;
    }
    else {
        player.pause();
        play_button.src = "./resources/play.png";
        play_text_fade_percent = 0
        play_text.style.opacity = "0%";
        
    }
}

function update_vol() {
    const volume = document.getElementById("volume");
    const player = document.getElementById("player");

    player.volume = (parseInt(volume.value)/100);
}

function pulse_play_text() {
    // Condition for when the text should be hidden
    if (play_text_fade_percent === 0) {
        return;
    }

    // Update fade percent
    if (play_text_fade_percent_inc) {
        play_text_fade_percent += play_text_fade_percent_step;
    }
    else {
        play_text_fade_percent -= play_text_fade_percent_step;
    }
    if (play_text_fade_percent <= play_text_fade_percent_min) {
        play_text_fade_percent_inc = true;
    }
    else if (play_text_fade_percent >= play_text_fade_percent_max) {
        play_text_fade_percent_inc = false;
    }

    // Set fade percent to element
    const play_text = document.getElementById("play_indicator");
    play_text.style.opacity = play_text_fade_percent+"%";
}

$(document).ready(function () {
    $("#volume").on("input", update_vol);

    // Set the interval to pulse the "now playing..." text when the stream is playing
    setInterval(pulse_play_text, 40);
});
