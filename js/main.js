import captureSetup from './captureSetup.js';
import handler from './handler.js';

const serverURL = 'http://127.0.0.1/';

// Init encoder so we can call methods on it from global scope
let encoder = null;

// RECORD BUTTON LOGIC
const recordButton = document.querySelector('#record');
recordButton.addEventListener('click', record);
// END RECORD BUTTON

let stream = await captureSetup();

let recording = false;

// RECORD function
async function record() {
  if(recording) return;
  let title_entered = document.getElementById("video_title").value;
  let video_title = title_entered.length > 0 ? title_entered : 'untitled';

  recording = true;

  const options = {
    videoBitsPerSecond: 5000000,
    mimeType: 'video/webm;codecs=h264',
  };
  const mediaRecorder = new MediaRecorder(stream, options);

  let segment_number = 1;
  let last = false;

  mediaRecorder.start(3000);

  mediaRecorder.ondataavailable = (event) => {
    if (event.data.size > 0) {
      const segment_blob = new Blob([event.data], { type: 'video/webm;codecs=h264' });
      let formData = new FormData();
      formData.append('segment', segment_blob, 'segment' + segment_number++);
      formData.append('last', last);
      formData.append('title', video_title);

      fetch(serverURL + 'upload.php', {
        method: 'POST',
        body: formData,
      }).then((response) => {
        console.log(response);
      });
    }
  };

  // STOP BUTTON LOGIC
  const stopButton = document.querySelector('#stop');
  stopButton.addEventListener('click', stop);

  async function stop() {
    last = true;
    mediaRecorder.stop();
    recording = false;
  }// END STOP BUTTON
} // END Record


// LOGIC TO CHECK FILE TYPE BROWSER SUPPORT
/*
const types = [
  'video/webm',
  'audio/webm',
  'video/webm;codecs=vp8',
  'video/webm;codecs=daala',
  'video/webm;codecs=h264',
  'audio/webm;codecs=opus',
  'video/mpeg',
  'video/mp4',
];

for (const type of types) {
  console.log(
    `Is ${type} supported? ${
      MediaRecorder.isTypeSupported(type) ? 'Maybe!' : 'Nope :('
    }`
  );
}
*/