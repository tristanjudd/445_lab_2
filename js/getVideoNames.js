const videoList = document.querySelector('#videos');

const PATH_LOCATION = 'http://localhost:8000/videos.php';

fetch(PATH_LOCATION)
.then(res => (
    res.json()
))
.then(data => {
    let exp = /[^\/]*\.mp4$/;

    data.forEach(val => {
        let match = exp.exec(val)[0].split('.');

        const li = document.createElement("li");
        
        const link = document.createElement("a");
        link.setAttribute('href', 'http://localhost:8000/index.html');
        link.setAttribute('data-target', match[0]);
        link.innerText = match[0];

        li.appendChild(link);
        videoList.appendChild(li);
        
    });
})
.then(wtv => {
    const links = document.querySelectorAll('a');

    links.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();

            localStorage.setItem('target', link.dataset.target);

            window.location.href = './player.html';
            
        })
    })
})