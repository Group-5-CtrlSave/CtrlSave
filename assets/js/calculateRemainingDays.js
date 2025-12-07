function calculateRemainingDays(dueDate) {
    const today = new Date();
    const target = new Date(dueDate);

    if (isNaN(target)) return 'Invalid date';

    // Create dates at midnight without mutating originals
    const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const targetMidnight = new Date(target.getFullYear(), target.getMonth(), target.getDate());

    const diffTime = targetMidnight - todayMidnight; // difference in ms
    const days = diffTime / (1000 * 60 * 60 * 24);   // convert to days

    if (days < 0) return 'Expired';
    if (days === 0) return 'Today';
    if (days === 1) return '1 day left';
    return `${days} days left`;
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
