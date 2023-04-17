<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>445 Lab 2 Client</title>
    <link rel="stylesheet" href="./css/styles.css" />
  </head>
  <body>
    <div id="container">
      <h2>UPLOADED VIDEOS</h2>
      <ul>
        <?php
          $video_directory = dirname(__FILE__) . '/api/videos/';
          $videos = glob($video_directory . '*.mp4');
          foreach($videos as $video) {
            echo '<li>' . basename($video, '.mp4') . '</li>';
          }
        ?>
      </ul>
    </div>
    <script type="module" src="./js/main.js" defer></script>
  </body>
</html>
