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

if (isset($_GET['delete'])) {

    $clips = readCSV('clips.csv');

    if (isset($clips[$_GET['delete']])) {
        $_SESSION['feedback'] = [
            'class' => 'success',
            'icon' => 'ok',
            'message' => 'Clip is successfully deleted'
        ];

        unset($clips[$_GET['delete']]);

        $fp = fopen('clips.csv', 'w');
        foreach ($clips as $clip) {
            fputcsv($fp, $clip);
        }
        fclose($fp);
    } else {
        $_SESSION['feedback'] = [
            'class' => 'danger',
            'icon' => 'remove',
            'message' => 'Error deleting clip'
        ];
    }
} else {
    $_SESSION['feedback'] = [
        'class' => 'danger',
        'icon' => 'remove',
        'message' => 'Error deleting clip'
    ];
}

header("Location: index.php");
die();