/**
 * APPOLIOS - Main JavaScript File
 * Handles interactive functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // THEME TOGGLE (aligné sur public/css/style.css : classe body.light = mode clair)
    // ============================================
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    function applyStoredTheme() {
        const saved = localStorage.getItem('theme');
        if (saved === 'light') {
            body.classList.add('light');
        } else if (saved === 'dark') {
            body.classList.remove('light');
        } else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
            body.classList.add('light');
        } else {
            body.classList.remove('light');
        }
    }

    applyStoredTheme();

    if (themeToggle) {
        themeToggle.style.cursor = 'pointer';
        themeToggle.setAttribute('role', 'button');
        themeToggle.setAttribute('tabindex', '0');
        themeToggle.addEventListener('click', function() {
            body.classList.toggle('light');
            localStorage.setItem('theme', body.classList.contains('light') ? 'light' : 'dark');
        });
        themeToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                themeToggle.click();
            }
        });
    }

    // ============================================
    // MOBILE MENU TOGGLE
    // ============================================
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });

        // Close menu when clicking on a link
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }

    // ============================================
    // FLASH MESSAGE AUTO-DISMISS
    // ============================================
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s ease';
            setTimeout(function() {
                message.remove();
            }, 500);
        }, 5000);
    });

    // ============================================
    // SCROLL ANIMATIONS
    // ============================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.card, .feature-item, .course-card, .stat-card').forEach(function(el) {
        observer.observe(el);
    });

    // ============================================
    // FORM SUBMISSION MODE (PHP VALIDATION ONLY)
    // ============================================
    const forms = document.querySelectorAll('form');

    forms.forEach(function(form) {
        // Turn off browser-native constraints so server-side PHP controls validation.
        form.setAttribute('novalidate', 'novalidate');
    });

    // ============================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ============================================
    // PROGRESS BAR ANIMATION
    // ============================================
    const progressBars = document.querySelectorAll('[style*="width:"][style*="%"]');
    progressBars.forEach(function(bar) {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(function() {
            bar.style.transition = 'width 1s ease';
            bar.style.width = width;
        }, 100);
    });

    // ============================================
    // TABLE ROW HIGHLIGHT
    // ============================================
    document.querySelectorAll('table tbody tr').forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(77, 168, 218, 0.1)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Show a toast notification
 * @param {string} message - The message to display
 * @param {string} type - Type: success, error, warning, info
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `flash-message ${type}`;
    toast.innerHTML = `<span>${message}</span>`;
    toast.style.position = 'fixed';
    toast.style.top = '80px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';

    document.body.appendChild(toast);

    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s ease';
        setTimeout(function() {
            toast.remove();
        }, 500);
    }, 3000);
}

/**
 * Format date for display
 * @param {string} dateString - The date string to format
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Validate email format
 * @param {string} email - The email to validate
 * @returns {boolean} True if valid
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Password strength checker
 * @param {string} password - The password to check
 * @returns {object} Strength info
 */
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];

    if (password.length >= 6) strength++;
    else feedback.push('At least 6 characters');

    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('Include uppercase letter');

    if (/[0-9]/.test(password)) strength++;
    else feedback.push('Include number');

    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('Include special character');

    const levels = ['Weak', 'Fair', 'Good', 'Strong'];

    return {
        score: strength,
        level: levels[strength] || 'Weak',
        feedback: feedback
    };
}