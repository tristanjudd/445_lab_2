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
      <h2>VIDEO CAPTURE INTERFACE</h2>
      <video id="videoCapture" autoplay="" controls>
        Your browser does not support video.
      </video>
      <div id="buttons">
        <label for="video_title">Video Title:</label>
        <input type="text" id = "video_title">
        <button id="record">
          Record
          <div id="circle"></div>
        </button>
        <button id="stop">
          Stop
          <div id="square"></div>
        </button>
      </div>
    </div>
    <script type="module" src="./js/main.js" defer></script>
  </body>
</html>
