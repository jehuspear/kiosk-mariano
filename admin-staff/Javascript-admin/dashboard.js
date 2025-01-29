// Chart.js configuration and initialization
let customerChart = null;
let productChart = null;
let revenueChart = null;
let topProductsChart = null;

// Colors for charts
const colors = {
    primary: '#1565c0',
    secondary: '#1976d2',
    tertiary: '#2196f3',
    success: '#4caf50',  // Green for Cash Sales
    info: '#2196f3',     // Blue for GCash Sales
    warning: '#ffd700',  // Gold for Total Sales
    light: '#e3f2fd',
    dark: '#0d47a1'
};

// Bar chart colors
const barColors = [
    '#FF6B6B',  // Coral Red
    '#4ECDC4',  // Turquoise
    '#45B7D1',  // Sky Blue
    '#96CEB4',  // Sage Green
    '#FFEEAD'   // Light Yellow
];

// Utility function to format dates
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
}

// Utility function to format time
function formatTime(datetime) {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

// Function to format date range title
function formatDateRangeTitle(period, date) {
    const d = new Date(date);
    
    switch(period) {
        case 'daily':
            return d.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        case 'weekly':
            const weekEnd = new Date(d);
            weekEnd.setDate(d.getDate() + 6);
            return `Week of ${d.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric'
            })} - ${weekEnd.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            })}`;
        case 'monthly':
            return d.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            });
        default:
            return '';
    }
}

