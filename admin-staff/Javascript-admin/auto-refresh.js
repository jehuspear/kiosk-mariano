class AutoRefresh {
    constructor(interval = 5000) {
        this.interval = interval;
        this.searchInput = document.getElementById('searchTicket');
        this.lastSearchValue = '';
        this.init();
    }

    init() {
        // Store initial search value if search exists
        if (this.searchInput) {
            this.lastSearchValue = this.searchInput.value;
        }

        // Initial event listener setup
        this.setupEventListeners();

        // Start refresh cycle
        this.startRefresh();
    }

    async startRefresh() {
        setInterval(() => {
            // Store current search value
            if (this.searchInput) {
                this.lastSearchValue = this.searchInput.value;
            }
            this.refreshPage();
        }, this.interval);
    }

    setupEventListeners() {
        // Setup for pending orders page
        document.querySelectorAll('.done-button').forEach(button => {
            const orderId = button.getAttribute('data-order-id');
            button.onclick = (e) => {
                if (window.confirmOrderHandler) {
                    window.confirmOrderHandler.call(button, e);
                }
            };
        });

        document.querySelectorAll('.button-cancel').forEach(button => {
            const orderId = button.getAttribute('data-order-id');
            button.onclick = (e) => {
                if (window.cancelOrderHandler) {
                    window.cancelOrderHandler.call(button, e);
                }
            };
        });

        // Setup for preparing orders page
        document.querySelectorAll('.action-btn.approve').forEach(button => {
            const orderId = button.getAttribute('data-order-id');
            button.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.handleOrderAction) {
                    window.handleOrderAction(orderId, 'ready');
                }
            };
        });

        document.querySelectorAll('.action-btn.decline').forEach(button => {
            const orderId = button.getAttribute('data-order-id');
            button.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.handleOrderAction) {
                    window.handleOrderAction(orderId, 'cancel');
                }
            };
        });
    }

    refreshPage() {
        // Get current scroll position
        const scrollPos = window.scrollY;
        
        // Show refresh indicator
        const indicator = document.querySelector('.refresh-indicator');
        if (indicator) {
            indicator.style.display = 'block';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 1000);
        }

        // Add refresh animation to container
        const container = document.querySelector('.order-details-container, .order-list');
        if (container) {
            container.style.opacity = '0.6';
            container.style.transition = 'opacity 0.3s ease';
        }
        
        // Fetch current page content
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                // Create a temporary container
                const temp = document.createElement('div');
                temp.innerHTML = html;

                // Get the new order container
                const newOrderContainer = temp.querySelector('.order-details-container, .order-list');
                const currentOrderContainer = document.querySelector('.order-details-container, .order-list');

                if (newOrderContainer && currentOrderContainer) {
                    // If there's an active search, don't update the container directly
                    if (this.searchInput && this.searchInput.value.trim()) {
                        // Update the search class's initial orders instead
                        if (window.orderSearch) {
                            const tempSearch = new OrderSearch();
                            tempSearch.orderContainer = newOrderContainer;
                            tempSearch.storeInitialOrders();
                            window.orderSearch.initialOrders = tempSearch.initialOrders;
                            window.orderSearch.handleSearch();
                        }
                    } else {
                        // No search active, update container directly
                        currentOrderContainer.innerHTML = newOrderContainer.innerHTML;
                    }

                    // Restore scroll position and opacity
                    window.scrollTo(0, scrollPos);
                    if (container) {
                        container.style.opacity = '1';
                    }

                    // Setup event listeners for the new content
                    this.setupEventListeners();

                    // Initialize order handlers if available
                    if (window.initializeOrderHandlers) {
                        window.initializeOrderHandlers();
                    }

                    // Update search functionality if it exists
                    if (window.orderSearch && !this.searchInput?.value.trim()) {
                        window.orderSearch.updateInitialOrders();
                    }

                    // Reinitialize Bootstrap components
                    if (typeof bootstrap !== 'undefined') {
                        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(element => {
                            new bootstrap.Modal(element);
                        });
                    }
                }
            })
            .catch(error => console.error('Error refreshing page:', error));
    }
}

// Initialize auto-refresh
document.addEventListener('DOMContentLoaded', () => {
    window.autoRefresh = new AutoRefresh(5000); // 5 seconds
});
