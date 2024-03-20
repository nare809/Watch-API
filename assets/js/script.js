$(document).ready(function () {
    var lastRequest = 0;
    var startTime = 0;
    var url = atob(secret);
    var checkUrl;
    var limitRequest = 10;
    var resizer;
    var ext = ".json";

    $(".start").click(function () {
        $(".message").text("Creating systems and querying data...");
        request(0);
    });

    function request(num) {
        lastRequest = new Date().getTime() / 1000;
        if (!checkUrl) return;

        $.get(
            checkUrl,
            function (data) {
                if (data.status == 401) {
                    $(".message").text("An error has occurred, the server did not respond correctly");
                }

                if (typeof data.sleep != "undefined") {
                    if (isNaN(data.sleep)) data.sleep = 3000;

                    if (++num < limitRequest)
                        setTimeout(function () {
                            request(num);
                        }, data.sleep);
                    else $(".message").text("Could not get video.");
                } else if (typeof data.file !== "undefined") {
                    var tracks = data.tracks;
                    setup = {
                        file: atob(data.file),
                        type: data.type,
                        androidhls: data.androidhls,
                        hlshtml: data.hlshtml,
                        autostart: true,
                        stretching: "bestfit",
                        responsive: true,
                        width: $(window).width(),
                        height: $(window).height(),
                        tracks,
                    };
                    jwplayer("player").setup(setup);
                    window.onresize = function () {
                        clearTimeout(resizer);
                        resizer = setTimeout(resizeVideo, 100);
                    };
                    $(".message").hide();

                    jwplayer().on("ready", function (event) {
                        if (startTime != 0) jwplayer("player").seek(startTime);
                    });

                    jwplayer().on("seek", function (event) {
                        startTime = event.offset;
                    });
                    jwplayer().on("time", function (event) {
                        startTime = event.position;
                    });

                    jwplayer().on("error", function (event) {
                        var currentTime = new Date().getTime() / 1000;
                        if (currentTime - 5 < lastRequest) return;

                        jwplayer().remove();
                        $(".message").show().text("Loading...");
                        request(0);
                    });
                } else {
                    $(".message").text("An error has occurred!");
                }
            },
            "json"
        ).fail(function () {
            $(".message").text("An error has occurred, the server did not respond correctly");
        });
    }

    function resizeVideo() {
        jwplayer().resize($(window).width(), $(window).height());
    }

    function init() {
        checkUrl = url + encodeURIComponent(token);
    }

    init();
});