// Function to update customer chart
async function updateCustomerChart(period = 'daily', date = new Date().toISOString().split('T')[0]) {
    try {
        const response = await fetch(`api/get_dashboard_data.php?action=customers&period=${period}&date=${date}`);
        const result = await response.json();
        
        let labels, values;
        
        if (period === 'daily') {
            labels = result.data.map(item => formatTime(item.datetime));
            values = result.data.map(item => item.count);
        } else {
            labels = result.data.map(item => formatDate(item.date));
            values = result.data.map(item => item.count);
        }
        
        if (customerChart) {
            customerChart.destroy();
        }
        
        const ctx = document.getElementById('customerChart').getContext('2d');
        customerChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Customer Count',
                    data: values,
                    borderColor: colors.primary,
                    backgroundColor: colors.light,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: [
                            'Customer Count',
                            formatDateRangeTitle(period, date),
                            `Total Customers: ${result.total || 0}`
                        ],
                        padding: {
                            bottom: 10
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error updating customer chart:', error);
    }
}

// Function to update product sales chart
async function updateProductChart(period = 'daily', date = new Date().toISOString().split('T')[0]) {
    try {
        const response = await fetch(`api/get_dashboard_data.php?action=products&period=${period}&date=${date}`);
        const result = await response.json();
        
        let labels, values;
        
        if (period === 'daily') {
            labels = result.data.map(item => formatTime(item.datetime));
            values = result.data.map(item => item.count);
        } else {
            labels = result.data.map(item => formatDate(item.date));
            values = result.data.map(item => item.count);
        }
        
        if (productChart) {
            productChart.destroy();
        }
        
        const ctx = document.getElementById('productChart').getContext('2d');
        productChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Products Sold',
                    data: values,
                    borderColor: colors.success,
                    backgroundColor: colors.success + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: [
                            'Products Sold',
                            formatDateRangeTitle(period, date),
                            `Total Products Sold: ${result.total || 0}`
                        ],
                        padding: {
                            bottom: 10
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error updating product chart:', error);
    }
}

// Function to update revenue chart
async function updateRevenueChart(period = 'daily', date = new Date().toISOString().split('T')[0]) {
    try {
        const response = await fetch(`api/get_dashboard_data.php?action=revenue&period=${period}&date=${date}`);
        const result = await response.json();
        
        let labels;
        const cashData = [];
        const gcashData = [];
        const totalData = [];
        
        if (period === 'daily') {
            labels = result.data.map(item => formatTime(item.datetime));
        } else {
            labels = result.data.map(item => formatDate(item.date));
        }
        
        result.data.forEach(item => {
            cashData.push(item.cash_total);
            gcashData.push(item.gcash_total);
            totalData.push(item.total);
        });
        
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        const ctx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Cash Sales',
                        data: cashData,
                        backgroundColor: colors.success,
                        borderColor: colors.success,
                        borderWidth: 1
                    },
                    {
                        label: 'GCash Sales',
                        data: gcashData,
                        backgroundColor: colors.info,
                        borderColor: colors.info,
                        borderWidth: 1
                    },
                    {
                        label: 'Total Sales',
                        data: totalData,
                        backgroundColor: colors.warning,
                        borderColor: colors.warning,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: [
                            'Revenue',
                            formatDateRangeTitle(period, date),
                            `Total Revenue: ${formatCurrency(result.totals.total)}`,
                            `Cash Sales: ${formatCurrency(result.totals.cash_total)}`,
                            `GCash Sales: ${formatCurrency(result.totals.gcash_total)}`
                        ],
                        padding: {
                            bottom: 10
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
        
        // Update the DOM elements
        document.getElementById('cashTotal').textContent = formatCurrency(result.totals.cash_total);
        document.getElementById('gcashTotal').textContent = formatCurrency(result.totals.gcash_total);
        document.getElementById('grandTotal').textContent = formatCurrency(result.totals.total);
        
    } catch (error) {
        console.error('Error updating revenue chart:', error);
    }
}

// Function to create product images above bars
function createProductImages(chart, data) {
    const chartArea = chart.chartArea;
    const ctx = chart.ctx;
    const xAxis = chart.scales.x;
    const yAxis = chart.scales.y;
    
    data.forEach((item, index) => {
        const img = new Image();
        img.src = '../admin-staff/' + item.MenuItem_Image;
        
        img.onload = () => {
            const xPos = xAxis.getPixelForValue(index);
            const yPos = chartArea.top - 60; // Position above the bar
            
            // Draw circular background
            ctx.save();
            ctx.beginPath();
            ctx.arc(xPos, yPos + 25, 25, 0, Math.PI * 2);
            ctx.fillStyle = 'white';
            ctx.fill();
            ctx.restore();
            
            // Draw image in circle
            ctx.save();
            ctx.beginPath();
            ctx.arc(xPos, yPos + 25, 25, 0, Math.PI * 2);
            ctx.clip();
            ctx.drawImage(img, xPos - 25, yPos, 50, 50);
            ctx.restore();
        };
    });
}

// Function to update top products chart
async function updateTopProductsChart() {
    try {
        const response = await fetch('api/get_dashboard_data.php?action=top_products');
        const data = await response.json();
        
        const labels = data.map(item => item.MenuItem_Name);
        const totalSold = data.map(item => item.total_sold);
        
        if (topProductsChart) {
            topProductsChart.destroy();
        }
        
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        topProductsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Items Sold',
                    data: totalSold,
                    backgroundColor: barColors,
                    borderColor: barColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top 5 Best-Selling Products',
                        padding: {
                            top: 60  // Make room for images
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    onComplete: function() {
                        createProductImages(this, data);
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error updating top products chart:', error);
    }
}

// Function to handle period changes
function handlePeriodChange(chartType, selectElement) {
    const period = selectElement.value;
    const dateInput = document.querySelector(`.date-input[data-chart-type="${chartType}"]`);
    const date = dateInput.value || new Date().toISOString().split('T')[0];
    
    switch(chartType) {
        case 'customer':
            updateCustomerChart(period, date);
            break;
        case 'product':
            updateProductChart(period, date);
            break;
        case 'revenue':
            updateRevenueChart(period, date);
            break;
    }
}

// Function to handle date changes
function handleDateChange(chartType, dateInput) {
    const period = document.querySelector(`.period-select[data-chart-type="${chartType}"]`).value;
    const date = dateInput.value;
    
    switch(chartType) {
        case 'customer':
            updateCustomerChart(period, date);
            break;
        case 'product':
            updateProductChart(period, date);
            break;
        case 'revenue':
            updateRevenueChart(period, date);
            break;
    }
}

// Initialize all charts and totals
document.addEventListener('DOMContentLoaded', () => {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('.date-input').forEach(input => {
        input.value = today;
    });
    
    updateCustomerChart();
    updateProductChart();
    updateRevenueChart();
    updateTopProductsChart();
    
    // Set up period change listeners
    document.querySelectorAll('.period-select').forEach(select => {
        select.addEventListener('change', (e) => {
            handlePeriodChange(e.target.dataset.chartType, e.target);
        });
    });
    
    // Set up date change listeners
    document.querySelectorAll('.date-input').forEach(input => {
        input.addEventListener('change', (e) => {
            handleDateChange(e.target.dataset.chartType, e.target);
        });
    });
});
