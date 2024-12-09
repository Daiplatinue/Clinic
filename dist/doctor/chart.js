const checkupCtx = document.getElementById('checkupStats').getContext('2d');
new Chart(checkupCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Checkups',
            data: [65, 59, 80, 81, 56, 55, 40, 45, 60, 75, 85, 90],
            fill: true,
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            tension: 0.4,
            borderWidth: 2,
            pointBackgroundColor: '#0ea5e9',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#0ea5e9'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(156, 163, 175, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    color: 'rgba(156, 163, 175, 0.9)',
                    padding: 10
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: 'rgba(156, 163, 175, 0.9)',
                    padding: 10
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

const healthCtx = document.getElementById('healthIssues').getContext('2d');
new Chart(healthCtx, {
    type: 'doughnut',
    data: {
        labels: ['Fever', 'Headache', 'Allergies', 'Stomach Pain', 'Others'],
        datasets: [{
            data: [30, 25, 20, 15, 10],
            backgroundColor: [
                '#0ea5e9',
                '#22c55e',
                '#eab308',
                '#ef4444',
                '#8b5cf6'
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    color: 'rgba(156, 163, 175, 0.9)',
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            }
        },
        cutout: '75%'
    }
});