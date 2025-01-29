// Chart.js configuration and initialization
let customerChart = null;
let productChart = null;
let topProductsChart = null;

// Colors for charts
const colors = {
    primary: '#1565c0',
    secondary: '#1976d2',
    tertiary: '#2196f3',
    success: '#4caf50',
    warning: '#ff9800',
    danger: '#f44336',
    light: '#e3f2fd',
    dark: '#0d47a1'
};

// Utility function to format dates
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

// Function to update customer chart
async function updateCustomerChart(period = 'daily', date = new Date().toISOString().split('T')[0]) {
    try {
        const response = await fetch(`api/get_dashboard_data.php?action=customers&period=${period}&date=${date}`);
        const data = await response.json();
        
        let labels, values;
        
        if (period === 'daily') {
            labels = ['Today'];
            values = [data.count];
        } else {
            labels = data.map(item => formatDate(item.date));
            values = data.map(item => item.count);
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
                        text: 'Customer Count Over Time'
                    }
                },
                scales: {
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
        const data = await response.json();
        
        let labels, values;
        
        if (period === 'daily') {
            labels = ['Today'];
            values = [data[0]?.count || 0];
        } else {
            labels = data.map(item => formatDate(item.date));
            values = data.map(item => item.count);
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
                        text: 'Products Sold Over Time'
                    }
                },
                scales: {
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

// Function to update revenue totals
async function updateRevenueTotals(period = 'daily', date = new Date().toISOString().split('T')[0]) {
    try {
        const response = await fetch(`api/get_dashboard_data.php?action=revenue&period=${period}&date=${date}`);
        const data = await response.json();
        
        let cashTotal = 0;
        let gcashTotal = 0;
        let grandTotal = 0;
        
        if (Array.isArray(data)) {
            data.forEach(item => {
                cashTotal += parseFloat(item.cash_total) || 0;
                gcashTotal += parseFloat(item.gcash_total) || 0;
                grandTotal += parseFloat(item.total) || 0;
            });
        } else if (data && typeof data === 'object') {
            cashTotal = parseFloat(data.cash_total) || 0;
            gcashTotal = parseFloat(data.gcash_total) || 0;
            grandTotal = parseFloat(data.total) || 0;
        }
        
        // Update the DOM elements
        document.getElementById('cashTotal').textContent = formatCurrency(cashTotal);
        document.getElementById('gcashTotal').textContent = formatCurrency(gcashTotal);
        document.getElementById('grandTotal').textContent = formatCurrency(grandTotal);
        
    } catch (error) {
        console.error('Error updating revenue totals:', error);
    }
}

// Function to update top products chart
async function updateTopProductsChart() {
    try {
        const response = await fetch('api/get_dashboard_data.php?action=top_products');
        const data = await response.json();
        
        const labels = data.map(item => item.MenuItem_Name);
        const values = data.map(item => item.total_sold);
        
        if (topProductsChart) {
            topProductsChart.destroy();
        }
        
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        topProductsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Units Sold',
                    data: values,
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.warning,
                        colors.danger,
                        colors.tertiary
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top 5 Best-Selling Products'
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
    const date = new Date().toISOString().split('T')[0];
    
    switch(chartType) {
        case 'customer':
            updateCustomerChart(period, date);
            break;
        case 'product':
            updateProductChart(period, date);
            break;
        case 'revenue':
            updateRevenueTotals(period, date);
            break;
    }
}

// Initialize all charts and totals
document.addEventListener('DOMContentLoaded', () => {
    updateCustomerChart();
    updateProductChart();
    updateRevenueTotals();
    updateTopProductsChart();
    
    // Set up period change listeners
    document.querySelectorAll('.period-select').forEach(select => {
        select.addEventListener('change', (e) => {
            handlePeriodChange(e.target.dataset.chartType, e.target);
        });
    });
});
