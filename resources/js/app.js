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

        // Handle submenu toggles - PERBAIKAN DROPDOWN
        if (menuItems && menuItems.length > 0) {
            console.log(`Found ${menuItems.length} menu items`);

            menuItems.forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log("Menu item clicked:", this.getAttribute('data-menu'));

                    const menuId = this.getAttribute('data-menu');
                    if (!menuId) return;

                    const submenu = document.getElementById(menuId);
                    const indicator = this.querySelector('.submenu-indicator');
                    const parentItem = this.closest('.nav-item');

                    if (!submenu) return;

                    // Periksa jika sidebar dalam mode collapsed (desktop)
                    const isCollapsedDesktop = !mediaQuery.matches &&
                                             layoutContainer &&
                                             layoutContainer.classList.contains('sidebar-collapsed');

                    if (isCollapsedDesktop) {
                        // Di mode collapsed, tidak toggle submenu
                        return;
                    }

                    // Close other open submenus (accordion behavior)
                    const otherActiveItems = document.querySelectorAll('.sidebar-menu-item.active:not([data-menu="' + menuId + '"])');
                    otherActiveItems.forEach(function(otherItem) {
                        const otherMenuId = otherItem.getAttribute('data-menu');
                        const otherSubmenu = document.getElementById(otherMenuId);
                        const otherIndicator = otherItem.querySelector('.submenu-indicator');

                        if (otherSubmenu) {
                            otherItem.classList.remove('active');
                            otherSubmenu.classList.remove('show');
                            if (otherIndicator) {
                                otherIndicator.classList.remove('rotated');
                            }
                        }
                    });

                    // Toggle current submenu
                    const isCurrentlyActive = this.classList.contains('active');

                    if (isCurrentlyActive) {
                        // Close current submenu
                        this.classList.remove('active');
                        submenu.classList.remove('show');
                        if (indicator) {
                            indicator.classList.remove('rotated');
                        }
                    } else {
                        // Open current submenu
                        this.classList.add('active');
                        submenu.classList.add('show');
                        if (indicator) {
                            indicator.classList.add('rotated');
                        }
                    }

                    console.log("Submenu toggled:", {
                        menuId: menuId,
                        isVisible: submenu.classList.contains('show'),
                        isActive: this.classList.contains('active')
                    });
                });
            });
        }

        // Handle responsive behavior
        function handleResize(e) {
            if (e.matches) {
                // Mobile view
                if (layoutContainer) {
                    layoutContainer.classList.remove('sidebar-collapsed');
                }
            } else {
                // Desktop view
                if (layoutContainer) {
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
        }

        // Initial check and add listener
        if (mediaQuery) {
            handleResize(mediaQuery);
            mediaQuery.addEventListener('change', handleResize);
        }

        // Initialize active submenus based on current page
        document.querySelectorAll('.sidebar-submenu.show').forEach(function(submenu) {
            const menuId = submenu.id;
            const menuItem = document.querySelector(`[data-menu="${menuId}"]`);
            const indicator = menuItem ? menuItem.querySelector('.submenu-indicator') : null;

            if (menuItem) {
                menuItem.classList.add('active');
                if (indicator) {
                    indicator.classList.add('rotated');
                }
            }
        });

        console.log("Sidebar initialization completed");
    }

    // Function to auto-hide alerts
    function initAutoHideAlerts() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 5000);
    }
}