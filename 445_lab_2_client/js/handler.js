function handler(chunk) {
    // chunk is an EncodedVideoChunk object
    // docs: https://developer.mozilla.org/en-US/docs/Web/API/EncodedVideoChunk

    // This is where in theory we have chunks of encoded data and need to 
    // turn them into segments
    
    console.log(chunk);
}

export default handler;