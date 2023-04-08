<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $root_directory = dirname(__FILE__);

  $video_title =  $_POST['title'];
  $segment_directory = $root_directory . '/segments/' . $video_title . '/';

  if (!file_exists($segment_directory)) {
    mkdir($segment_directory, 0777, true);
  }
  echo $segment_directory;
  if (file_exists($segment_directory)) echo "1dir";
  else echo "0dir";
  
  $filename = $segment_directory . $_FILES['segment']['name'] . '.webm';

  // Write the video segment data to disk
  $handle = fopen($filename, 'w');
  if (file_exists($filename)) echo "1file";
  else echo "0file";
  $contents = file_get_contents($_FILES['segment']['tmp_name']);
  echo 3;
  fwrite($handle, file_get_contents($_FILES['segment']['tmp_name']));
  echo 4;
  fclose($handle);
  echo 5;

  // If this is the last segment, merge all segments into an MP4 file
  if ($_POST['last'] === "true") {
    // Get a list of all video segments in the current session
    $segmentFiles = glob($segment_directory . '*.webm');

    // Sort the video segments by filename
    sort($segmentFiles);

    // Generate a filename for the final MP4 file
    $outputFilename =  $root_directory . '/videos/' . $video_title . '.mp4';

    // Delete the previous video with the same filename
    if(file_exists($outputFilename)) {
      unlink($outputFilename);
    }

    // Concatenate all video segments into a single MP4 file using ffmpeg
    $cmd = 'ffmpeg -i "concat:' . implode('|', $segmentFiles) . '" -c copy ' . $outputFilename;
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
