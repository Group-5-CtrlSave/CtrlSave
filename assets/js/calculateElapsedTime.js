function calculateElapsedTime(dateTime) {

    const now = new Date();

    const date = new Date(dateTime);
    const diff = now - date;


    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (days >= 1) {
        return date.toLocaleDateString();
    } else if (hours >= 1) {
        return hours + `h ago`;
    } else if (minutes >= 1) {
        return minutes + `m ago`;
    } else {
        return "Just Now";
    }
}

function calculateTime() {
    document.querySelectorAll('.time').forEach(element => {
        const dateTime = element.dataset.datetime;

        if (dateTime){
            element.textContent = calculateElapsedTime(dateTime);
        }
       
    });

}

calculateTime();

setInterval(calculateTime, 1000);

