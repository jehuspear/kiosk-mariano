class SidebarNotifications {
    constructor() {
        this.pendingLink = document.querySelector('a[href="pending-orders.php"]');
        this.preparingLink = document.querySelector('a[href="preparing-orders.php"]');
        this.checkInterval = 5000; // Check every 5 seconds
        this.currentCounts = {
            pending: 0,
            preparing: 0
        };
        this.init();
    }

    init() {
        if (this.pendingLink) {
            // Create pending badge
            const pendingBadge = document.createElement('span');
            pendingBadge.className = 'notification-badge pending-badge';
            this.pendingLink.appendChild(pendingBadge);
        }

        if (this.preparingLink) {
            // Create preparing badge
            const preparingBadge = document.createElement('span');
            preparingBadge.className = 'notification-badge preparing-badge';
            this.preparingLink.appendChild(preparingBadge);
        }
        
        // Start checking for updates
        this.startChecking();
    }

    async checkCounts() {
        try {
            // Check pending orders
            if (this.pendingLink && !window.location.pathname.includes('pending-orders.php')) {
                const pendingResponse = await fetch('get_pending_count.php');
                const pendingData = await pendingResponse.json();

                if (pendingData.success) {
                    await this.updateBadge('pending', parseInt(pendingData.count));
                }
            }

            // Check preparing orders
            if (this.preparingLink && !window.location.pathname.includes('preparing-orders.php')) {
                const preparingResponse = await fetch('get_preparing_count.php');
                const preparingData = await preparingResponse.json();

                if (preparingData.success) {
                    await this.updateBadge('preparing', parseInt(preparingData.count));
                }
            }
        } catch (error) {
            console.error('Error checking counts:', error);
        }
    }

    async updateBadge(type, newCount) {
        const link = type === 'pending' ? this.pendingLink : this.preparingLink;
        const badge = link.querySelector(`.${type}-badge`);
        
        if (badge) {
            if (newCount > 0) {
                badge.textContent = newCount;
                badge.style.display = 'flex';
                
                // If count increased, animate the badge and play sound
                if (newCount > this.currentCounts[type]) {
                    badge.classList.add('pulse');
                    setTimeout(() => badge.classList.remove('pulse'), 1000);
                    
                    // Play notification sound
                    this.playNotificationSound();
                }
            } else {
                badge.style.display = 'none';
            }
        }
        
        this.currentCounts[type] = newCount;
    }

    playNotificationSound() {
        const audio = document.getElementById('notificationSound');
        if (audio) {
            audio.play().catch(e => console.log('Error playing sound:', e));
        }
    }

    startChecking() {
        // Initial check
        this.checkCounts();
        
        // Start periodic checking
        setInterval(() => this.checkCounts(), this.checkInterval);
    }
}

// Initialize notifications
document.addEventListener('DOMContentLoaded', () => {
    window.sidebarNotifications = new SidebarNotifications();
});
