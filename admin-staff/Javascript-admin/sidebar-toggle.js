document.addEventListener('DOMContentLoaded', function() {
    // Get all wrapper elements on the page (there might be multiple if sidebar is included in multiple pages)
    const wrappers = document.querySelectorAll('.wrapper');
    
    wrappers.forEach(function(wrapper) {
        const sidebar = wrapper.querySelector('.sidebar');
        const mainContent = wrapper.querySelector('.main-content');
        const toggleBtn = wrapper.querySelector('#sidebarToggle');
        
        if (!sidebar || !toggleBtn) return; // Skip if elements not found
        
        // Function to toggle sidebar
        function toggleSidebar() {
            wrapper.classList.toggle('sidebar-collapsed');
            
            // Change button icon based on sidebar state
            const icon = toggleBtn.querySelector('i');
            if (wrapper.classList.contains('sidebar-collapsed')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
            
            // Store state in localStorage
            localStorage.setItem('sidebarState', wrapper.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
        }
        
        // Add click event to toggle button
        toggleBtn.addEventListener('click', toggleSidebar);
        
        // Check if we should initialize based on screen size
        const isMobile = window.innerWidth <= 991;
        
        // Initialize sidebar state
        if (isMobile) {
            // On mobile, start collapsed
            wrapper.classList.add('sidebar-collapsed');
            if (toggleBtn.querySelector('i')) {
                toggleBtn.querySelector('i').classList.remove('fa-times');
                toggleBtn.querySelector('i').classList.add('fa-bars');
            }
        } else {
            // On desktop, check localStorage or default to expanded
            const savedState = localStorage.getItem('sidebarState');
            if (savedState === 'collapsed') {
                wrapper.classList.add('sidebar-collapsed');
                if (toggleBtn.querySelector('i')) {
                    toggleBtn.querySelector('i').classList.remove('fa-times');
                    toggleBtn.querySelector('i').classList.add('fa-bars');
                }
            }
        }
        
        // Handle clicks outside sidebar on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 991) {
                if (sidebar && !sidebar.contains(e.target) && toggleBtn && !toggleBtn.contains(e.target) && !wrapper.classList.contains('sidebar-collapsed')) {
                    toggleSidebar();
                }
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const isMobile = window.innerWidth <= 991;
            if (isMobile && !wrapper.classList.contains('sidebar-collapsed')) {
                // Auto-collapse on mobile
                toggleSidebar();
            }
        });
    });
});
