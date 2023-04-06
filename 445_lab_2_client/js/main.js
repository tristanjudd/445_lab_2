import captureSetup from './captureSetup.js';
import handler from './handler.js';

const serverURL = 'http://127.0.0.1:8000/';

// Init encoder so we can call methods on it from global scope
let encoder = null;

// RECORD BUTTON LOGIC
const recordButton = document.querySelector('#record');
recordButton.addEventListener('click', record);
// END RECORD BUTTON

// STOP BUTTON LOGIC
const stopButton = document.querySelector('#stop');
stopButton.addEventListener('click', stop);

// This behaviour is only a placeholder, it works but is not ideal
async function stop() {
  console.log('stop');
  encoder.close();
}
// END STOP BUTTON

// INIT VIDEO STREAM FOR CAPTURE
let stream = await captureSetup();
// END INIT VIDEO
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
// RECORD function
async function record() {
  const options = {
    //videoBitsPerSecond: 2500000,
    mimeType: 'video/webm;codecs=h264',
  };
  const mediaRecorder = new MediaRecorder(stream, options);
  let recordedChunks = [];

  mediaRecorder.addEventListener('dataavailable', (event) => {
    if (event.data.size > 0) {
      recordedChunks.push(event.data);
    }
  });

  setInterval(() => {
    const blob = new Blob(recordedChunks, { type: 'video/webm;codecs=h264' });
    fetch(serverURL + 'upload', {
      method: 'POST',
      body: blob,
    }).then((response) => {
      console.log(response);
      console.log('h2');
    });
    recordedChunks = [];
  }, 3000);

  console.log('h1');

  //mediaRecorder.addEventListener('stop',

  mediaRecorder.start();
} // END Record
