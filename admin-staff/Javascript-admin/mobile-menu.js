document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle functionality
    const toggleButtons = document.querySelectorAll('#sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    toggleButtons.forEach(function(toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle sidebar visibility
            sidebar.classList.toggle('show');
            
            // Toggle body class for overlay
            document.body.classList.toggle('sidebar-open', sidebar.classList.contains('show'));
            
            // Change icon based on sidebar state
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 991 && sidebar) {
            let clickedOnToggle = false;
            
            // Check if click was on any toggle button
            toggleButtons.forEach(function(btn) {
                if (btn.contains(event.target)) {
                    clickedOnToggle = true;
                }
            });
            
            if (!sidebar.contains(event.target) && !clickedOnToggle && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
                
                // Reset all toggle button icons
                toggleButtons.forEach(function(btn) {
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991 && sidebar) {
            sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');
            
            // Reset all toggle button icons
            toggleButtons.forEach(function(btn) {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        }
    });
});
