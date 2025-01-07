class OrderSearch {
    constructor() {
        this.searchInput = document.getElementById('searchTicket');
        this.orderContainer = document.querySelector('.order-details-container');
        this.init();
    }

    init() {
        // Add clear button to search input
        this.addClearButton();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Store initial orders
        this.storeInitialOrders();
    }

    addClearButton() {
        // Remove existing clear button if any
        const existingClear = this.searchInput.parentNode.querySelector('.search-clear');
        if (existingClear) {
            existingClear.remove();
        }

        const clearButton = document.createElement('button');
        clearButton.className = 'search-clear';
        clearButton.innerHTML = '<i class="fas fa-times"></i>';
        clearButton.type = 'button';
        this.searchInput.parentNode.appendChild(clearButton);

        clearButton.addEventListener('click', () => {
            this.searchInput.value = '';
            this.handleSearch();
            clearButton.classList.remove('visible');
            this.searchInput.focus();
        });
    }

    setupEventListeners() {
        // Debounced search handler
        let searchTimeout;
        this.searchInput.addEventListener('input', (e) => {
            const clearButton = document.querySelector('.search-clear');
            if (e.target.value) {
                clearButton.classList.add('visible');
            } else {
                clearButton.classList.remove('visible');
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => this.handleSearch(), 300);
        });

        // Prevent form submission
        const form = this.searchInput.closest('form');
        if (form) {
            form.addEventListener('submit', (e) => e.preventDefault());
        }

        // Handle escape key
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.searchInput.value = '';
                this.handleSearch();
                document.querySelector('.search-clear').classList.remove('visible');
            }
        });
    }

    storeInitialOrders() {
        const orders = this.orderContainer.querySelectorAll('.order');
        this.initialOrders = Array.from(orders).map(order => ({
            element: order.cloneNode(true),
            ticketNumber: order.querySelector('.order-item:first-child').textContent.trim()
        }));
    }

    handleSearch() {
        const searchTerm = this.searchInput.value.trim().toLowerCase();
        const orders = this.initialOrders || Array.from(this.orderContainer.querySelectorAll('.order'));

        // Clear current orders
        this.orderContainer.innerHTML = '';

        if (!searchTerm) {
            // Show all orders if no search term
            orders.forEach(order => {
                const clone = order.element.cloneNode(true);
                this.orderContainer.appendChild(clone);
            });
        } else {
            // Filter and highlight matching orders
            const matchingOrders = orders.filter(order => 
                order.ticketNumber.toLowerCase().includes(searchTerm)
            );

            if (matchingOrders.length > 0) {
                matchingOrders.forEach(order => {
                    const clone = order.element.cloneNode(true);
                    const ticketCell = clone.querySelector('.order-item:first-child');
                    ticketCell.innerHTML = this.highlightMatch(ticketCell.textContent, searchTerm);
                    this.orderContainer.appendChild(clone);
                });
            } else {
                this.orderContainer.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-search" style="margin-right: 10px;"></i>
                        No orders found matching "${searchTerm}"
                    </div>`;
            }
        }

        // Reinitialize event listeners for the new content
        if (window.autoRefresh) {
            window.autoRefresh.setupEventListeners();
        }
    }

    highlightMatch(text, searchTerm) {
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    // Method to update initial orders after refresh
    updateInitialOrders() {
        this.storeInitialOrders();
        if (this.searchInput.value.trim()) {
            this.handleSearch(); // Re-apply current search
        }
    }
}

// Initialize search functionality
document.addEventListener('DOMContentLoaded', () => {
    window.orderSearch = new OrderSearch();
});
