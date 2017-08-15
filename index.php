<?php
session_start();
function readCSV($csvFile)
{
    $result = [];
    if (file_exists($csvFile)) {
        $handle = fopen($csvFile, 'r');
        while (!feof($handle)) {
            $row = fgetcsv($handle, 1024);
            if ($row) {
                $result[] = $row;
            }
        }
        fclose($handle);
    }
    return $result;
}
$clips = readCSV('clips.csv');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YouTube Clips</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>
<body>


<div class="container" role="main">
    <div class="page-header">
        <h2>YouTube Clips</h2>
    </div>

    <div class="well">
        <p>Add new clips by filling in the fields at the bottom of the table.</p>
        <ul>
            <li><strong>Description: </strong> Short description of what the clip is about</li>
            <li><strong>YouTube ID: </strong> The ID of a video <code>(3FkUUk8ChIY)</code> or the url <code>(https://www.youtube.com/watch?v=3FkUUk8ChIY)</code>
            </li>
            <li><strong>Start: </strong> Moment when the clip needs to start (hours:minutes:seconds)</li>
            <li><strong>Duration: </strong> How many seconds is the clip (default is 10 seconds)</li>
        </ul>
    </div>

    <?php if (isset($_SESSION['feedback'])): ?>
        <div class="alert alert-<?php echo $_SESSION['feedback']['class']; ?>" role="alert">
            <span class="glyphicon glyphicon-<?php echo $_SESSION['feedback']['icon']; ?>"></span>
            <?php echo $_SESSION['feedback']['message']; ?>
        </div>
        <?php unset($_SESSION['feedback']); ?>
    <?php endif; ?>

    <form id="clipForm" method="post" action="add.php">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed">
                <colgroup>
                    <col width="1"/>
                    <col/>
                    <col width="150"/>
                    <col width="100"/>
                    <col width="100"/>
                    <col width="1"/>
                </colgroup>
                <thead>
                <tr>
                    <th class="text-nowrap"></th>
                    <th class="text-nowrap">Description</th>
                    <th class="text-nowrap">YouTube ID</th>
                    <th class="text-nowrap">Start</th>
                    <th class="text-nowrap">Duration</th>
                    <th class="text-nowrap"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clips as $key => $clip): ?>
                    <?php
                    $startParts = array_reverse(explode(':',$clip[2]));
                    $start = 0;
                    foreach ($startParts as $key => $value) {
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
                    ?>
                    <tr>
                        <td>
                            <a href="delete.php?delete=<?php echo $key; ?>" class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to delete this clip?');">
                                <span class="glyphicon glyphicon-remove"></span>
                            </a>
                        </td>
                        <td><strong><?php echo $clip[3]; ?></strong><br/><?php echo $clip[4]; ?></td>
                        <td class="text-nowrap">
                            <a href="https://www.youtube.com/watch?v=<?php echo $clip[1]; ?>"
                               target="_blank"><?php echo $clip[1]; ?></a>
                        </td>
                        <td class="text-nowrap"><?php echo $clip[2]; ?></td>
                        <td class="text-nowrap"><?php echo $clip[5]; ?></td>
                        <td class="text-nowrap">
                            <button type="button" class="btn btn-primary"
                                    data-toggle="modal"
                                    data-target="#clip"
                                    data-title="<?php echo $clip[3]; ?>"
                                    data-id="<?php echo $clip[1]; ?>"
                                    data-start="<?php echo $start; ?>"
                                    data-duration="<?php echo $clip[5]; ?>"
                                    data-description="<?php echo $clip[4]; ?>">
                                <span class="glyphicon glyphicon-play"></span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php
                $youtubeId = isset($_SESSION['youtube_id']) ? $_SESSION['youtube_id'] : '';
                $start = isset($_SESSION['start']) ? $_SESSION['start'] : '0:00:00';
                $duration = (isset($_SESSION['duration']) && $_SESSION['duration']) ? $_SESSION['duration'] : 10;
                ?>
                <tr>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" id="description" name="description"
                               placeholder="Description" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="youtube_id" name="youtube_id"
                               placeholder="YouTube ID" value="<?php echo $youtubeId; ?>" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="start" name="start" placeholder="Start"
                               value="<?php echo $start; ?>" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="duration" name="duration" placeholder="Duration"
                               value="<?php echo $duration; ?>" required>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-block btn-success"><span
                                    class="glyphicon glyphicon-plus"></span></button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

<div class="modal fade" id="clip" tabindex="-1" role="dialog" aria-labelledby="clipLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="clipLabel"></h4>
            </div>
            <div class="modal-body">
                <div class="iframe-wrapper">
                    <div id="clipFrame"></div>
                </div>
                <div id="controls">
                    <button id="btn-skip-backward" class="btn btn-default" onclick="skipBackward();"><span class="glyphicon glyphicon-backward"></span></button>
                    <button id="btn-playback-rate" class="btn btn-default on" onclick="togglePlaybackRate();"><span class="glyphicon glyphicon-facetime-video"></span><span class="glyphicon glyphicon-picture"></span></button>
                    <button id="btn-play" class="btn btn-default" onclick="togglePlay();"><span class="glyphicon glyphicon-play"></span><span class="glyphicon glyphicon-pause"></span></button>
                    <button id="btn-skip-forward" class="btn btn-default" onclick="skipForward();"><span class="glyphicon glyphicon-forward"></span></button>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-left" id="clipFooter"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="js/scripts.js?v=1.0"></script>
</body>
</html>