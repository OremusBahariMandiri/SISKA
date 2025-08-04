import './bootstrap';

// This file will be processed by Vite
// We'll ensure jQuery and Bootstrap are properly loaded

// Import jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Import DataTables (if needed)
import 'datatables.net';
import 'datatables.net-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';

// Import the main CSS file
import '../css/app.css';

// Import the sidebar CSS explicitly
import '../css/sidebar.css';

// Flag untuk mencegah inisialisasi ganda
if (window.appInitialized) {
    console.log("App already initialized, skipping");
} else {
    window.appInitialized = true;

    // Initialize components when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Initializing app.js components");

        // Initialize DataTables
        initDataTables();

        // Initialize Bootstrap components
        initBootstrapComponents();

        // Initialize delete confirmation
        initDeleteConfirmation();

        // Initialize sidebar functionality
        initSidebar();

        // Auto-hide alerts after 5 seconds
        initAutoHideAlerts();
    });

    // Function to initialize DataTables
    function initDataTables() {
        console.log("Initializing DataTables");
        if ($.fn.DataTable) {
            $('.data-table').each(function() {
                // Check if table is already initialized
                if (!$.fn.DataTable.isDataTable(this)) {
                    // Initialize DataTable only if not already initialized
                    console.log("Initializing table:", this.id);
                    $(this).DataTable({
                        responsive: true,
                        // The language is now set globally in the layout file
                        columnDefs: [
                            {
                                responsivePriority: 1,
                                targets: [0, 1, -1] // Priority on first, second and last column
                            },
                            {
                                orderable: false,
                                targets: [-1] // Last column (actions) not sortable
                            }
                        ]
                    });
                } else {
                    console.log("Table already initialized:", this.id);
                }
            });
        }
    }

    // Function to initialize Bootstrap components
    function initBootstrapComponents() {
        console.log("Initializing Bootstrap components");

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function(popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Function to initialize delete confirmation
    function initDeleteConfirmation() {
        console.log("Initializing delete confirmation");

        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const id = $(this).data('id');
            const name = $(this).data('name');
            const url = $(this).data('url') || $(this).data('route');

            console.log("Delete confirmation clicked for:", name);

            // Set item name in modal
            $('#itemNameToDelete').text(name);

            // Set form action URL
            $('#deleteForm').attr('action', url);

            // Show modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            deleteModal.show();
        });
    }

    // Function to initialize sidebar functionality
    function initSidebar() {
        console.log("Initializing sidebar");

        // Get DOM elements
        const layoutContainer = document.getElementById('layoutContainer');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');
        const mediaQuery = window.matchMedia('(max-width: 991.98px)');
        const menuItems = document.querySelectorAll('.sidebar-menu-item');

        // Toggle sidebar on hamburger click
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                console.log("Sidebar toggle clicked");
                e.preventDefault();

                if (mediaQuery.matches) {
                    // Mobile view
                    layoutContainer.classList.toggle('sidebar-active');
                } else {
                    // Desktop view
                    layoutContainer.classList.toggle('sidebar-collapsed');

                    // Save sidebar state
                    const isCollapsed = layoutContainer.classList.contains('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                }
            });
        }

        // Close sidebar on mobile
        if (sidebarClose) {
            sidebarClose.addEventListener('click', function(e) {
                console.log("Sidebar close clicked");
                e.preventDefault();
                layoutContainer.classList.remove('sidebar-active');
            });
        }

        // Close sidebar when overlay is clicked
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function(e) {
                console.log("Sidebar overlay clicked");
                e.preventDefault();
                layoutContainer.classList.remove('sidebar-active');
            });
        }

        // Handle submenu toggles - PERBAIKAN UNTUK DROPDOWN
        if (menuItems && menuItems.length > 0) {
            console.log(`Found ${menuItems.length} menu items`);

            menuItems.forEach(function(item) {
                item.addEventListener('click', function(e) {
                    console.log("Menu item clicked:", this);

                    // Periksa apakah yang diklik adalah elemen menu item itu sendiri atau ikon submenu
                    if (e.target === this || e.target.closest('.menu-icon-text') ||
                        e.target.classList.contains('submenu-indicator') ||
                        e.target.closest('.submenu-indicator')) {

                        e.preventDefault();

                        const menuId = this.getAttribute('data-menu');
                        const submenu = document.getElementById(menuId);

                        console.log("Menu ID:", menuId, "Submenu:", submenu);

                        // Jika berada di mobile view, atau desktop view tapi tidak dalam collapsed state
                        const isCollapsedDesktop = !mediaQuery.matches && layoutContainer.classList.contains('sidebar-collapsed');

                        if (!isCollapsedDesktop) {
                            // Toggle open class on menu item
                            this.classList.toggle('open');

                            // Toggle show class on submenu
                            if (submenu) {
                                submenu.classList.toggle('show');
                                console.log("Toggled submenu visibility:", submenu.classList.contains('show'));
                            }
                        }
                    }
                });
            });
        }

        // Check for mobile/desktop view
        function handleResize(e) {
            if (e.matches) {
                // Mobile view
                layoutContainer.classList.remove('sidebar-collapsed');
            } else {
                // Desktop view
                layoutContainer.classList.remove('sidebar-active');

                // Restore sidebar state
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    layoutContainer.classList.add('sidebar-collapsed');
                } else {
                    layoutContainer.classList.remove('sidebar-collapsed');
                }
            }
        }

        // Initial check and add listener
        if (mediaQuery) {
            handleResize(mediaQuery);
            mediaQuery.addEventListener('change', handleResize);
        }

        // Initialize any active submenus (for current page)
        const activeMenuItems = document.querySelectorAll('.sidebar-menu-item.active');
        activeMenuItems.forEach(function(item) {
            console.log("Setting active menu item:", item);
            item.classList.add('open');

            const menuId = item.getAttribute('data-menu');
            const submenu = document.getElementById(menuId);

            if (submenu) {
                submenu.classList.add('show');
            }
        });
    }

    // Function to auto-hide alerts
    function initAutoHideAlerts() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 5000);
    }
}