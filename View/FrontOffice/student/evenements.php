<?php
/**
 * APPOLIOS - Student Evenements Catalog
 */
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <aside class="admin-sidebar student-space-sidebar">
                <div class="student-sidebar-panel">
                    <a class="student-sidebar-brand" href="<?= APP_ENTRY ?>?url=student/dashboard">
                        <span class="student-sidebar-brand-mark" aria-hidden="true">a</span>
                        <span class="student-sidebar-brand-text">Appolios</span>
                    </a>

                    <nav class="admin-sidebar-nav student-sidebar-nav" aria-label="Front Office Navigation">
                        <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 10.5L12 3l9 7.5"></path>
                                    <path d="M5 9.5V21h14V9.5"></path>
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </a>

                        <p class="student-sidebar-section">Pages</p>

                        <a href="<?= APP_ENTRY ?>?url=student/courses" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16v12H4z"></path>
                                    <path d="M8 10h8"></path>
                                    <path d="M8 14h5"></path>
                                </svg>
                            </span>
                            <span>Courses</span>
                        </a>

                        <a href="<?= APP_ENTRY ?>?url=student/evenements" class="admin-side-link active">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>
                            </span>
                            <span>Events</span>
                        </a>

                        <a href="<?= APP_ENTRY ?>?url=student/my-courses" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h7v12H3z"></path>
                                    <path d="M14 6h7v12h-7z"></path>
                                </svg>
                            </span>
                            <span>My Courses</span>
                        </a>

                        <a href="<?= APP_ENTRY ?>?url=student/profile" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20c2-4 5-6 8-6s6 2 8 6"></path>
                                </svg>
                            </span>
                            <span>Profile</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <div class="admin-main">
                <section class="student-events-hero-top">
                    <div class="student-events-hero-copy">
                        <span class="student-events-hero-kicker">Student Space</span>
                        <h1>Upcoming Events</h1>
                        <p>Welcome <?= htmlspecialchars($userName ?? ($_SESSION['user_name'] ?? 'Student')) ?>, discover event details and resources.</p>
                    </div>

                    <div class="student-events-hero-media" aria-hidden="true">
                        <article class="student-events-visual-card student-events-visual-card-main">
                            <img src="<?= APP_URL ?>/View/assets/images/about/06.jpg" alt="Students collaborating around a table" class="student-events-visual-img">
                        </article>
                        <article class="student-events-visual-card student-events-visual-card-sub">
                            <img src="<?= APP_URL ?>/View/assets/images/about/09.jpg" alt="Online conference session" class="student-events-visual-img">
                        </article>
                    </div>
                </section>

                <div class="dashboard-header student-events-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h2>All Events</h2>
                        <p>Browse every upcoming session and open details with one click.</p>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="custom-select-wrapper" style="position: relative; user-select: none; width: 150px; z-index: 50;">
                            <div class="custom-select-trigger" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #475569; font-family: inherit; font-weight: 500; font-size: 0.95rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s;">
                                <span class="custom-select-text">Sort By</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div class="custom-select-options" style="position: absolute; top: calc(100% + 8px); left: 0; width: 100%; background: white; border: 1px solid #eef2f6; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s; overflow: hidden; padding: 6px;">
                                <div class="custom-option" data-value="default" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Sort By</div>
                                <div class="custom-option" data-value="titleAsc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Title (A-Z)</div>
                                <div class="custom-option" data-value="titleDesc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Title (Z-A)</div>
                            </div>
                        </div>
                        <select id="studentEventSort" style="display: none;">
                            <option value="default">Sort By</option>
                            <option value="titleAsc">Title (A-Z)</option>
                            <option value="titleDesc">Title (Z-A)</option>
                        </select>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const wrapper = document.querySelector('.custom-select-wrapper');
                                const trigger = wrapper.querySelector('.custom-select-trigger');
                                const options = wrapper.querySelector('.custom-select-options');
                                const text = wrapper.querySelector('.custom-select-text');
                                const svg = wrapper.querySelector('svg');
                                const hiddenSelect = document.getElementById('studentEventSort');
                                const optionItems = wrapper.querySelectorAll('.custom-option');

                                trigger.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    const isOpen = options.style.visibility === 'visible';
                                    
                                    if (isOpen) {
                                        options.style.opacity = '0';
                                        options.style.visibility = 'hidden';
                                        options.style.transform = 'translateY(-10px)';
                                        svg.style.transform = 'rotate(0deg)';
                                        trigger.style.borderColor = '#e2e8f0';
                                        trigger.style.boxShadow = 'none';
                                    } else {
                                        options.style.opacity = '1';
                                        options.style.visibility = 'visible';
                                        options.style.transform = 'translateY(0)';
                                        svg.style.transform = 'rotate(180deg)';
                                        trigger.style.borderColor = '#548CA8';
                                        trigger.style.boxShadow = '0 0 0 3px rgba(84, 140, 168, 0.1)';
                                    }
                                });

                                optionItems.forEach(item => {
                                    item.addEventListener('click', function(e) {
                                        e.stopPropagation();
                                        const value = this.getAttribute('data-value');
                                        text.textContent = this.textContent;
                                        hiddenSelect.value = value;
                                        
                                        hiddenSelect.dispatchEvent(new Event('change'));
                                        
                                        options.style.opacity = '0';
                                        options.style.visibility = 'hidden';
                                        options.style.transform = 'translateY(-10px)';
                                        svg.style.transform = 'rotate(0deg)';
                                        trigger.style.borderColor = '#e2e8f0';
                                        trigger.style.boxShadow = 'none';
                                    });
                                });

                                document.addEventListener('click', function() {
                                    options.style.opacity = '0';
                                    options.style.visibility = 'hidden';
                                    options.style.transform = 'translateY(-10px)';
                                    svg.style.transform = 'rotate(0deg)';
                                    trigger.style.borderColor = '#e2e8f0';
                                    trigger.style.boxShadow = 'none';
                                });
                            });
                        </script>
                        <div style="position: relative;">
                            <input type="text" id="studentEventSearch" placeholder="Search event by title..." style="padding: 10px 15px 10px 35px; border-radius: 8px; border: 1px solid #e2e8f0; width: 250px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#548CA8'" onblur="this.style.borderColor='#e2e8f0'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <span class="student-events-header-chip">Live Catalog</span>
                    </div>
                </div>

                <?php if (!empty($evenements)): ?>
                    <div class="student-events-grid">
                        <?php foreach ($evenements as $event): ?>
                            <article class="student-event-card">
                                <div class="student-event-topline">
                                    <span class="student-event-status"><?= htmlspecialchars(strtoupper($event['statut'] ?? 'PLANIFIE')) ?></span>
                                    <span class="student-event-type"><?= htmlspecialchars($event['type'] ?? 'General') ?></span>
                                </div>
                                <h3><?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? 'Event')) ?></h3>
                                <p><?= htmlspecialchars(substr((string) ($event['description'] ?? ''), 0, 140)) ?>...</p>

                                <div class="student-event-meta">
                                    <span><strong>Date:</strong> <?= htmlspecialchars((string) (($event['date_debut'] ?? '') ?: date('Y-m-d', strtotime((string) ($event['event_date'] ?? 'now'))))) ?></span>
                                    <span><strong>Time:</strong> <?= htmlspecialchars((string) (!empty($event['heure_debut']) ? substr((string) $event['heure_debut'], 0, 5) : date('H:i', strtotime((string) ($event['event_date'] ?? 'now'))))) ?></span>
                                    <span><strong>Location:</strong> <?= htmlspecialchars((string) (($event['lieu'] ?? '') ?: ($event['location'] ?? 'TBA'))) ?></span>
                                    <span><strong>Resources:</strong> <?= (int) ($event['resource_count'] ?? 0) ?></span>
                                </div>

                                <a href="<?= APP_ENTRY ?>?url=student/evenement/<?= (int) $event['id'] ?>" class="btn btn-primary btn-block">View Details</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container student-events-empty">
                        <h3>No Events Yet</h3>
                        <p>You will see upcoming events here as soon as they are published.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentEventSearch');
    const sortSelect = document.getElementById('studentEventSort');
    const grid = document.querySelector('.student-events-grid');
    if (!grid) return;

    let cards = Array.from(grid.querySelectorAll('.student-event-card'));

    // Search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            cards.forEach(card => {
                const titleEl = card.querySelector('h3');
                if (titleEl) {
                    const titleText = titleEl.textContent.toLowerCase();
                    card.style.display = titleText.includes(filter) ? '' : 'none';
                }
            });
        });
    }

    // Sort
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const val = this.value;
            if (val === 'default') return;

            // Separate visible and hidden to avoid messing up the filtered view
            const visibleCards = cards.filter(card => card.style.display !== 'none');
            const hiddenCards = cards.filter(card => card.style.display === 'none');

            visibleCards.sort((a, b) => {
                const aTitle = a.querySelector('h3').textContent.trim();
                const bTitle = b.querySelector('h3').textContent.trim();
                
                if (val === 'titleAsc') {
                    return aTitle.localeCompare(bTitle);
                } else if (val === 'titleDesc') {
                    return bTitle.localeCompare(aTitle);
                }
                return 0;
            });

            // Re-append to DOM
            grid.innerHTML = '';
            visibleCards.forEach(card => grid.appendChild(card));
            hiddenCards.forEach(card => grid.appendChild(card));
        });
    }
});
</script>
