document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.wrapper');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleBtn = document.getElementById('sidebarToggle');

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

    // Initialize sidebar state - always start collapsed
    wrapper.classList.add('sidebar-collapsed');
    toggleBtn.querySelector('i').classList.remove('fa-times');
    toggleBtn.querySelector('i').classList.add('fa-bars');
    localStorage.setItem('sidebarState', 'collapsed');

    // Handle clicks outside sidebar on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && !wrapper.classList.contains('sidebar-collapsed')) {
                toggleSidebar();
            }
        }
    });
});
