<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function make_directory($path) {
  if (!file_exists($segment_directory)) {
    mkdir($path, 0777, true);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $root_directory = dirname(__FILE__);

  $video_title =  $_POST['title'];

  make_directory($root_directory . '/segments');
  $segment_directory = $root_directory . '/segments/' . $video_title . '/';

  make_directory($segment_directory);
  
  $filename = $segment_directory . $_FILES['segment']['name'] . '.webm';

  // Write the video segment data to disk
  $handle = fopen($filename, 'w');

  $contents = file_get_contents($_FILES['segment']['tmp_name']);
  
  fwrite($handle, file_get_contents($_FILES['segment']['tmp_name']));
  
  fclose($handle);

  // If this is the last segment, merge all segments into an MP4 file
  if ($_POST['last'] === "true") {
    // Get a list of all video segments in the current session
    $segmentFiles = glob($segment_directory . '*.webm');

    // Sort the video segments by filename
    sort($segmentFiles);

    // Generate a filename for the final MP4 file
    make_directory($root_directory . '/videos');
    $outputFilename =  $root_directory . '/videos/' . $video_title;
    $outputFileVideoName = $outputFilename . '.mp4';
    $outputFileMPDName = $outputFilename . '/output.mpd';

    // Delete the previous video with the same filename
    if(file_exists($outputFileVideoName)) {
      unlink($outputFileVideoName);
    }

    // Concatenate all video segments into a single MP4 file using ffmpeg
    $cmd = 'ffmpeg -i "concat:' . implode('|', $segmentFiles) . '" -c copy ' . $outputFileVideoName;
    exec($cmd);
    $del_cmd = 'rm -r ' . $outputFilename;
    exec($del_cmd);
    make_directory($outputFilename);
    /*
    $cmd = 'ffmpeg -i ' . $outputFileVideoName . 
    ' -c:a copy -c:v libx264 -b:v:0 500k -b:v:1 1000k -b:v:2 2000k -profile:v baseline -level 3.0 -s:v:0 640x360 -s:v:1 1280x720 -f dash -init_seg_name \'init-$RepresentationID$.mp4\' -media_seg_name \'chunk-$RepresentationID$-$Number%05d$.m4s\' -segment_time 3 -use_template 0 '
    . $outputFileMPDName;
    */
    $cmd = 'ffmpeg -i ' . $outputFileVideoName .
    ' -an ' .
    '-map 0:v:0 -c:v:0 libx264 -b:v:0 4000k -s:v:0 1280x720 ' .
    '-map 0:v:0 -c:v:1 libx264 -b:v:1 2000k -s:v:1 854x480 ' .
    '-map 0:v:0 -c:v:2 libx264 -b:v:2 1000k -s:v:2 640x360 ' .
    '-map 0:v:0 -c:v:3 libx264 -b:v:3 700k -s:v:3 426x240 ' .
    '-profile:v baseline -level 3.0 -f dash -init_seg_name \'init-$RepresentationID$.mp4\' ' .
    '-media_seg_name \'chunk-$RepresentationID$-$Number%05d$.m4s\' -segment_time 3 -use_template 0 ' .
    $outputFileMPDName;

    exec($cmd);

    // Delete the temporary video segments
    foreach ($segmentFiles as $segmentFile) {
      unlink($segmentFile);
    }
    // Delete the temporary folder
    rmdir($segment_directory);
    
    // Send the filename of the final MP4 file back to the client
    echo $outputFilename;
  }
} else {
  echo "Invalid request method.";
}
?>
