<style>
    .fw-4 {
        font-weight: 400;
    }

    .fw-5 {
        font-weight: 500;
    }

    .fw-6 {
        font-weight: 600;
    }

    .bg-edit {
        color: rgb(255, 11, 11);
        box-shadow: #bbb;
    }

    .bg-icon {
        color: #ff970b;
        box-shadow: #bbb;
    }

    .bg-icon:hover {
        color: rgb(248, 77, 52, 1);
    }

    .btn-gradient {
        color: #ffffff;
        border: 0;
        border-radius: 5px;
        background: #4f46e5;
        background: linear-gradient(81deg, #4f46e5 0%, #7c3aed 100%);
        transition: all 0.3s ease;
    }

    .btn-gradient:hover,
    .btn-gradient:active,
    .btn-gradient.active {
        color: #eeeeee;
        background: #4338ca;
        background: linear-gradient(81deg, #4338ca 0%, #6d28d9 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-gradient-outline {
        border-radius: 5px;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
        transition: all 0.3s ease;
    }

    .btn-gradient-outline:hover,
    .btn-gradient-outline:active,
    .btn-gradient-outline.active {
        background: linear-gradient(to right, #4f46e5, #7c3aed);
        color: #ffffff;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Gradient Background Classes */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    /* Badge Enhancements */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%) !important;
    }

    /* Modal Enhancements */
    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Form Enhancements */
    .form-control:focus,
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    .form-label.fw-500 {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    /* Table Enhancements */
    .table-hover tbody tr:hover {
        background-color: rgba(79, 70, 229, 0.05);
    }

    .table-light {
        background-color: #f9fafb;
    }

    /* Button Group Enhancements */
    .btn-group .btn {
        border-radius: 0.375rem;
        margin: 0 0.125rem;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }

    .btn.disabled {
        background-color: #CECECE;
        color: #ffffff;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 34px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    .switch-slider:hover {
        background-color: #bbb;
    }

    .switch-slider:before {
        transition: transform 0.4s ease, background-color 0.4s ease;
    }

    .switch-input:checked+.switch-slider {
        /* background-color: #ff970b; */
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
    }

    .switch-input:checked+.switch-slider:before {
        transform: translateX(26px);
    }

    .bg-menu-theme .menu-inner>.menu-item.active>.menu-link {
        color: #000;
        background-color: rgba(255, 151, 11, .16) !important;
    }

    .bg-menu-theme .menu-inner>.menu-item.active:before {
        background: #ff970b;
    }

    .page-item.active .page-link,
    .page-item.active .page-link:hover,
    .page-item.active .page-link:focus,
    .pagination li.active>a:not(.page-link),
    .pagination li.active>a:not(.page-link):hover,
    .pagination li.active>a:not(.page-link):focus {
        border-color: #ff970b;
        background-color: #ff970b;
        color: #fff;
        box-shadow: 0 .125rem .25rem rgba(255, 151, 11, .16);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Soft Text Colors */
    .text-primary {
        color: #4f46e5 !important;
    }

    .text-secondary {
        color: #6b7280 !important;
    }

    .text-muted {
        color: #9ca3af !important;
    }

    /* Soft Background Colors */
    .bg-light {
        background-color: #f9fafb !important;
    }

    .bg-white {
        background-color: #ffffff !important;
    }

    /* Card Background Variations */
    .card.border-primary {
        border-color: rgba(79, 70, 229, 0.2) !important;
    }

    .card.border-success {
        border-color: rgba(5, 150, 105, 0.2) !important;
    }

    .card.border-info {
        border-color: rgba(8, 145, 178, 0.2) !important;
    }

    .card.border-warning {
        border-color: rgba(217, 119, 6, 0.2) !important;
    }

    /* Modal Header Backgrounds */
    .modal-header.bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
    }

    .modal-header.bg-gradient-info {
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important;
    }

    .modal-header.bg-gradient-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
    }

    /* Admin Management Specific Styles */
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 0.875rem;
    }

    .avatar-lg {
        width: 60px;
        height: 60px;
        font-size: 1.25rem;
    }

    .avatar-xl {
        width: 80px;
        height: 80px;
        font-size: 1.5rem;
    }

    /* Enhanced Table Styles */
    .table th {
        border-top: none;
        font-weight: 600;
        color: #374151;
        background-color: #f9fafb;
    }

    .table td {
        vertical-align: middle;
        border-color: #e5e7eb;
    }

    /* Badge Enhancements */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
    }

    /* Button Group Enhancements */
    .btn-group .btn {
        border-radius: 0.375rem;
        margin: 0 0.125rem;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }

    /* Search and Filter Enhancements */
    .input-group-text {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #6b7280;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    /* Modal Enhancements */
    .modal-content {
        border: none;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Role Badge with Remove Button */
    .badge .btn-close {
        font-size: 0.5rem;
        margin-left: 0.25rem;
        opacity: 0.8;
    }

    .badge .btn-close:hover {
        opacity: 1;
    }

    /* Loading States */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
    }

    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }

        to {
            transform: rotate(1turn);
        }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .btn-group {
            display: flex;
            flex-direction: column;
        }

        .btn-group .btn {
            margin: 0.125rem 0;
            border-radius: 0.375rem !important;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }

    /* Custom Scrollbar */
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Sticky Table Header */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f9fafb;
    }

    /* Modal Table Enhancements */
    .modal .table-responsive {
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .modal .table th {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
        padding: 0.75rem;
    }

    .modal .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .modal .table tbody tr:hover {
        background-color: rgba(79, 70, 229, 0.05);
    }

    /* Selection Badge Enhancements */
    .badge .btn-close {
        font-size: 0.5rem;
        margin-left: 0.25rem;
        opacity: 0.8;
        padding: 0;
        background: none;
        border: none;
        color: inherit;
    }

    .badge .btn-close:hover {
        opacity: 1;
    }

    /* Modal Layout Enhancements */
    .modal-xl {
        max-width: 90%;
    }

    @media (min-width: 1200px) {
        .modal-xl {
            max-width: 1140px;
        }
    }

    /* Enhanced Form Controls */
    .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }

    .form-check-input:focus {
        border-color: #4f46e5;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    /* Loading State for Modal */
    .modal-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.6;
    }

    .modal-loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 40px;
        height: 40px;
        margin: -20px 0 0 -20px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #4f46e5;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 1000;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .text-gray-800 {
        color: #2d3748 !important;
    }

    .text-gray-700 {
        color: #4a5568 !important;
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }

    .text-info {
        color: #0dcaf0 !important;
    }

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .text-success {
        color: #198754 !important;
    }

    .rank-badge .badge {
        font-size: 0.875rem;
        border-radius: 0.5rem;
    }

    .sales-amount .badge {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .total-amount .badge {
        font-size: 0.9rem;
        font-weight: 700;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .empty-state {
        padding: 3rem 0;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }


    .table tbody tr {
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Hover effects */
    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
        transform: translateX(2px);
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    /* Validation Error Styles */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Success state for valid fields */
    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Animation for error messages */
    .text-danger {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Focus styles */
    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        .btn {
            width: 100%;
        }

        .input-group {
            width: 100% !important;
        }
    }
</style>