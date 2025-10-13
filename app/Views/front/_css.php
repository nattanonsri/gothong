<style>
    :root {
        /* Font & Layout Variables */
        --bs-font-sans-serif: 'Inter', system-ui, -apple-system, sans-serif;
        --sidebar-width: 280px;
        --header-height: 70px;

        /* Color Variables - Softer, Eye-friendly Colors */
        --sidebar-bg: linear-gradient(135deg, #64748b 0%, #475569 100%);
        --sidebar-text: #f8fafc;
        --sidebar-text-hover: #ffffff;
        --sidebar-active: rgba(255, 255, 255, 0.15);
        --sidebar-hover: rgba(255, 255, 255, 0.08);
        --dropdown-bg: rgba(51, 65, 85, 0.1);

        /* Shadow Variables */
        --card-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.08), 0 1px 2px -1px rgb(0 0 0 / 0.08);
        --sidebar-shadow: 0 4px 20px rgba(51, 65, 85, 0.1);

        /* Transition Variables */
        --transition-fast: 0.2s ease;
        --transition-normal: 0.3s ease;
        --transition-slow: 0.35s ease;
    }

    /* ==================== BASE STYLES ==================== */
    body {
        font-family: var(--bs-font-sans-serif);
        background-color: #f8fafc;
    }

    /* ==================== FONT WEIGHT ==================== */
    .fw-100 {
        font-weight: 100;
    }

    .fw-200 {
        font-weight: 200;
    }

    .fw-300 {
        font-weight: 300;
    }

    .fw-400 {
        font-weight: 400;
    }

    .fw-500 {
        font-weight: 500;
    }

    .fw-600 {
        font-weight: 600;
    }

    .fw-700 {
        font-weight: 700;
    }

    .fw-800 {
        font-weight: 800;
    }

    .fw-900 {
        font-weight: 900;
    }

    /* ==================== SIDEBAR CORE STYLES ==================== */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        transition: transform var(--transition-normal);
        z-index: 1050;
        overflow-y: auto;
        box-shadow: var(--sidebar-shadow);
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Sidebar States */
    .sidebar.sidebar-collapsed {
        transform: translateX(-100%);
    }

    /* ==================== SIDEBAR BRAND ==================== */
    .sidebar .navbar-brand {
        padding: 2rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        background: rgba(0, 0, 0, 0.1);
    }

    .sidebar .navbar-brand img {
        filter: brightness(0) invert(1);
        margin-bottom: 0.5rem;
    }

    .sidebar .navbar-brand span {
        display: block;
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        letter-spacing: 0.5px;
    }

    /* ==================== NAVIGATION SECTIONS ==================== */
    .sidebar .nav {
        padding: 1rem 0;
    }

    .sidebar .nav-section {
        padding: 1rem 1.5rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ==================== COLLAPSE ARROW ANIMATION ==================== */
    .collapse-arrow {
        transition: transform var(--transition-normal);
        transform: rotate(0deg);
    }

    .collapse-toggle[aria-expanded="true"] .collapse-arrow {
        transform: rotate(180deg);
    }

    .sidebar .nav-section:first-child {
        margin-top: 0;
    }

    /* ==================== NAVIGATION LINKS ==================== */
    .sidebar .nav-link {
        color: var(--sidebar-text);
        padding: 0.875rem 1.5rem;
        border-radius: 0;
        transition: all var(--transition-normal);
        border: none;
        background: none;
        display: flex;
        align-items: center;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .sidebar .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: white;
        transform: scaleY(0);
        transition: transform var(--transition-normal);
    }

    .sidebar .nav-link:hover::before,
    .sidebar .nav-link.active::before {
        transform: scaleY(1);
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        color: var(--sidebar-text-hover);
        background: var(--sidebar-active);
        /* transform: translateX(4px); */
    }

    .sidebar .nav-link i {
        width: 20px;
        margin-right: 0.875rem;
        font-size: 1.1rem;
        text-align: center;
        transition: transform var(--transition-normal);
    }

    .sidebar .nav-link:hover i,
    .sidebar .nav-link.active i {
        transform: scale(1.1);
    }

    .sidebar .nav-link span {
        font-weight: 500;
        font-size: 0.95rem;
    }

    .sidebar .nav-link .badge {
        margin-left: auto;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        background: rgba(255, 255, 255, 0.9);
        color: #667eea;
        border-radius: 50px;
    }

    /* ==================== COLLAPSE FUNCTIONALITY ==================== */
    .sidebar .collapse-toggle {
        justify-content: space-between;
        align-items: center;
    }

    .sidebar .collapse-arrow {
        font-size: 0.8rem;
        transition: transform var(--transition-normal);
        margin-left: auto;
    }

    .sidebar .collapse-toggle[aria-expanded="true"] .collapse-arrow {
        transform: rotate(180deg);
    }

    .sidebar .collapse-toggle:hover .collapse-arrow {
        color: white;
    }

    /* Collapse animation */
    .sidebar .collapse {
        transition: height var(--transition-slow);
        overflow: hidden;
        height: 0;
        max-height: 0;
    }

    .sidebar .collapse.show {
        height: auto;
        max-height: 1000px;
    }

    .sidebar .collapse-content {
        background: var(--dropdown-bg);
        border-left: 3px solid rgba(255, 255, 255, 0.2);
        margin-left: 1rem;
        border-radius: 0 0 8px 0;
        overflow: hidden;
    }

    .sidebar .nav-sub-link {
        padding: 0.75rem 1rem 0.75rem 2rem;
        margin: 0;
        position: relative;
        background: transparent;
        border-radius: 0;
    }

    .sidebar .nav-sub-link::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        transition: all var(--transition-normal);
    }

    .sidebar .nav-sub-link:hover::before,
    .sidebar .nav-sub-link.active::before {
        background: white;
        transform: translateY(-50%) scale(1.5);
    }

    .sidebar .nav-sub-link:hover,
    .sidebar .nav-sub-link.active {
        background: var(--sidebar-hover);
        color: white;
        transform: translateX(4px);
    }

    .sidebar .nav-sub-link i {
        width: 16px;
        margin-right: 0.75rem;
        font-size: 0.9rem;
    }

    .sidebar .nav-sub-link span {
        font-size: 0.9rem;
        font-weight: 400;
    }

    /* Active Parent State */
    .sidebar .nav-item:has(.nav-sub-link.active) .collapse-toggle {
        color: var(--sidebar-text-hover);
        background: var(--sidebar-active);
    }

    .sidebar .nav-item:has(.nav-sub-link.active) .collapse-toggle::before {
        transform: scaleY(1);
    }

    .sidebar .nav-item:has(.nav-sub-link.active) .collapse {
        display: block !important;
    }

    .sidebar .nav-item:has(.nav-sub-link.active) .collapse-arrow {
        transform: rotate(180deg);
    }

    /* ==================== HEADER STYLES ==================== */
    .main-header {
        position: fixed;
        top: 0;
        left: var(--sidebar-width);
        right: 0;
        height: var(--header-height);
        background-color: white;
        border-bottom: 1px solid #e2e8f0;
        z-index: 1040;
        transition: left var(--transition-normal);
    }

    .header-collapsed {
        left: 0;
    }

    /* ==================== MAIN CONTENT ==================== */
    .main-content {
        margin-left: var(--sidebar-width);
        margin-top: var(--header-height);
        padding: 2rem;
        min-height: calc(100vh - var(--header-height));
        transition: margin-left var(--transition-normal);
    }

    .content-collapsed {
        margin-left: 0;
    }

    /* ==================== OVERLAY ==================== */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: none;
        backdrop-filter: blur(4px);
    }

    .sidebar-overlay.show {
        display: block;
        animation: fadeIn var(--transition-normal);
    }

    /* ==================== ANIMATIONS ==================== */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideInLeft {
        from {
            transform: translateX(-100%);
        }

        to {
            transform: translateX(0);
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sidebar.show {
        animation: slideInLeft var(--transition-normal);
    }

    .sidebar .collapse.show {
        animation: slideDown var(--transition-slow);
    }

    .sidebar .collapsing {
        transition: height var(--transition-slow);
    }

    /* ==================== RESPONSIVE DESIGN ==================== */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-header {
            left: 0;
        }

        .main-content {
            margin-left: 0;
        }

        .sidebar .collapse-content {
            margin-left: 0.5rem;
        }

        .sidebar .nav-sub-link {
            padding-left: 1.5rem;
        }
    }

    /* ==================== UTILITY CLASSES ==================== */
    .fade-in {
        animation: fadeIn var(--transition-normal) ease-in;
    }

    .nav-item+.nav-item {
        margin-top: 0.25rem;
    }

    /* ==================== CARD STYLES ==================== */
    .card {
        box-shadow: var(--card-shadow);
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* Stats Cards */
    .stats-card {
        background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
        color: white;
        border: none;
    }

    .stats-card.primary {
        --gradient-from: #3b82f6;
        --gradient-to: #1d4ed8;
    }

    .stats-card.success {
        --gradient-from: #10b981;
        --gradient-to: #059669;
    }

    .stats-card.warning {
        --gradient-from: #f59e0b;
        --gradient-to: #d97706;
    }

    .stats-card.danger {
        --gradient-from: #ef4444;
        --gradient-to: #dc2626;
    }

    /* ==================== TABLE STYLES ==================== */
    .table {
        --bs-table-bg: white;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e2e8f0;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-badge.approved {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>