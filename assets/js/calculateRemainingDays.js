function calculateRemainingDays(dueDate) {
    const now = new Date();
    const date = new Date(dueDate);

    if (isNaN(date)) {
        return 'Invalid date';
    }

    const diff = date - now;

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (diff < 0) {
        return 'Expired';
    } else if (days === 0) {
        return 'Today';
    } else if (days === 1) {
        return '1 day left';
    } else {
        return `${days} days left`;
    }
}

function calculateDays() {
    document.querySelectorAll('.time').forEach(element => {
        const dueDate = element.dataset.duedate;
        if (dueDate) {
            element.textContent = calculateRemainingDays(dueDate);
        }
    });
}

calculateDays();
setInterval(calculateDays, 60 * 60 * 1000); 
