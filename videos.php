<?php
  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');

  $video_directory = dirname(__FILE__) . '/api/videos/';
  $videos = glob($video_directory . '*.mp4');

  $file_names = array();

  foreach($videos as $key => $video) {
    $file_names[$key] = $video;
  }

  echo json_encode($file_names);
?>