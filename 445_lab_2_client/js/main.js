import captureSetup from './captureSetup.js';
import handler from './handler.js';

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

// RECORD function
async function record() {
// INIT VideoEncoder
const init = {
    output: handler,
    error: (e) => {
      console.log(
        "There was an error! Message follows: \n" + 
        e.message);
    },
  };
  
  const config = {
    codec: "avc1.42002A", // h.264 codec code
    width: 1280,
    height: 720,
    bitrate: 5000000, // 5Mbps
    framerate: 30
  };


const { supported } = await VideoEncoder.isConfigSupported(config);
if (supported) {
    // VideoEncoder object docs: https://developer.mozilla.org/en-US/docs/Web/API/VideoEncoder
    encoder = new VideoEncoder(init);
    encoder.configure(config);

    // GET VideoFrame OBJECTS
    const track = stream.getTracks()[0];
    // Create MediaStramTrackProcessor object
    // docs: https://developer.mozilla.org/en-US/docs/Web/API/MediaStreamTrackProcessor
    const trackProcessor = new MediaStreamTrackProcessor(track); 
    
    // MediaStreamTrackProcessor.readable is a ReadableStream object
    // docs: https://developer.mozilla.org/en-US/docs/Web/API/ReadableStream
    // .getReader() creates a reader and locks stream to it
    const reader = trackProcessor.readable.getReader(); 
    let frameCount = 0;

    // read from reader
    while(true) {
        const input = await reader.read();
        if (input.done) break;

        const frame = input.value;
        if (encoder.encodeQueueSize > 30) { // drop frame if queue over 30
            frame.close();
        } else {
            frameCount++;
            // keyframes are frames that cannot be compressed
            // they are useful for video editing but probably not something we need to care about
            // I set it for every 30 frames, I don't think it matters
            const keyFrame = frameCount % 30 == 0;
            encoder.encode(frame, { keyFrame });
            frame.close();
        }
    }
    // END GET VideoFrame

} else {
    console.log('Video configurations not supported');
}
// END VideoEncoder


} // END Record