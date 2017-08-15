<?php
session_start();
$youtubeId = false;
$duration = $_POST['duration'];
if (isset($_POST['youtube_id'], $_POST['start'], $_POST['description'], $_POST['duration'])) {

    $youtubeId = $_POST['youtube_id'];

    $parts = parse_url($youtubeId);
    if ($parts && isset($parts['query'])) {
        parse_str($parts['query'], $query);
        if (isset($query['v'])) {
            $youtubeId = $query['v'];
        }
    }

    $api = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?id={$youtubeId}&key=AIzaSyCic-caVd5wWge1UEVDBBGWhbl8qGfYp9I&fields=items(id,snippet(title))&part=snippet"),true);
    if (isset($api['items'][0]['snippet']['title'])) {
        $start = 0;
        $time = array_reverse(explode(':', $_POST['start']));
        foreach ($time as $key => $value) {
            switch ($key) {
                case 0:
                    $start += $value;
                    break;
                case 1:
                    $start += ($value * 60);
                    break;
                case 2:
                    $start += ($value * 60 * 60);
                    break;
            }
        }
        $end = $start + $duration;
        $url = "http://www.youtube.com/embed/{$youtubeId}?autoplay=1&rel=0&start={$start}&end={$end}";

        $fp = fopen('clips.csv', 'a');
        fputcsv($fp, [
            htmlspecialchars($url),
            htmlspecialchars($youtubeId),
            htmlspecialchars($_POST['start']),
            htmlspecialchars($api['items'][0]['snippet']['title']),
            htmlspecialchars($_POST['description']),
            htmlspecialchars($_POST['duration']),
        ]);
        fclose($fp);

        $duration = false;

        $_SESSION['feedback'] = [
            'class' => 'success',
            'icon' => 'ok',
            'message' => 'The clip has been successfully added'
        ];
    } else {
        $_SESSION['feedback'] = [
            'class' => 'danger',
            'icon' => 'remove',
            'message' => 'Unable to find YouTube video'
        ];
    }
} else {
    $_SESSION['feedback'] = [
        'class' => 'danger',
        'icon' => 'remove',
        'message' => 'Not all form fields were filled'
    ];
}

$_SESSION['duration'] = $duration;
$_SESSION['youtube_id'] = $youtubeId ? : $_POST['youtube_id'] ;
$_SESSION['start'] = $_POST['start'];

header("Location: index.php");
die();