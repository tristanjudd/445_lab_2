async function captureSetup() {
    // specs for input video
    let constraints = {
      video: {width: 1280, height: 720, frameRate: 30}
    };

    // this asks for permission and gets user I/O devices
    // default behaviour seems to grab webcam, didn't need to look further into it
    let stream = await window.navigator.mediaDevices.getUserMedia(constraints);
    
    // get video tag in html file
    let video = document.querySelector('#videoCapture');
    // set video source to the stream
    video.srcObject = stream;
    // return the stream as it is needed for other things
    return stream;
  }

export default captureSetup;