<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <title>Music Viewer</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="viewer.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div id="header">

    <h1>190M Music Playlist Viewer</h1>
    <h2>Search Through Your Playlists and Music</h2>
</div>

<div id="listarea">
    <ul id="musiclist">
        <?php
        function isSong($line){
            return $line != "" and !str_starts_with($line, '#');
        }

        function cmpSize($a, $b){
            $a = filesize($a);
            $b = filesize($b);
            if ($a == $b) return 0;
            return ($a < $b) ? 1 : -1;
        }

        function listSongs($songs) {
            if (isset($_GET['shuffle'])){
                if ($_GET['shuffle'] == "on") shuffle($songs);
            }
            elseif (isset($_GET['bysize'])){
                if ($_GET['bysize'] == "on") usort($songs, "cmpSize");
            }
            foreach ($songs as $filename) {
                $size = filesize($filename);
                if ($size >= 0 and $size <= 1023){
                    $size = $size . ' b';
                } elseif ($size >= 1024 and $size <= 1048575){
                    $size = round($size/1024, 2) . ' kb';
                } else {
                    $size = round($size/1024/1024, 2) . ' mb';
                }

                print "<li class='mp3item'> <a href='$filename'>".basename($filename)." ($size) </a></li>";
            }
        }

        $param = isset($_GET['playlist'])? "&playlist=".$_GET['playlist']: "";
        print "<li id='shuffle'><a href='music.php?shuffle=on$param'>Shuffle</a>";
        print " | <a href='music.php?bysize=on$param'>By size</a></li>";

        if (isset($_GET['playlist'])) {
            $playlist =  file_get_contents('songs/'.$_GET['playlist']);

            $songs = array_filter(explode(PHP_EOL, $playlist), "isSong");
            foreach ($songs as &$song) {
                $song = 'songs/'.$song;
            }

            listSongs($songs);
            print "<li id='return'><a href='music.php'>Go back</a></li>";
        }
        else {
            listSongs(glob("songs/*.mp3"));
            foreach (glob("songs/*.m3u") as $filename) {
                $filename = basename($filename);
                print "<li class='playlistitem'> <a href='music.php?playlist=$filename'> $filename </a></li>";
            }
        }
        ?>
    </ul>
</div>
</body>
</html>
