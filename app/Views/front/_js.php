
<script>
    class SidebarManager {
        constructor() {
            this.sidebar = document.getElementById('sidebar');
            this.mainHeader = document.getElementById('mainHeader');
            this.mainContent = document.getElementById('mainContent');
            this.sidebarToggle = document.getElementById('sidebarToggle');
            this.sidebarCollapseToggle = document.getElementById('sidebarCollapseToggle');
            this.sidebarOverlay = document.getElementById('sidebarOverlay');

            this.init();
        }

        init() {
            this.bindEvents();
            // this.handleCollapseToggles();
            // this.autoOpenActiveCollapse();
            // this.addCollapseAnimations();
            this.addKeyboardShortcuts();
            this.scrollToActiveItem();

            // Initialize collapse states
            // this.initializeCollapseStates();
        }

        // initializeCollapseStates() {
        //     const collapseToggles = document.querySelectorAll('.collapse-toggle');

        //     collapseToggles.forEach(toggle => {
        //         const targetId = toggle.getAttribute('data-bs-target');
        //         const targetCollapse = document.querySelector(targetId);

        //         if (targetCollapse) {
        //             // Set initial state
        //             const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        //             if (isExpanded) {
        //                 targetCollapse.classList.add('show');
        //                 const arrow = toggle.querySelector('.collapse-arrow');
        //                 if (arrow) {
        //                     arrow.style.transform = 'rotate(180deg)';
        //                 }
        //             } else {
        //                 targetCollapse.classList.remove('show');
        //                 const arrow = toggle.querySelector('.collapse-arrow');
        //                 if (arrow) {
        //                     arrow.style.transform = 'rotate(0deg)';
        //                 }
        //             }
        //         }
        //     });
        // }

        bindEvents() {
            // Mobile sidebar toggle
            this.sidebarToggle?.addEventListener('click', () => {
                this.toggleMobileSidebar();
            });

            // Desktop sidebar collapse
            this.sidebarCollapseToggle?.addEventListener('click', () => {
                this.toggleDesktopSidebar();
            });

            // Close sidebar on overlay click
            this.sidebarOverlay?.addEventListener('click', () => {
                this.closeMobileSidebar();
            });

            // Close sidebar on window resize if mobile
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    this.closeMobileSidebar();
                }
            });
        }

        toggleMobileSidebar() {
            if (!this.sidebar) return;

            this.sidebar.classList.toggle('show');
            this.sidebarOverlay?.classList.toggle('show');

            // Prevent body scroll when sidebar is open
            document.body.style.overflow = this.sidebar.classList.contains('show') ? 'hidden' : '';
        }

        closeMobileSidebar() {
            if (!this.sidebar) return;

            this.sidebar.classList.remove('show');
            this.sidebarOverlay?.classList.remove('show');
            document.body.style.overflow = '';
        }

        toggleDesktopSidebar() {
            if (!this.sidebar) return;

            this.sidebar.classList.toggle('sidebar-collapsed');
            this.mainHeader?.classList.toggle('header-collapsed');
            this.mainContent?.classList.toggle('content-collapsed');
        }

        // handleCollapseToggles() {
        //     const collapseToggles = document.querySelectorAll('.collapse-toggle');

        //     collapseToggles.forEach(toggle => {
        //         // ลบ event listener เก่าออกก่อน
        //         const newToggle = toggle.cloneNode(true);
        //         toggle.parentNode.replaceChild(newToggle, toggle);

        //         // เพิ่ม event listener ใหม่
        //         newToggle.addEventListener('click', (e) => {
        //             // ไม่ต้อง preventDefault เพื่อให้ Bootstrap ทำงานได้
        //             this.handleCollapseClick(e);
        //         });
        //     });
        // }

        // handleCollapseClick(e) {
        //     const toggle = e.currentTarget;
        //     const targetId = toggle.getAttribute('data-bs-target');
        //     const targetCollapse = document.querySelector(targetId);

        //     if (!targetCollapse) return;

        //     console.log('Collapse toggle clicked:', toggle);

        //     // ใช้ setTimeout เพื่อให้ Bootstrap ทำงานก่อน แล้วค่อยอัพเดท UI
        //     setTimeout(() => {
        //         const isExpanded = targetCollapse.classList.contains('show');

        //         // อัพเดท aria-expanded
        //         toggle.setAttribute('aria-expanded', isExpanded.toString());

        //         // อัพเดท arrow
        //         const arrow = toggle.querySelector('.collapse-arrow');
        //         if (arrow) {
        //             arrow.style.transform = isExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
        //         }

        //         console.log('Collapse state updated:', {
        //             targetId,
        //             isExpanded
        //         });
        //     }, 10);
        // }

        // toggleCollapse(toggle) {
        //     const targetId = toggle.getAttribute('data-bs-target');
        //     const targetCollapse = document.querySelector(targetId);
        //     const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        //     console.log('Toggle state:', {
        //         targetId,
        //         isExpanded,
        //         hasCollapse: !!targetCollapse
        //     });

        //     if (!targetCollapse) return;

        //     // Close other collapses (optional behavior)
        //     this.closeOtherCollapses(toggle);

        //     // Toggle current collapse
        //     if (isExpanded) {
        //         console.log('Closing collapse');
        //         this.closeCollapse(targetCollapse, toggle);
        //     } else {
        //         console.log('Opening collapse');
        //         this.openCollapse(targetCollapse, toggle);
        //     }
        // }

        // closeOtherCollapses(currentToggle) {
        //     const allToggles = document.querySelectorAll('.collapse-toggle');

        //     allToggles.forEach(toggle => {
        //         if (toggle !== currentToggle) {
        //             const targetId = toggle.getAttribute('data-bs-target');
        //             const targetCollapse = document.querySelector(targetId);

        //             if (targetCollapse && targetCollapse.classList.contains('show')) {
        //                 this.closeCollapse(targetCollapse, toggle);
        //             }
        //         }
        //     });
        // }

        openCollapse(collapse, toggle) {
            collapse.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');

            // Update arrow rotation
            const arrow = toggle.querySelector('.collapse-arrow');
            if (arrow) {
                arrow.style.transform = 'rotate(180deg)';
            }
        }

        closeCollapse(collapse, toggle) {
            collapse.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');

            // Reset arrow rotation
            const arrow = toggle.querySelector('.collapse-arrow');
            if (arrow) {
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        autoOpenActiveCollapse() {
            const activeSubLinks = document.querySelectorAll('.nav-sub-link.active');

            activeSubLinks.forEach(activeLink => {
                const collapse = activeLink.closest('.collapse');
                if (!collapse) return;

                const toggle = document.querySelector(`[data-bs-target="#${collapse.id}"]`);

                if (toggle) {
                    this.openCollapse(collapse, toggle);
                }
            });
        }

        addCollapseAnimations() {
            const collapses = document.querySelectorAll('.sidebar .collapse');

            collapses.forEach(collapse => {
                // Show animation
                collapse.addEventListener('show.bs.collapse', function() {
                    this.style.height = '0px';
                    this.style.height = this.scrollHeight + 'px';
                });

                // Hide animation
                collapse.addEventListener('hide.bs.collapse', function() {
                    this.style.height = this.scrollHeight + 'px';
                    setTimeout(() => {
                        this.style.height = '0px';
                    }, 10);
                });

                // Clean up after animations
                collapse.addEventListener('shown.bs.collapse', function() {
                    this.style.height = 'auto';
                });

                collapse.addEventListener('hidden.bs.collapse', function() {
                    this.style.height = '';
                });
            });
        }

        addKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // ESC to close mobile sidebar
                if (e.key === 'Escape' && this.sidebar?.classList.contains('show')) {
                    this.closeMobileSidebar();
                }

                // Ctrl/Cmd + B to toggle sidebar
                if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                    e.preventDefault();
                    if (window.innerWidth >= 992) {
                        this.toggleDesktopSidebar();
                    } else {
                        this.toggleMobileSidebar();
                    }
                }
            });
        }

        scrollToActiveItem() {
            const activeLink = this.sidebar?.querySelector('.nav-link.active');
            if (activeLink) {
                setTimeout(() => {
                    activeLink.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 300);
            }
        }

        // Public methods for external use
        openSidebar() {
            if (window.innerWidth >= 992) {
                this.sidebar?.classList.remove('sidebar-collapsed');
                this.mainHeader?.classList.remove('header-collapsed');
                this.mainContent?.classList.remove('content-collapsed');
            } else {
                this.sidebar?.classList.add('show');
                this.sidebarOverlay?.classList.add('show');
            }
        }

        closeSidebar() {
            if (window.innerWidth >= 992) {
                this.sidebar?.classList.add('sidebar-collapsed');
                this.mainHeader?.classList.add('header-collapsed');
                this.mainContent?.classList.add('content-collapsed');
            } else {
                this.closeMobileSidebar();
            }
        }

        isOpen() {
            if (window.innerWidth >= 992) {
                return !this.sidebar?.classList.contains('sidebar-collapsed');
            } else {
                return this.sidebar?.classList.contains('show');
            }
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Sidebar Manager
        window.sidebarManager = new SidebarManager();

        // Chart.js basic configuration (if needed)
        window.chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        };

        // Add loading states for navigation links
        const navLinks = document.querySelectorAll('.sidebar .nav-link:not(.collapse-toggle)');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Skip loading state for logout, external links, or anchors
                if (this.href.includes('logout') ||
                    this.href.includes('mailto:') ||
                    this.href.includes('tel:') ||
                    this.href.includes('#')) {
                    return;
                }

                // Add loading state
                const icon = this.querySelector('i');
                if (icon) {
                    const originalClass = icon.className;
                    icon.className = 'fa-solid fa-spinner fa-spin';

                    // Remove loading state after delay
                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 1000);
                }
            });
        });
    });

    // Utility functions for external use
    window.SidebarUtils = {
        toggle: () => window.sidebarManager?.toggleMobileSidebar(),
        open: () => window.sidebarManager?.openSidebar(),
        close: () => window.sidebarManager?.closeSidebar(),
        isOpen: () => window.sidebarManager?.isOpen() || false
    };


    function notifySocketIO(endpoint, data) {
        try {
            $.ajax({
                url: `<?= getenv('SOCKET_IO_URL') ?>/api/${endpoint}`,
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    console.log('Socket notification sent successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to send socket notification:', error);
                }
            });
        } catch (error) {
            console.error('Error sending socket notification:', error);
        }
    }

    function formatLargeNumber(value, element) {
        const numValue = parseFloat(value);
        const digits = value.toString().replace(/[,.]/g, '').length;

        if (digits >= 8) {
            element.style.fontSize = '1.8rem';
        } else if (digits >= 6) {
            element.style.fontSize = '2.0rem';
        } else {
            element.style.fontSize = '2.5rem';
        }

        if (numValue >= 1000000000) {
            return (numValue / 1000000000).toFixed(1) + 'B';
        } else if (numValue >= 1000000) {
            return (numValue / 1000000).toFixed(1) + 'M';
        } else if (numValue >= 1000) {
            return (numValue / 1000).toFixed(1) + 'K';
        }

        return numValue.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatCount(value, element) {
        const numValue = parseInt(value);
        const digits = value.toString().length;

        if (digits >= 7) {
            element.style.fontSize = '1.8rem';
        } else if (digits >= 5) {
            element.style.fontSize = '2.0rem';
        } else {
            element.style.fontSize = '2.5rem';
        }

        if (numValue >= 1000000) {
            return (numValue / 1000000).toFixed(1) + 'M';
        } else if (numValue >= 1000) {
            return (numValue / 1000).toFixed(1) + 'K';
        }

        return numValue.toLocaleString('th-TH');
    }
</script>