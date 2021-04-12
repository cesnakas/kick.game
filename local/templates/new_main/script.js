const menu = document.querySelector('.header__links');
const toggleMenuButton = document.querySelector('.js-toggle-menu');
const backLink = document.querySelector('.js-back');
const toggleLangButton = document.querySelector('.js-lang-label');
const langSelector = document.querySelector('.js-lang');

const player = document.querySelector('.player');
const video = player.querySelector('.player__video');
const progress = player.querySelector('.player__progress');
const progressBar = player.querySelector('.player__progress-filled')
const playButton = player.querySelector('.js-play');
const fullScreenButton = player.querySelector('.js-full');

function toggleMenu() {
  menu.classList.toggle('header__links--mobile');
}

function togglePlay() {
  if (video.paused) {
    video.play();
  } else {
    video.pause();
  }
};

function updateButton() {
  const icon = this.paused ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 163.861 163.861"><path d="M34.857 3.613C20.084-4.861 8.107 2.081 8.107 19.106v125.637c0 17.042 11.977 23.975 26.75 15.509L144.67 97.275c14.778-8.477 14.778-22.211 0-30.686L34.857 3.613z"/></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320"><path d="M112 0H16C7.168 0 0 7.168 0 16v288c0 8.832 7.168 16 16 16h96c8.832 0 16-7.168 16-16V16c0-8.832-7.168-16-16-16zM304 0h-96c-8.832 0-16 7.168-16 16v288c0 8.832 7.168 16 16 16h96c8.832 0 16-7.168 16-16V16c0-8.832-7.168-16-16-16z"/></svg>';
  playButton.innerHTML = icon;
};

function handleProgress() {
  const percent = (video.currentTime / video.duration) * 100;
  progressBar.style.flexBasis = `${percent}%`
};

function scrub(e) {
  const time = (e.offsetX / progress.offsetWidth) * video.duration;
  video.currentTime = time;
};

function openFullscreen() {
  if (video.requestFullscreen) {
    video.requestFullscreen();
  } else if (video.webkitRequestFullscreen) { /* Safari */
    video.webkitRequestFullscreen();
  } else if (video.msRequestFullscreen) { /* IE11 */
    video.msRequestFullscreen();
  }
}

toggleMenuButton.addEventListener('click', (e) => {
  e.preventDefault();
  toggleMenu();
});

backLink.addEventListener('click', (e) => {
  e.preventDefault();
  toggleMenu();
});

toggleLangButton.addEventListener('click', (e) => {
  langSelector.classList.toggle('lang-selector--opened');
});

video.addEventListener('click', togglePlay);

video.addEventListener('play', updateButton);
video.addEventListener('pause', updateButton);
video.addEventListener('timeupdate', handleProgress);

playButton.addEventListener('click', togglePlay);

progress.addEventListener('click', scrub);

fullScreenButton.addEventListener('click', openFullscreen);