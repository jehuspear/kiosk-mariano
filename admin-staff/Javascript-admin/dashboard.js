// Chart.js configuration and initialization
let customerChart = null;
let productChart = null;
let revenueChart = null;
let topProductsChart = null;

// Colors for charts
const colors = {
    primary: '#B9F2FF',
    secondary: '#1976d2',
    tertiary: '#2196f3',
    success: '#4caf50',  // Green for Cash Sales
    info: '#2196f3',     // Blue for GCash Sales
    warning: '#ffd700',  // Gold for Total Sales
    light: '#B9F2FF20',  // Light version of primary color with opacity
    dark: '#0d47a1',
    text: '#FFFFFF'      // White text color
};

// Common chart options for consistent styling
const commonChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: {
            color: colors.text,
            font: {
                size: 14,
                weight: 'bold'
            },
            padding: {
                top: 10,
                bottom: 10
            }
        },
        legend: {
            labels: {
                color: colors.text,
                font: {
                    size: 12
                },
                boxWidth: 15,
                padding: 10
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: colors.text,
                maxRotation: 45,
                minRotation: 45
            },
            grid: {
                color: 'rgba(255, 255, 255, 0.1)'
            }
        },
        y: {
            ticks: {
                color: colors.text
            },
            grid: {
                color: 'rgba(255, 255, 255, 0.1)'
            }
        }
    }
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
                    ...commonChartOptions,
                    plugins: {
                        ...commonChartOptions.plugins,
                        title: {
                            display: true,
                            text: [
                                'Customer Count',
                                formatDateRangeTitle(period, date)
                            ],
                            font: {
                                size: 20,
                                weight: 'bold'
                            },
                            padding: {
                                top: 20,
                                bottom: 10
                            },
                            color: colors.text
                        },
                        subtitle: {
                            display: true,
                            text: `${result.total || 0}`,
                            color: '#B9F2FF',
                            font: {
                                size: 32,
                                weight: 'bold',
                                family: "'Arial Black', 'Arial Bold', Gadget, sans-serif"
                            },
                            padding: {
                                top: 5,
                                bottom: 15
                            }
                        },
                        afterSubtitle: {
                            id: 'afterSubtitle',
                            beforeDraw(chart, args, options) {
                                const {ctx, chartArea: {top, bottom, left, right, width, height}} = chart;
                                ctx.save();
                                ctx.fillStyle = 'rgba(185, 242, 255, 0.1)';
                                ctx.fillRect(left, top + 80, width, 60);
                                ctx.restore();
                            }
                        }
                    },
                    scales: {
                        ...commonChartOptions.scales,
                        y: {
                            ...commonChartOptions.scales.y,
                            beginAtZero: true,
                            ticks: {
                                ...commonChartOptions.scales.y.ticks,
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
        
        // Update the Products Sold value in the side panel
        document.getElementById('productsSold').textContent = result.total || 0;

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
                    ...commonChartOptions,
                    plugins: {
                        ...commonChartOptions.plugins,
                        title: {
                            display: true,
                            text: [
                                'Products Sold',
                                formatDateRangeTitle(period, date),
                                `Total Products Sold: ${result.total || 0}`
                            ],
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: {
                                bottom: 5
                            },
                            color: colors.text
                        }
                    },
                    scales: {
                        ...commonChartOptions.scales,
                        y: {
                            ...commonChartOptions.scales.y,
                            beginAtZero: true,
                            ticks: {
                                ...commonChartOptions.scales.y.ticks,
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
        
        // Update the Revenue panel values first
        if (result.totals) {
            document.getElementById('totalRevenue').textContent = formatCurrency(result.totals.total);
            document.getElementById('cashTotal').textContent = formatCurrency(result.totals.cash_total);
            document.getElementById('gcashTotal').textContent = formatCurrency(result.totals.gcash_total);
            document.getElementById('grandTotal').textContent = formatCurrency(result.totals.total);
        }

        // Prepare chart data
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
            cashData.push(parseFloat(item.cash_total) || 0);
            gcashData.push(parseFloat(item.gcash_total) || 0);
            totalData.push(parseFloat(item.total) || 0);
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
                    ...commonChartOptions,
                    plugins: {
                        ...commonChartOptions.plugins,
                        title: {
                            display: true,
                            text: [
                                'Revenue',
                                formatDateRangeTitle(period, date),
                                `Total Revenue: ${formatCurrency(result.totals.total)}`,
                                `Cash Sales: ${formatCurrency(result.totals.cash_total)}`,
                                `GCash Sales: ${formatCurrency(result.totals.gcash_total)}`
                            ],
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: {
                                bottom: 5
                            },
                            color: colors.text
                        }
                    },
                    scales: {
                        ...commonChartOptions.scales,
                        y: {
                            ...commonChartOptions.scales.y,
                            beginAtZero: true,
                            ticks: {
                                ...commonChartOptions.scales.y.ticks,
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
        });
        
        
    } catch (error) {
        console.error('Error updating revenue chart:', error);
    }
}

// Function to create product images at bar tips
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
            const yValue = item.total_sold;
            const yPos = yAxis.getPixelForValue(yValue); // Position at the tip of the bar
            
            // Draw circular background with shadow
            ctx.save();
            ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
            ctx.shadowBlur = 5;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
            ctx.beginPath();
            ctx.arc(xPos, yPos - 25, 20, 0, Math.PI * 2);
            ctx.fillStyle = 'white';
            ctx.fill();
            ctx.restore();
            
            // Draw image in circle
            ctx.save();
            ctx.beginPath();
            ctx.arc(xPos, yPos - 25, 20, 0, Math.PI * 2);
            ctx.clip();
            ctx.drawImage(img, xPos - 20, yPos - 45, 40, 40);
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
                    ...commonChartOptions,
                    plugins: {
                        ...commonChartOptions.plugins,
                        title: {
                            display: true,
                            text: ['✨ Top 5 Best-Selling Products ✨', 'Most Popular Items'],
                            font: {
                                size: 18,
                                weight: 'bold',
                                family: "'Arial Black', 'Arial Bold', Gadget, sans-serif"
                            },
                            padding: {
                                top: 20,
                                bottom: 15
                            },
                            color: '#B9F2FF'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        ...commonChartOptions.scales,
                        y: {
                            ...commonChartOptions.scales.y,
                            beginAtZero: true,
                            ticks: {
                                ...commonChartOptions.scales.y.ticks,
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

// Function to update all data based on customer section controls
function updateAllData(period, date) {
    updateCustomerChart(period, date);
    updateProductChart(period, date);
    updateRevenueChart(period, date);
    updateTopProductsChart();
}

// Function to handle period changes
function handlePeriodChange(selectElement) {
    const period = selectElement.value;
    const dateInput = document.querySelector('.date-input[data-chart-type="customer"]');
    const date = dateInput.value || new Date().toISOString().split('T')[0];
    updateAllData(period, date);
}

// Function to handle date changes
function handleDateChange(dateInput) {
    const period = document.querySelector('.period-select[data-chart-type="customer"]').value;
    const date = dateInput.value;
    updateAllData(period, date);
}

// Initialize all charts and totals
document.addEventListener('DOMContentLoaded', () => {
    // Set default date to Manila time
    const dateInput = document.querySelector('.date-input[data-chart-type="customer"]');
    dateInput.value = window.manilaTime;
    
    // Initial update of all data
    updateAllData('daily', window.manilaTime);
    
    // Set up period change listener for customer select
    const periodSelect = document.querySelector('.period-select[data-chart-type="customer"]');
    periodSelect.addEventListener('change', (e) => handlePeriodChange(e.target));
    
    // Set up date change listener for customer date input
    dateInput.addEventListener('change', (e) => handleDateChange(e.target));
});
