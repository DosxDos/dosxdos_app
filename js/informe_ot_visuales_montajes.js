/* LOADER */
function loaderOn() {
    scrollToTop();
    document.getElementById('loader-text').textContent = '';
    document.getElementById('loader-dots').innerHTML = '<span>.</span><span>.</span><span>.</span>';
    document.getElementById('loader').classList.remove("displayOff");
    document.getElementById('loader').classList.add("displayOn");
}

function loaderOff() {
    setTimeout(() => {
        document.getElementById('loader').classList.remove("displayOn");
        document.getElementById('loader').classList.add("displayOff");
    }, 3000);
}

function scrollToTop() {
    document.getElementsByTagName('body').scrollTop = 0;
}

errorApp = false;
