import * as bootstrap from 'bootstrap';

// Bootstrap's JS (Offcanvas, Modal, Dropdown, Toast, Carousel, Tabs, etc.)
// is imported as a namespace and attached to window so Blade views can use
// the same declarative data-bs-* attributes without every page needing its
// own <script type="module"> import.
window.bootstrap = bootstrap;

// Auto-show the flash-message toast on every page load, if present.
document.addEventListener('DOMContentLoaded', () => {
    const flashToastEl = document.getElementById('flashToast');
    if (flashToastEl) {
        new bootstrap.Toast(flashToastEl).show();
    }
});
