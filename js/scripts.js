$('.table .btn').on('click', function () {
    document.getElementById('clipLabel').innerHTML = this.dataset.title;
    document.getElementById('clipFooter').innerHTML = this.dataset.description;
    player.loadVideoById({
        'videoId': this.dataset.id,
        'startSeconds': this.dataset.start,
        'endSeconds': +this.dataset.start + +this.dataset.duration
    });
});

$('#clip').on('hidden.bs.modal', function (e) {
    document.getElementById('clipLabel').innerHTML = '';
    document.getElementById('clipFooter').innerHTML = '';
    player.stopVideo();
});



// Define player
var player;

// Add YouTube iframe API script tag
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// Creates an <iframe> (and YouTube player) after the API code downloads.
function onYouTubeIframeAPIReady() {
    player = new YT.Player('clipFrame', {
        events: {
            'onStateChange': onPlayerStateChange,
            'onPlaybackRateChange': onPlaybackRateChange
        },
        playerVars: {
            'autoplay': 0,
            'controls': 0,
            'enablejsapi': 1,
            'loop': 1,
            'modestbranding': 1,
            'showinfo': 0,
            'rel': 0

        }
    });
}

// On state change
function onPlayerStateChange(event) {
    /**
     * BUFFERING: 3
     * CUED: 5
     * ENDED: 0
     * PAUSED: 2
     * PLAYING: 1
     * UNSTARTED: -1
     */
    if (event.data == YT.PlayerState.ENDED) {
        player.seekTo(40746);
    }

    // Set play status
    if (event.data === YT.PlayerState.PLAYING) {
        $('#btn-play').addClass('on');
    } else {
        $('#btn-play').removeClass('on');
    }
}

function onPlaybackRateChange(event) {
    if (event.data === 1) {
        $('#btn-playback-rate').addClass('on');
    } else {
        $('#btn-playback-rate').removeClass('on');
    }
}

function togglePlay() {
    if (player.getPlayerState() === YT.PlayerState.PLAYING) {
        player.pauseVideo();
    } else {
        player.playVideo();
    }
}

function togglePlaybackRate() {
    if (player.getPlaybackRate() === 1) {
        player.setPlaybackRate(0.1);
    } else {
        player.setPlaybackRate(1);
    }
}

function skipBackward() {
    skipPlayback(false);
}
function skipForward() {
    skipPlayback(true);
}
function skipPlayback(direction) {
    direction = (direction) ? 1 : -1 ;
    var currentTime = player.getCurrentTime();
    var newTime = currentTime + (player.getPlaybackRate() * 5 * direction);

    player.seekTo(newTime)
}