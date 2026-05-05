<?php
/**
 * Shared visual system for student Groups & Discussions (scoped under .collab-hub).
 */
?>
<style>
.collab-hub {
    --ch-teal: #2b4865;
    --ch-teal-mid: #355c7d;
    --ch-teal-soft: #548ca8;
    --ch-coral: #e19864;
    --ch-slate: #0f172a;
    --ch-muted: #64748b;
    --ch-line: rgba(148, 163, 184, 0.35);
    --ch-glass: rgba(255, 255, 255, 0.72);
    --ch-glass-border: rgba(255, 255, 255, 0.55);
    /* Surfaces for lists / detail / feed (adapt with body.dark-mode) */
    --collab-disc-bg: rgba(255, 255, 255, 0.92);
    --collab-disc-border: rgba(226, 232, 240, 0.95);
    --collab-disc-shadow: 0 10px 36px rgba(15, 23, 42, 0.07);
    --collab-group-bg: rgba(255, 255, 255, 0.95);
    --collab-group-border: rgba(226, 232, 240, 0.9);
    --collab-group-body-bg: rgba(255, 255, 255, 0.98);
    --collab-group-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
    --collab-group-approved-bg: rgba(255, 255, 255, 0.99);
    --collab-group-approved-body-bg: rgba(255, 255, 255, 1);
    --collab-panel-bg: rgba(255, 255, 255, 0.94);
    --collab-panel-border: rgba(226, 232, 240, 0.95);
    --collab-panel-shadow: 0 14px 44px rgba(15, 23, 42, 0.06);
    --collab-thread-card-bg: #fafbfc;
    --collab-thread-card-border: #eef2f6;
    --collab-compose-bg: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
    --collab-feed-panel-bg: linear-gradient(165deg, #ffffff 0%, #f8fafc 55%, #f1f5f9 100%);
    --collab-feed-composer-bg: #fff;
    --collab-feed-post-bg: #fff;
    --collab-form-shell-bg: rgba(255, 255, 255, 0.96);
    --collab-form-shell-border: rgba(226, 232, 240, 0.95);
    --collab-empty-bg: linear-gradient(160deg, #f8fafc 0%, #fff 50%);
    --collab-member-chip-bg: rgba(255, 255, 255, 0.92);
    --collab-toolbar-input-bg: rgba(255, 255, 255, 0.95);
    --collab-pill-group-bg: linear-gradient(135deg, #e9f1fa 0%, #f1f5f9 100%);
    --collab-feed-composer-textarea-bg: #f8fafc;
    --collab-feed-composer-textarea-focus-bg: #fff;
    --collab-feed-composer-toolbar-border: #eef2f6;
    --collab-feed-composer-attach-bg: #fff;
    --collab-feed-composer-attach-hover-bg: #f0f9ff;
    --collab-feed-panel-head-icon-color: #0f172a;
    --collab-feed-avatar-text: #0f172a;
    --collab-feed-join-bg: #fff;
    --collab-feed-join-border: rgba(226, 232, 240, 0.98);
    --collab-feed-join-shadow: 0 8px 26px rgba(15, 23, 42, 0.05);
    --collab-feed-join-icon-bg: #ecfeff;
    --collab-feed-join-icon-border: rgba(6, 182, 212, 0.25);
    font-family: 'Inter', system-ui, sans-serif;
}

.collab-hub .collab-hero {
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    padding: 2rem 2rem 2.25rem;
    margin-bottom: 1.75rem;
    background: linear-gradient(135deg, var(--ch-teal) 0%, var(--ch-teal-mid) 42%, var(--ch-teal-soft) 100%);
    color: #fff;
    box-shadow: 0 18px 50px rgba(43, 72, 101, 0.35);
}

.collab-hub .collab-hero::before {
    content: '';
    position: absolute;
    inset: -40%;
    background:
        radial-gradient(circle at 20% 30%, rgba(225, 152, 100, 0.35) 0%, transparent 45%),
        radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.12) 0%, transparent 40%),
        radial-gradient(circle at 60% 20%, rgba(84, 140, 168, 0.4) 0%, transparent 35%);
    pointer-events: none;
}

.collab-hub .collab-hero__inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1.25rem;
}

.collab-hub .collab-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    opacity: 0.92;
    margin-bottom: 0.65rem;
}

.collab-hub .collab-hero h1 {
    margin: 0 0 0.45rem 0;
    font-size: clamp(1.65rem, 3vw, 2.25rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.15;
}

.collab-hub .collab-hero p {
    margin: 0;
    max-width: 520px;
    opacity: 0.92;
    font-size: 1rem;
    line-height: 1.55;
}

.collab-hub .collab-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
    align-items: center;
}

.collab-hub .collab-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.72rem 1.35rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, var(--ch-coral) 0%, #d88952 100%);
    color: #fff !important;
    box-shadow: 0 10px 28px rgba(225, 152, 100, 0.45);
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
}

.collab-hub .collab-btn-primary:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
    box-shadow: 0 14px 34px rgba(225, 152, 100, 0.5);
}

.collab-hub .collab-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.72rem 1.2rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.14);
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.35);
    backdrop-filter: blur(8px);
    transition: background 0.2s ease, transform 0.2s ease;
}

.collab-hub .collab-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.24);
    transform: translateY(-1px);
}

.collab-hub .collab-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 1.1rem;
    margin-bottom: 1.65rem;
    padding: 1.15rem 1.35rem;
    border-radius: 16px;
    background: var(--ch-glass);
    border: 1px solid var(--ch-glass-border);
    backdrop-filter: blur(14px);
    box-shadow: 0 8px 32px rgba(15, 23, 42, 0.06);
}

.collab-hub .collab-toolbar label {
    display: block;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--ch-muted);
    margin-bottom: 0.35rem;
}

.collab-hub .collab-search-row {
    display: flex;
    align-items: stretch;
    flex: 1 1 320px;
    min-width: 0;
}

.collab-hub .collab-search-row input[type="text"] {
    flex: 1 1 auto;
    min-width: 0;
    padding: 0.72rem 1rem;
    border: 1px solid var(--ch-line);
    border-radius: 12px 0 0 12px;
    border-right: 0;
    font-size: 0.95rem;
    background: var(--collab-toolbar-input-bg);
}

.collab-hub .collab-search-row button[type="submit"] {
    flex: 0 0 auto;
    padding: 0 1.25rem;
    border: 1px solid var(--ch-teal);
    border-radius: 0 12px 12px 0;
    background: linear-gradient(135deg, var(--ch-teal), var(--ch-teal-mid));
    color: #fff;
    cursor: pointer;
    font-size: 1.1rem;
    transition: filter 0.2s ease, box-shadow 0.2s ease;
}

.collab-hub .collab-search-row button[type="submit"]:hover {
    filter: brightness(1.06);
    box-shadow: 0 8px 22px rgba(43, 72, 101, 0.28);
}

.collab-hub .collab-toolbar select {
    min-width: 210px;
    padding: 0.72rem 0.85rem;
    border-radius: 12px;
    border: 1px solid var(--ch-line);
    font-size: 0.9rem;
    font-weight: 600;
    background: var(--collab-toolbar-input-bg);
    color: var(--ch-slate);
}

.collab-hub .collab-section-label {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin: 0 0 1rem 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--ch-slate);
    letter-spacing: -0.02em;
}

.collab-hub .collab-section-label span.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ch-coral), var(--ch-teal-soft));
    box-shadow: 0 0 0 4px rgba(225, 152, 100, 0.25);
}

.collab-hub .collab-disc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.collab-hub .collab-disc-card {
    position: relative;
    border-radius: 18px;
    padding: 1.35rem 1.35rem 1.2rem;
    background: var(--collab-disc-bg);
    border: 1px solid var(--collab-disc-border);
    box-shadow: var(--collab-disc-shadow);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, filter 0.25s ease, opacity 0.25s ease;
}

.collab-hub .collab-disc-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--ch-teal-soft), var(--ch-coral));
    opacity: 0.85;
    transform: scaleX(0.35);
    transform-origin: left;
    transition: transform 0.35s ease;
}

.collab-hub .collab-disc-card:hover {
    transform: translateY(-6px);
    border-color: rgba(84, 140, 168, 0.45);
    box-shadow: 0 22px 48px rgba(43, 72, 101, 0.14);
}

.collab-hub .collab-disc-card:hover::before {
    transform: scaleX(1);
}

.collab-hub .collab-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.collab-hub .collab-pill--group {
    background: var(--collab-pill-group-bg);
    color: var(--ch-teal);
    border: 1px solid rgba(84, 140, 168, 0.28);
}

.collab-hub .collab-disc-card__title {
    margin: 0.85rem 0 0.45rem;
    font-size: 1.12rem;
    font-weight: 800;
    color: var(--ch-slate);
    letter-spacing: -0.02em;
    line-height: 1.3;
}

.collab-hub .collab-line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.collab-hub .collab-line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.collab-hub .collab-disc-card__excerpt {
    margin: 0 0 1.1rem;
    color: var(--ch-muted);
    font-size: 0.92rem;
    line-height: 1.55;
    min-height: 3.6rem;
}

.collab-hub .collab-card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.collab-hub .collab-chip-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.85rem;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid transparent;
    transition: transform 0.15s ease, box-shadow 0.2s ease;
}

.collab-hub .collab-chip-btn:hover {
    transform: translateY(-2px);
}

.collab-hub .collab-chip-btn--live {
    background: linear-gradient(135deg, var(--ch-teal) 0%, var(--ch-teal-mid) 100%);
    color: #fff !important;
    box-shadow: 0 6px 18px rgba(43, 72, 101, 0.28);
}

.collab-hub .collab-chip-btn--muted {
    background: #f8fafc;
    color: var(--ch-teal) !important;
    border-color: #e2e8f0;
}

.collab-hub .collab-chip-btn--danger {
    background: #fff1f2;
    color: #be123c !important;
    border-color: #fecdd3;
}

.collab-hub .collab-msg-btn-icon-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.collab-hub .collab-msg-unread-badge,
.student-events-page .student-unread-dot {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.05rem;
    height: 1.05rem;
    padding: 0 0.3rem;
    border-radius: 999px;
    font-size: 0.65rem;
    font-weight: 800;
    line-height: 1;
    color: #fff;
    background: #ef4444;
    box-shadow: 0 2px 10px rgba(239, 68, 68, 0.45);
}

.collab-hub .collab-msg-unread-badge {
    position: absolute;
    top: -0.42rem;
    right: -0.52rem;
}

.student-events-page .student-space-sidebar .admin-side-icon {
    position: relative;
    overflow: visible;
}

.student-events-page .student-unread-dot {
    position: absolute;
    top: -0.34rem;
    right: -0.5rem;
}

.collab-hub .collab-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    border-radius: 18px;
    background: var(--collab-empty-bg);
    border: 2px dashed var(--ch-line);
    color: var(--ch-muted);
}

.collab-hub .collab-empty-icon {
    font-size: 3rem;
    line-height: 1;
    margin-bottom: 0.75rem;
    opacity: 0.35;
}

.collab-hub .collab-empty h3 {
    margin: 0 0 0.35rem;
    color: var(--ch-slate);
    font-weight: 800;
}

/* ---- Groups index cards ---- */
.collab-hub .collab-group-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 1.35rem;
}

.collab-hub .collab-group-card {
    position: relative;
    border-radius: 20px;
    overflow: visible;
    background: var(--collab-group-bg);
    border: 1px solid var(--collab-group-border);
    box-shadow: var(--collab-group-shadow);
    transition: transform 0.28s ease, box-shadow 0.28s ease, filter 0.28s ease, opacity 0.28s ease;
}

.collab-hub .collab-group-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 56px rgba(43, 72, 101, 0.15);
}

.collab-hub .collab-group-card__media {
    position: relative;
    height: 148px;
    border-radius: 20px 20px 0 0;
    overflow: hidden;
    background: linear-gradient(145deg, #e9f1fa 0%, #dbeafe 40%, #fef3c7 100%);
}

.collab-hub .collab-group-card__ph {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: rgba(43, 72, 101, 0.28);
    z-index: 0;
}

.collab-hub .collab-group-card__pending-tag {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 3;
    padding: 0.35rem 0.65rem;
    border-radius: 10px;
    font-size: 0.62rem;
    font-weight: 900;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.92);
    color: #c2410c;
    border: 1px solid rgba(251, 146, 60, 0.55);
    box-shadow: 0 8px 22px rgba(234, 88, 12, 0.22);
}

.collab-hub .collab-group-card__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.collab-hub .collab-group-card__overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: linear-gradient(to top, rgba(15, 23, 42, 0.72) 0%, transparent 55%);
    opacity: 0.92;
    pointer-events: none;
}

.collab-hub .collab-group-card__floating-title {
    position: absolute;
    left: 1rem;
    right: 1rem;
    bottom: 0.85rem;
    z-index: 2;
    margin: 0;
    font-size: 1.08rem;
    font-weight: 800;
    color: #fff;
    text-shadow: 0 2px 16px rgba(0, 0, 0, 0.35);
    letter-spacing: -0.02em;
}

.collab-hub .collab-group-card__ribbon {
    position: absolute;
    top: 12px;
    right: -36px;
    z-index: 2;
    transform: rotate(42deg);
    padding: 0.35rem 2.5rem;
    font-size: 0.65rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: linear-gradient(135deg, #fbbf24, #f97316);
    color: #fff;
    box-shadow: 0 6px 18px rgba(249, 115, 22, 0.45);
}

.collab-hub .collab-group-card__body {
    padding: 1.15rem 1.25rem 1.35rem;
    border-radius: 0 0 19px 19px;
    background: var(--collab-group-body-bg);
}

.collab-hub .collab-group-card__body h4 {
    margin: 0 0 0.45rem;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--ch-slate);
}

.collab-hub .collab-group-card__body p {
    margin: 0 0 1rem;
    font-size: 0.9rem;
    color: var(--ch-muted);
    line-height: 1.5;
}

.collab-hub .collab-pending-block {
    margin-bottom: 2rem;
}

.collab-hub .collab-pending-block .collab-group-card {
    border-color: rgba(251, 191, 36, 0.45);
    box-shadow: 0 14px 44px rgba(245, 158, 11, 0.18);
}

/* Approved groups: soft blended green backlight */
.collab-hub .approved-group-grid .collab-group-card::after {
    content: '';
    position: absolute;
    left: 10%;
    right: 10%;
    bottom: -16px;
    height: 44px;
    border-radius: 999px;
    pointer-events: none;
    background: radial-gradient(ellipse at center, rgba(74, 222, 128, 0.28) 0%, rgba(34, 197, 94, 0.14) 45%, rgba(16, 185, 129, 0) 78%);
    filter: blur(8px);
    opacity: 0.9;
    transition: opacity 0.25s ease, filter 0.25s ease, transform 0.25s ease;
    z-index: -1;
}

.collab-hub .approved-group-grid .collab-group-card:hover::after {
    opacity: 1;
    filter: blur(10px);
    transform: scale(1.03);
}

.collab-hub .approved-group-grid .collab-group-card {
    background: var(--collab-group-approved-bg);
    border-color: rgba(134, 239, 172, 0.65);
    box-shadow: 0 16px 44px rgba(34, 197, 94, 0.18), 0 10px 30px rgba(15, 23, 42, 0.08);
}

.collab-hub .approved-group-grid .collab-group-card .collab-group-card__body {
    background: var(--collab-group-approved-body-bg);
}

/* Focus-on-hover: blur/dim sibling cards in groups and discussions */
@media (hover: hover) and (pointer: fine) {
    .collab-hub .collab-group-grid:hover .collab-group-card:not(:hover),
    .collab-hub .collab-disc-grid:hover .collab-disc-card:not(:hover) {
        filter: blur(2.5px) saturate(0.85);
        opacity: 0.45;
        transform: scale(0.985);
    }

    .collab-hub .collab-group-grid:hover .collab-group-card:hover,
    .collab-hub .collab-disc-grid:hover .collab-disc-card:hover {
        z-index: 2;
    }
}

/* ---- Group detail ---- */
.collab-hub.collab-hub--detail .collab-detail-hero {
    display: grid;
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}

@media (min-width: 880px) {
    .collab-hub.collab-hub--detail .collab-detail-hero {
        grid-template-columns: 1.15fr 0.85fr;
        align-items: stretch;
    }
}

.collab-hub .collab-detail-banner {
    position: relative;
    border-radius: 22px;
    min-height: 220px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--ch-teal) 0%, var(--ch-teal-mid) 50%, var(--ch-teal-soft) 100%);
    box-shadow: 0 18px 48px rgba(43, 72, 101, 0.28);
}

.collab-hub .collab-detail-banner img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.55;
    mix-blend-mode: luminosity;
}

.collab-hub .collab-detail-banner__inner {
    position: relative;
    z-index: 1;
    padding: 2rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    color: #fff;
}

.collab-hub .collab-detail-banner__inner h1 {
    margin: 0 0 0.5rem;
    font-size: clamp(1.6rem, 2.8vw, 2.1rem);
    font-weight: 800;
    letter-spacing: -0.03em;
}

.collab-hub .collab-detail-banner__inner p {
    margin: 0;
    opacity: 0.92;
    max-width: 540px;
    line-height: 1.55;
}

.collab-hub .collab-detail-sidecard {
    border-radius: 22px;
    padding: 1.35rem 1.35rem 1.5rem;
    background: var(--ch-glass);
    border: 1px solid var(--ch-glass-border);
    backdrop-filter: blur(16px);
    box-shadow: 0 14px 44px rgba(15, 23, 42, 0.08);
}

.collab-hub .collab-detail-sidecard h3 {
    margin: 0 0 1rem;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ch-muted);
}

.collab-hub .collab-member-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
}

.collab-hub .collab-member-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.45rem 0.75rem 0.45rem 0.45rem;
    border-radius: 999px;
    background: var(--collab-member-chip-bg);
    border: 1px solid var(--ch-line);
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--ch-slate);
}

.collab-hub .collab-member-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ch-teal-soft), var(--ch-coral));
    color: #fff;
    font-size: 0.72rem;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
}

.collab-hub .collab-role-tag {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--ch-teal-soft);
}

.collab-hub .collab-thread-panel {
    margin-top: 0.5rem;
    padding: 1.5rem 1.35rem 1.65rem;
    border-radius: 22px;
    background: var(--collab-panel-bg);
    border: 1px solid var(--collab-panel-border);
    box-shadow: var(--collab-panel-shadow);
}

.collab-hub .collab-thread-panel > h3 {
    margin: 0 0 1.1rem;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--ch-slate);
}

.collab-hub .collab-compose {
    padding: 1.15rem;
    margin-bottom: 1.35rem;
    border-radius: 16px;
    background: var(--collab-compose-bg);
    border: 1px dashed rgba(84, 140, 168, 0.45);
}

.collab-hub .collab-compose .form-group {
    margin-bottom: 0.85rem;
}

.collab-hub .collab-compose input[type="text"],
.collab-hub .collab-compose textarea {
    width: 100%;
    border-radius: 12px;
    border: 1px solid var(--ch-line);
    padding: 0.65rem 0.85rem;
    font-family: inherit;
    font-size: 0.92rem;
}

.collab-hub .collab-compose textarea {
    min-height: 88px;
    resize: vertical;
}

.collab-hub .collab-thread-card {
    position: relative;
    padding: 1.15rem 1.15rem 1.15rem 1.35rem;
    margin-bottom: 0.85rem;
    border-radius: 16px;
    background: var(--collab-thread-card-bg);
    border: 1px solid var(--collab-thread-card-border);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.collab-hub .collab-thread-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 12px;
    bottom: 12px;
    width: 4px;
    border-radius: 4px;
    background: linear-gradient(180deg, var(--ch-teal-soft), var(--ch-coral));
}

.collab-hub .collab-thread-card:hover {
    border-color: rgba(84, 140, 168, 0.35);
    box-shadow: 0 10px 28px rgba(43, 72, 101, 0.08);
}

.collab-hub .collab-thread-card__title {
    margin: 0 0 0.35rem;
    font-size: 1.02rem;
    font-weight: 800;
    color: var(--ch-slate);
}

.collab-hub .collab-thread-meta {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--ch-muted);
    margin-bottom: 0.65rem;
}

.collab-hub .collab-thread-card p {
    margin: 0 0 0.85rem;
    font-size: 0.9rem;
    color: var(--ch-muted);
    line-height: 1.55;
}

/* ---- Group feed (wall) — composer, cards, engagement ---- */
.collab-hub .collab-feed-panel.collab-thread-panel {
    background: var(--collab-feed-panel-bg);
    border: 1px solid var(--collab-panel-border);
    box-shadow: var(--collab-panel-shadow);
}

.collab-hub .collab-feed-panel__head {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1.35rem;
}

.collab-hub .collab-feed-panel__head-icon {
    flex-shrink: 0;
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--collab-feed-panel-head-icon-color);
    background: linear-gradient(135deg, #e0f2fe, #fef3c7);
    border: 1px solid rgba(56, 189, 248, 0.25);
    box-shadow: 0 4px 14px rgba(56, 189, 248, 0.12);
}

.collab-hub .collab-feed-panel__title {
    margin: 0 0 0.35rem;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--ch-slate);
    letter-spacing: -0.02em;
}

.collab-hub .collab-feed-panel__subtitle {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.55;
    color: var(--ch-muted);
    max-width: 48rem;
}

.collab-hub .collab-feed-composer-card {
    margin-bottom: 1.35rem;
    padding: 1rem 1.1rem 1.05rem;
    border-radius: 18px;
    background: var(--collab-feed-composer-bg);
    border: 1px solid var(--collab-panel-border);
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.06);
}

.collab-hub .collab-feed-composer__head {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}

.collab-hub .collab-feed-composer__avatar {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 800;
    color: var(--collab-feed-avatar-text);
    background: linear-gradient(135deg, #bae6fd, #fed7aa);
    border: 2px solid #fff;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.08);
}

.collab-hub .collab-feed-composer__kicker {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ch-muted);
}

.collab-hub .collab-feed-composer__as {
    font-size: 0.86rem;
    color: var(--ch-slate);
    margin-top: 0.1rem;
}

.collab-hub .collab-feed-composer__alert {
    margin-bottom: 0.75rem;
}

.collab-hub .collab-feed-composer__textarea {
    width: 100%;
    min-height: 88px;
    resize: vertical;
    border-radius: 14px;
    border: 1px solid var(--ch-line);
    padding: 0.75rem 0.85rem;
    font-size: 0.95rem;
    line-height: 1.5;
    color: var(--ch-slate);
    background: var(--collab-feed-composer-textarea-bg);
    transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
}

.collab-hub .collab-feed-composer__textarea:focus {
    outline: none;
    border-color: rgba(56, 189, 248, 0.55);
    background: var(--collab-feed-composer-textarea-focus-bg);
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
}

.collab-hub .collab-feed-composer__toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--collab-feed-composer-toolbar-border);
}

.collab-hub .collab-feed-composer__attach {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 0.75rem;
    border-radius: 999px;
    border: 1px dashed rgba(100, 116, 139, 0.45);
    background: var(--collab-feed-composer-attach-bg);
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--ch-slate);
    cursor: pointer;
    overflow: hidden;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.collab-hub .collab-feed-composer__attach:hover {
    border-color: rgba(56, 189, 248, 0.55);
    background: var(--collab-feed-composer-attach-hover-bg);
}

.collab-hub .collab-feed-composer__attach input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    font-size: 0;
}

.collab-hub .collab-feed-composer__attach-icon {
    font-size: 1.05rem;
    color: var(--ch-teal);
}

.collab-hub .collab-feed-composer__toolbar-hint {
    flex: 1 1 160px;
    font-size: 0.76rem;
    color: var(--ch-muted);
    font-weight: 600;
}

.collab-hub .collab-feed-composer__submit {
    margin-left: auto;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: 0;
    border-radius: 999px;
    padding: 0.5rem 1.15rem;
    font-size: 0.88rem;
    font-weight: 800;
    color: #fff;
    cursor: pointer;
    background: linear-gradient(135deg, #f97316, #ea580c);
    box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
    transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.collab-hub .collab-feed-composer__submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(234, 88, 12, 0.42);
}

.collab-hub a.collab-feed-composer__submit {
    text-decoration: none;
}

.collab-hub .collab-feed-join-card {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 1.35rem;
    padding: 1.1rem 1.15rem;
    border-radius: 18px;
    background: var(--collab-feed-join-bg);
    border: 1px solid var(--collab-feed-join-border);
    box-shadow: var(--collab-feed-join-shadow);
}

.collab-hub .collab-feed-join-card__icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--ch-teal);
    background: var(--collab-feed-join-icon-bg);
    border: 1px solid var(--collab-feed-join-icon-border);
}

.collab-hub .collab-feed-join-card__text {
    margin: 0;
    flex: 1 1 200px;
    font-size: 0.9rem;
    color: var(--ch-muted);
    line-height: 1.5;
}

.collab-hub .collab-feed-join-card__btn {
    margin-left: auto;
}

.collab-hub .collab-feed-post {
    position: relative;
    margin-bottom: 1rem;
    padding: 1rem 1.1rem 1.05rem;
    border-radius: 18px;
    background: var(--collab-feed-post-bg);
    border: 1px solid var(--collab-panel-border);
    box-shadow: 0 8px 26px rgba(15, 23, 42, 0.05);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.collab-hub .collab-feed-post:last-of-type {
    margin-bottom: 0;
}

.collab-hub .collab-feed-post:hover {
    border-color: rgba(84, 140, 168, 0.28);
    box-shadow: 0 12px 34px rgba(43, 72, 101, 0.08);
}

.collab-hub .collab-feed-post__hd {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.65rem;
}

.collab-hub .collab-feed-post__hd-main {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.collab-hub .collab-feed-post__avatar {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 800;
    color: var(--collab-feed-avatar-text);
    background: linear-gradient(135deg, #e0f2fe, #cffafe);
    border: 1px solid rgba(56, 189, 248, 0.35);
}

.collab-hub .collab-feed-post__who {
    min-width: 0;
}

.collab-hub .collab-feed-post__name {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--ch-slate);
    line-height: 1.25;
}

.collab-hub .collab-feed-post__time {
    display: block;
    margin-top: 0.15rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--ch-muted);
}

.collab-hub .collab-feed-post__delete {
    flex-shrink: 0;
    width: 2.1rem;
    height: 2.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: var(--ch-muted);
    border: 1px solid transparent;
    transition: color 0.12s ease, background 0.12s ease, border-color 0.12s ease;
}

.collab-hub .collab-feed-post__delete:hover {
    color: #b91c1c;
    background: #fef2f2;
    border-color: rgba(239, 68, 68, 0.25);
}

.collab-hub .collab-feed-post__body {
    margin: 0;
    font-size: 0.95rem;
    color: var(--ch-slate);
    line-height: 1.55;
    word-break: break-word;
}

.collab-hub .collab-feed-post__media {
    margin-top: 0.75rem;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #f1f5f9;
}

.collab-hub .collab-feed-post__media img,
.collab-hub .collab-feed-post__media video {
    display: block;
    width: 100%;
    max-height: 420px;
    object-fit: contain;
    vertical-align: middle;
}

.collab-hub .collab-feed-post__media audio {
    display: block;
    width: 100%;
    margin: 0.5rem;
    max-width: calc(100% - 1rem);
}

.collab-hub .collab-feed-reactions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.9rem;
    padding-top: 0.7rem;
    border-top: 1px solid #eef2f6;
}

.collab-hub .collab-feed-reactions__label {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--ch-muted);
    margin-right: 0.2rem;
}

.collab-hub .collab-feed-reaction-form {
    display: inline;
    margin: 0;
}

.collab-hub .collab-feed-reaction-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 999px;
    padding: 0.25rem 0.6rem;
    font-size: 1rem;
    cursor: pointer;
    line-height: 1.2;
    transition: background 0.15s ease, border-color 0.15s ease, transform 0.1s ease;
}

.collab-hub .collab-feed-reaction-btn:hover {
    border-color: rgba(56, 189, 248, 0.5);
    background: #f0f9ff;
    transform: translateY(-1px);
}

.collab-hub .collab-feed-reaction-btn.is-active {
    border-color: rgba(249, 115, 22, 0.55);
    background: #fff7ed;
    box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.2);
}

.collab-hub .collab-feed-reaction-count {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--ch-muted);
}

.collab-hub .collab-feed-comments {
    margin-top: 0.8rem;
    padding: 0.55rem 0.75rem 0.65rem;
    background: linear-gradient(180deg, #f8fafc, #f1f5f9);
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

.collab-hub .collab-feed-comment {
    font-size: 0.87rem;
    color: var(--ch-slate);
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(226, 232, 240, 0.95);
}

.collab-hub .collab-feed-comment:last-child {
    border-bottom: 0;
}

.collab-hub .collab-feed-comment__meta {
    font-size: 0.7rem;
    color: var(--ch-muted);
    margin-left: 0.35rem;
    font-weight: 600;
}

.collab-hub .collab-feed-comment-form {
    margin-top: 0.7rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.collab-hub .collab-feed-comment-form textarea {
    width: 100%;
    min-height: 56px;
    border-radius: 12px;
    border: 1px solid var(--ch-line);
    padding: 0.55rem 0.7rem;
    font-size: 0.9rem;
    background: #fff;
}

.collab-hub .collab-feed-share {
    margin-top: 0.8rem;
    padding: 0.65rem 0.75rem;
    border-radius: 14px;
    background: rgba(255, 247, 237, 0.65);
    border: 1px solid rgba(253, 186, 116, 0.45);
}

.collab-hub .collab-feed-share__label {
    display: block;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #9a3412;
    margin-bottom: 0.5rem;
}

.collab-hub .collab-feed-share-form {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.collab-hub .collab-feed-share-form select {
    flex: 1 1 200px;
    min-width: 160px;
    border-radius: 10px;
    border: 1px solid rgba(251, 146, 60, 0.45);
    padding: 0.45rem 0.55rem;
    font-size: 0.88rem;
    background: #fff;
}

.collab-hub .collab-feed-share__empty {
    margin: 0;
    font-size: 0.84rem;
    color: #9a3412;
    opacity: 0.9;
}

.collab-hub .collab-feed-shares-log {
    margin-top: 0.65rem;
    padding: 0.5rem 0.65rem;
    font-size: 0.8rem;
    color: var(--ch-muted);
    border-radius: 12px;
    background: #f8fafc;
    border: 1px dashed #e2e8f0;
}

.collab-hub .collab-feed-shares-log ul {
    margin: 0.3rem 0 0;
    padding-left: 1.1rem;
}

.collab-hub .collab-feed-shares-log__title {
    font-weight: 800;
    color: var(--ch-slate);
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.collab-hub .collab-feed-shares-log__time {
    opacity: 0.75;
    margin-left: 0.35rem;
}

.collab-hub .collab-feed-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.65rem;
    padding: 1.75rem 1.25rem;
    margin-bottom: 0.35rem;
    border-radius: 18px;
    background: #fff;
    border: 1px dashed rgba(148, 163, 184, 0.55);
}

.collab-hub .collab-feed-empty__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: var(--ch-muted);
    background: #f1f5f9;
}

.collab-hub .collab-feed-empty__text {
    margin: 0;
    max-width: 26rem;
    font-size: 0.92rem;
    color: var(--ch-muted);
    line-height: 1.55;
}

.collab-hub .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* ---- Forms (create / edit discussion) ---- */
.collab-hub .collab-form-shell {
    max-width: 720px;
    margin-top: 0.5rem;
    padding: 1.65rem 1.65rem 1.85rem;
    border-radius: 22px;
    background: var(--collab-form-shell-bg);
    border: 1px solid var(--collab-form-shell-border);
    box-shadow: 0 16px 48px rgba(15, 23, 42, 0.07);
}

.collab-hub .collab-form-shell .form-group label {
    font-weight: 700;
    font-size: 0.82rem;
    color: var(--ch-teal);
    letter-spacing: 0.02em;
}

.collab-hub .collab-form-shell select,
.collab-hub .collab-form-shell input[type="text"],
.collab-hub .collab-form-shell textarea {
    border-radius: 12px !important;
    border: 1px solid var(--ch-line) !important;
    padding: 0.7rem 0.95rem !important;
}

.collab-hub .collab-alert-soft {
    padding: 1rem 1.15rem;
    border-radius: 14px;
    margin-bottom: 1.15rem;
    background: linear-gradient(135deg, #fffbeb 0%, #fff7ed 100%);
    border: 1px solid rgba(251, 191, 36, 0.45);
    color: #92400e;
    font-size: 0.92rem;
    line-height: 1.5;
}

/* Live chat + sidebar: surfaces follow body.dark-mode */
.collab-chat-root {
    --msg-shell-bg: rgba(255, 255, 255, 0.96);
    --msg-shell-border: rgba(226, 232, 240, 0.95);
    --msg-main-bg: #ffffff;
    --msg-head-bg: linear-gradient(135deg, rgba(43, 72, 101, 0.97) 0%, rgba(53, 92, 125, 0.96) 100%);
    --msg-head-border: rgba(255, 255, 255, 0.12);
    --msg-stream-bg: transparent;
    --msg-composer-bg: rgba(255, 255, 255, 0.92);
    --msg-composer-border: rgba(148, 163, 184, 0.35);
    --msg-input-bg: #ffffff;
    --msg-input-color: #0f172a;
    --msg-input-border: rgba(148, 163, 184, 0.45);
    --msg-attach-bg: #ffffff;
    --msg-attach-border: rgba(148, 163, 184, 0.35);
    --msg-attach-color: var(--ch-teal);
    --msg-author-messenger-other: var(--ch-teal);
    --msg-author-messenger-self: #1d4ed8;
    --msg-meta-messenger: var(--ch-muted);
    --msg-details-active-border: rgba(225, 152, 100, 0.9);
    --msg-details-active-bg: rgba(225, 152, 100, 0.22);
    --msg-details-active-shadow: 0 0 0 1px rgba(225, 152, 100, 0.45);

    --msg-sidebar-bg: #f1f5f9;
    --msg-sidebar-border: rgba(148, 163, 184, 0.35);
    --msg-sidebar-done-bg: rgba(43, 72, 101, 0.1);
    --msg-sidebar-done-color: var(--ch-teal);
    --msg-sidebar-done-hover: rgba(43, 72, 101, 0.16);
    --msg-sidebar-profile-border: rgba(148, 163, 184, 0.35);
    --msg-sidebar-title: var(--ch-slate);
    --msg-sidebar-sub: var(--ch-muted);
    --msg-scrollbar: rgba(15, 23, 42, 0.22) transparent;

    --msg-acc-bg: rgba(255, 255, 255, 0.95);
    --msg-acc-border: rgba(148, 163, 184, 0.35);
    --msg-acc-summary: var(--ch-slate);
    --msg-acc-body: #475569;

    --msg-media-card-bg: #ffffff;
    --msg-media-card-border: rgba(148, 163, 184, 0.4);
    --msg-media-preview-bg: #f1f5f9;
    --msg-media-meta: var(--ch-slate);
    --msg-media-time: var(--ch-muted);
    --msg-media-link: #2563eb;
    --msg-hint: var(--ch-muted);

    --ch-tile-bg: #f1f5f9;
    --ch-tile-label: var(--ch-slate);
    --ch-tile-icon: var(--ch-muted);
    --ch-tile-hover-border: rgba(148, 163, 184, 0.55);
    --ch-tile-selected-border: #548ca8;
    --ch-tile-selected-shadow: 0 0 0 1px rgba(84, 140, 168, 0.45);
    --ch-tile-check-bg: #548ca8;
    --ch-tile-check-color: #ffffff;
    --ch-adv-bg: rgba(241, 245, 249, 0.95);
    --ch-adv-border: rgba(148, 163, 184, 0.35);
    --ch-adv-summary: var(--ch-muted);
    --ch-theme-input-bg: #fff;
    --ch-theme-input-border: rgba(148, 163, 184, 0.45);
    --ch-theme-input-color: var(--ch-slate);
    --ch-theme-check: var(--ch-slate);
    --ch-btn-primary-bg: linear-gradient(135deg, var(--ch-teal), var(--ch-teal-mid));
    --ch-btn-primary-color: #fff;
    --ch-btn-primary-hover-filter: brightness(1.08);
    --ch-btn-ghost-color: var(--ch-muted);
    --ch-btn-ghost-border: rgba(148, 163, 184, 0.45);
    --ch-btn-ghost-hover: rgba(148, 163, 184, 0.12);
    --ch-theme-status: var(--ch-muted);
    --msg-chat-layout-bg: rgba(255, 255, 255, 0.96);
    --msg-chat-layout-border: rgba(226, 232, 240, 0.95);
    --msg-chat-layout-shadow: 0 20px 56px rgba(15, 23, 42, 0.12);
}

/* Chat page uses CSS grid for .admin-main; min-height 0 lets nested messenger flex + scroll work */
.student-events-page.collab-chat-root .admin-main {
    min-height: 0;
}

/* ---- Live chat ---- */
.collab-chat-root .collab-chat-layout {
    position: relative;
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid var(--msg-chat-layout-border);
    box-shadow: var(--msg-chat-layout-shadow);
    background: var(--msg-chat-layout-bg);
}

.collab-chat-root .collab-chat-head {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 0.85rem 1.1rem;
    padding: 1.15rem 1.35rem;
    background: linear-gradient(135deg, rgba(43, 72, 101, 0.97) 0%, rgba(53, 92, 125, 0.96) 100%);
    color: #fff;
}

.collab-chat-root .collab-chat-head__main {
    min-width: 0;
    text-align: center;
}

.collab-chat-root .collab-chat-head__main h2,
.collab-chat-root .collab-chat-head__main .collab-chat-sub {
    text-align: center;
}

.collab-chat-root .collab-chat-head__back,
.collab-chat-root .collab-chat-head__tray,
.collab-chat-root .collab-chat-head__details {
    flex-shrink: 0;
    white-space: nowrap;
}

.collab-chat-root .collab-chat-head__tray {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    justify-content: flex-end;
    align-items: center;
}

.collab-chat-root .collab-chat-head__back.collab-btn-ghost,
.collab-chat-root .collab-chat-head__details.collab-btn-ghost,
.collab-chat-root .collab-chat-head__ai-sum.collab-btn-ghost {
    border-color: rgba(255, 255, 255, 0.35);
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
}

.collab-chat-root .collab-chat-head__ai-sum.collab-btn-ghost {
    border: 1px solid rgba(255, 255, 255, 0.35);
    border-radius: 12px;
    padding: 0.45rem 0.65rem;
    font-weight: 700;
    font-size: 0.78rem;
    cursor: pointer;
}

.collab-chat-root .collab-chat-head__ai-sum.collab-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.16);
}

.collab-chat-root .collab-chat-head__ai-sum.is-loading {
    opacity: 0.65;
    pointer-events: none;
}

.collab-chat-root .collab-chat-head__details {
    border: 1px solid rgba(255, 255, 255, 0.35);
    border-radius: 12px;
    padding: 0.5rem 0.85rem;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
}

.collab-chat-root .collab-chat-head__details:hover {
    background: rgba(255, 255, 255, 0.16);
}

.collab-chat-root .collab-chat-head h2 {
    margin: 0 0 0.25rem;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -0.02em;
}

.collab-chat-root .collab-chat-live {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    opacity: 0.95;
}

.collab-chat-root .collab-chat-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 4px rgba(74, 222, 128, 0.35);
    animation: collabPulse 2s ease-in-out infinite;
}

@keyframes collabPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.65; transform: scale(0.92); }
}

.collab-chat-root .collab-chat-sub {
    margin: 0;
    font-size: 0.88rem;
    opacity: 0.88;
    line-height: 1.45;
}

.collab-chat-root .collab-chat-stream {
    height: min(52vh, 480px);
    min-height: 320px;
    overflow-y: auto;
    padding: 1.25rem;
    background-color: #f1f5f9;
    background-image:
        radial-gradient(rgba(148, 163, 184, 0.22) 1px, transparent 1px);
    background-size: 18px 18px;
}

.collab-chat-root .chat-row {
    margin-bottom: 0.85rem;
    display: flex;
}

.collab-chat-root .chat-row.self {
    justify-content: flex-end;
}

/* Appolios AI: compact trigger under summarized thread (summary in SweetAlert) */
.collab-chat-root .chat-ai-sum-wrap {
    display: flex;
    justify-content: flex-start;
    margin: -0.35rem 0 0.85rem 2.75rem;
}

.collab-chat-root .chat-ai-sum-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid rgba(84, 140, 168, 0.45);
    background: rgba(255, 255, 255, 0.92);
    color: var(--ch-teal);
    box-shadow: 0 2px 10px rgba(43, 72, 101, 0.08);
    transition: background 0.15s ease, border-color 0.15s ease, transform 0.12s ease;
}

.collab-chat-root .chat-ai-sum-btn:hover {
    background: rgba(225, 152, 100, 0.15);
    border-color: rgba(225, 152, 100, 0.55);
    transform: translateY(-1px);
}

body.dark-mode .collab-chat-root .chat-ai-sum-btn {
    background: rgba(30, 41, 59, 0.92);
    color: #e2e8f0;
    border-color: rgba(148, 163, 184, 0.35);
}

body.dark-mode .collab-chat-root .chat-ai-sum-btn:hover {
    background: rgba(51, 65, 85, 0.88);
    border-color: rgba(225, 152, 100, 0.45);
}

/* Appolios AI summary popup (SweetAlert2 mounts on body) */
.swal2-popup .appolios-ai-swal-sub {
    margin: 0 0 0.75rem;
    font-size: 0.88rem;
    color: #64748b;
    text-align: left;
}

.swal2-popup .appolios-ai-swal-body {
    text-align: left;
    white-space: pre-wrap;
    word-break: break-word;
    font-size: 0.95rem;
    line-height: 1.55;
    color: #0f172a;
    max-height: min(52vh, 24rem);
    overflow-y: auto;
}

body.dark-mode .swal2-popup .appolios-ai-swal-sub {
    color: #94a3b8;
}

body.dark-mode .swal2-popup .appolios-ai-swal-body {
    color: #e2e8f0;
}

.collab-chat-root .collab-chat-msg-wrap {
    display: flex;
    align-items: flex-end;
    gap: 0.55rem;
    max-width: min(82%, 560px);
}

.collab-chat-root .chat-row.self .collab-chat-msg-wrap {
    flex-direction: row-reverse;
}

.collab-chat-root .collab-chat-avatar {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border-radius: 12px;
    background: linear-gradient(145deg, var(--ch-teal-soft), var(--ch-teal));
    color: #fff;
    font-size: 0.78rem;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 16px rgba(43, 72, 101, 0.28);
}

.collab-chat-root .chat-bubble {
    max-width: 100%;
    border-radius: 16px 16px 16px 6px;
    padding: 0.65rem 0.95rem;
    background: var(--chat-bubble-other-bg, rgba(255, 255, 255, 0.96));
    border: 1px solid var(--chat-bubble-other-border, rgba(226, 232, 240, 0.95));
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}

.collab-chat-root .chat-row.self .chat-bubble {
    border-radius: 16px 16px 6px 16px;
    background: var(--chat-bubble-self-bg, linear-gradient(145deg, #dbeafe 0%, #eff6ff 100%));
    border-color: var(--chat-bubble-self-border, rgba(59, 130, 246, 0.35));
}

.collab-chat-root .chat-text {
    color: var(--chat-msg-text, #0f172a);
    white-space: pre-wrap;
    word-break: break-word;
}

.collab-chat-root .chat-author {
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: var(--ch-teal);
    margin-bottom: 0.35rem;
}

.collab-chat-root .chat-row.self .chat-author {
    color: #1d4ed8;
}

.collab-chat-root .chat-meta {
    font-size: 0.68rem;
    color: var(--ch-muted);
    margin-top: 0.45rem;
}

.collab-chat-root .collab-chat-composer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    padding: 1rem 1.15rem;
    background: rgba(255, 255, 255, 0.92);
    border-top: 1px solid var(--ch-line);
    backdrop-filter: blur(12px);
}

.collab-chat-root .collab-chat-composer input[type="text"] {
    flex: 1 1 180px;
    min-width: 0;
    border: 1px solid var(--ch-line);
    border-radius: 14px;
    padding: 0.72rem 1rem;
    font-size: 0.95rem;
    background: #fff;
}

.collab-chat-root .chat-attach-btn {
    border: 1px solid var(--ch-line);
    border-radius: 12px;
    background: #fff;
    width: 42px;
    height: 42px;
}

.collab-chat-root .collab-chat-composer .btn-primary {
    border-radius: 12px;
    padding: 0.65rem 1.35rem;
    font-weight: 700;
}

/* Messenger-style split: main column + integrated right sidebar */
.collab-chat-root .collab-chat-layout--messenger {
    display: flex;
    flex-direction: column;
    /* Cap shell height so the message stream can scroll inside instead of growing the page */
    max-height: min(92vh, calc(100dvh - 5.5rem));
    min-height: min(52vh, 520px);
    background: var(--msg-shell-bg);
    border-color: var(--msg-shell-border);
}

.collab-chat-root .collab-chat-messenger {
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    flex-direction: row;
    align-items: stretch;
}

.collab-chat-root .collab-chat-main-column {
    flex: 1 1 auto;
    min-width: 0;
    min-height: 0;
    display: flex;
    flex-direction: column;
    background: var(--msg-main-bg);
}

.collab-chat-root .collab-chat-layout--messenger .collab-chat-head {
    flex-shrink: 0;
    background: var(--msg-head-bg);
    border-bottom: 1px solid var(--msg-head-border);
}

.collab-chat-root .collab-chat-head__details.is-active {
    border-color: var(--msg-details-active-border);
    background: var(--msg-details-active-bg);
    box-shadow: var(--msg-details-active-shadow);
}

.collab-chat-root .collab-chat-layout--messenger .collab-chat-stream {
    flex: 1 1 auto;
    min-height: 0;
    height: auto;
    max-height: none;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    /* Override base .collab-chat-stream fixed height so flex can assign remaining space */
    background-color: var(--msg-stream-bg);
}

.collab-chat-root .collab-chat-layout--messenger .collab-chat-composer {
    flex-shrink: 0;
    background: var(--msg-composer-bg);
    border-top: 1px solid var(--msg-composer-border);
}

.collab-chat-root .collab-chat-layout--messenger .collab-chat-composer input[type="text"] {
    background: var(--msg-input-bg);
    border-color: var(--msg-input-border);
    color: var(--msg-input-color);
}

.collab-chat-root .collab-chat-layout--messenger .chat-attach-btn {
    background: var(--msg-attach-bg);
    border-color: var(--msg-attach-border);
    color: var(--msg-attach-color);
}

.collab-chat-root .collab-chat-layout--messenger .chat-row:not(.self) .chat-bubble {
    background: var(--chat-bubble-other-bg, #ffffff);
    border-color: var(--chat-bubble-other-border, #e2e8f0);
}

.collab-chat-root .collab-chat-layout--messenger .chat-row.self .chat-bubble {
    background: var(--chat-bubble-self-bg, linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%));
    border-color: var(--chat-bubble-self-border, rgba(59, 130, 246, 0.45));
}

.collab-chat-root .collab-chat-layout--messenger .chat-author {
    color: var(--msg-author-messenger-other);
}

.collab-chat-root .collab-chat-layout--messenger .chat-row.self .chat-author {
    color: var(--msg-author-messenger-self);
}

.collab-chat-root .collab-chat-layout--messenger .chat-meta {
    color: var(--msg-meta-messenger);
}

.collab-chat-root .collab-chat-layout--messenger .collab-chat-sidebar {
    min-height: 0;
}

.collab-chat-root .collab-chat-sidebar {
    flex: 0 0 auto;
    width: 0;
    min-width: 0;
    overflow: hidden;
    background: var(--msg-sidebar-bg);
    border-left: 1px solid var(--msg-sidebar-border);
    display: flex;
    flex-direction: column;
    transition: width 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), min-width 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.collab-chat-root .collab-chat-layout--messenger.is-sidebar-open .collab-chat-sidebar {
    width: min(320px, 38vw);
    min-width: min(320px, 38vw);
}

.collab-chat-root .collab-chat-sidebar__top {
    display: flex;
    justify-content: flex-end;
    padding: 0.5rem 0.65rem 0;
    flex-shrink: 0;
}

.collab-chat-root .collab-chat-sidebar__done {
    border: 0;
    background: var(--msg-sidebar-done-bg);
    color: var(--msg-sidebar-done-color);
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.4rem 0.95rem;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.15s ease;
}

.collab-chat-root .collab-chat-sidebar__done:hover {
    background: var(--msg-sidebar-done-hover);
}

.collab-chat-root .collab-chat-sidebar__profile {
    text-align: center;
    padding: 0.25rem 1rem 0.85rem;
    border-bottom: 1px solid var(--msg-sidebar-profile-border);
    flex-shrink: 0;
}

.collab-chat-root .collab-chat-sidebar__avatar {
    width: 4.25rem;
    height: 4.25rem;
    margin: 0 auto 0.55rem;
    border-radius: 50%;
    background: linear-gradient(145deg, #5b21b6, #a78bfa);
    color: #fff;
    font-size: 1.5rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
}

.collab-chat-root .collab-chat-sidebar__room-title {
    margin: 0;
    font-size: 1.02rem;
    font-weight: 700;
    color: var(--msg-sidebar-title);
}

.collab-chat-root .collab-chat-sidebar__room-sub {
    margin: 0.2rem 0 0;
    font-size: 0.8rem;
    color: var(--msg-sidebar-sub);
}

.collab-chat-root .collab-chat-sidebar__scroll {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0.45rem 0.6rem 1rem;
    scrollbar-width: thin;
    scrollbar-color: var(--msg-scrollbar);
}

.collab-chat-root .msg-sidebar-acc {
    border-radius: 12px;
    margin-bottom: 0.4rem;
    background: var(--msg-acc-bg);
    border: 1px solid var(--msg-acc-border);
}

.collab-chat-root .msg-sidebar-acc > summary {
    list-style: none;
    padding: 0.65rem 0.8rem;
    font-size: 0.86rem;
    font-weight: 600;
    color: var(--msg-acc-summary);
    cursor: pointer;
    user-select: none;
}

.collab-chat-root .msg-sidebar-acc > summary::-webkit-details-marker {
    display: none;
}

.collab-chat-root .msg-sidebar-acc > summary::after {
    content: '›';
    float: right;
    font-size: 1.15rem;
    line-height: 1;
    opacity: 0.55;
    transition: transform 0.2s ease;
}

.collab-chat-root .msg-sidebar-acc[open] > summary::after {
    transform: rotate(90deg);
}

.collab-chat-root .msg-sidebar-acc__body {
    padding: 0 0.85rem 0.75rem;
    font-size: 0.8rem;
    color: var(--msg-acc-body);
    line-height: 1.5;
}

.collab-chat-root .msg-sidebar-acc__body--flush {
    padding: 0 0.55rem 0.65rem;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-details-section__hint {
    margin: 0 0 0.5rem;
    font-size: 0.76rem;
    color: var(--msg-hint);
    line-height: 1.45;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-list {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-empty--drawer {
    margin: 0;
    font-size: 0.84rem;
    color: var(--msg-hint);
    padding: 0.5rem 0;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-card {
    border: 1px solid var(--msg-media-card-border);
    border-radius: 16px;
    overflow: hidden;
    background: var(--msg-media-card-bg);
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-card__preview {
    background: var(--msg-media-preview-bg);
    padding: 0.45rem;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-card__preview img,
.collab-chat-root .collab-chat-sidebar .collab-chat-media-card__preview video {
    display: block;
    max-width: 100%;
    border-radius: 12px;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-card__preview audio {
    width: 100%;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-filelink {
    font-weight: 600;
    color: var(--msg-media-link);
    word-break: break-all;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-card__meta {
    padding: 0.5rem 0.65rem 0.6rem;
    font-size: 0.78rem;
    color: var(--msg-media-meta);
}

.collab-chat-root .collab-chat-sidebar .collab-chat-media-card__time {
    font-size: 0.72rem;
    color: var(--msg-media-time);
}

/* Theme grid (sidebar + legacy) */
.collab-chat-root .ch-theme-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 0.35rem;
}

.collab-chat-root .ch-theme-tile {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: flex-end;
    min-height: 108px;
    padding: 0;
    border: 2px solid transparent;
    border-radius: 16px;
    background: var(--ch-tile-bg);
    cursor: pointer;
    text-align: left;
    overflow: hidden;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.12s ease;
}

.collab-chat-root .ch-theme-tile:hover {
    transform: translateY(-1px);
    border-color: var(--ch-tile-hover-border);
}

.collab-chat-root .ch-theme-tile.is-selected {
    border-color: var(--ch-tile-selected-border);
    box-shadow: var(--ch-tile-selected-shadow);
}

.collab-chat-root .ch-theme-tile__preview {
    flex: 1;
    min-height: 56px;
    margin: 6px 6px 0;
    border-radius: 12px;
}

.collab-chat-root .ch-theme-tile__icon {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.65rem;
    color: var(--ch-tile-icon);
    min-height: 52px;
}

.collab-chat-root .ch-theme-tile__label {
    display: block;
    padding: 0.45rem 0.55rem 0.55rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--ch-tile-label);
    line-height: 1.25;
}

.collab-chat-root .ch-theme-tile__check {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--ch-tile-check-bg);
    color: var(--ch-tile-check-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    opacity: 0;
    transform: scale(0.85);
    transition: opacity 0.15s ease, transform 0.15s ease;
}

.collab-chat-root .ch-theme-tile.is-selected .ch-theme-tile__check {
    opacity: 1;
    transform: scale(1);
}

.collab-chat-root .ch-theme-tile--action .ch-theme-tile__label {
    text-align: center;
}

.collab-chat-root .ch-theme-advanced {
    margin-top: 0.5rem;
    border-radius: 14px;
    background: var(--ch-adv-bg);
    border: 1px solid var(--ch-adv-border);
}

.collab-chat-root .ch-theme-advanced__summary {
    padding: 0.65rem 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--ch-adv-summary);
    cursor: pointer;
    list-style: none;
}

.collab-chat-root .ch-theme-advanced__summary::-webkit-details-marker {
    display: none;
}

.collab-chat-root .ch-theme-advanced__summary::after {
    content: '▸';
    float: right;
    opacity: 0.6;
    transition: transform 0.2s ease;
}

.collab-chat-root .ch-theme-advanced[open] .ch-theme-advanced__summary::after {
    transform: rotate(90deg);
}

.collab-chat-root .ch-theme-advanced .collab-chat-theme-form {
    padding: 0 0.85rem 0.85rem;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-theme-row {
    font-size: 0.76rem;
    font-weight: 600;
    color: var(--ch-theme-input-color);
}

.collab-chat-root .collab-chat-sidebar .collab-chat-theme-row input[type="url"],
.collab-chat-root .collab-chat-sidebar .collab-chat-theme-row input[type="color"] {
    border: 1px solid var(--ch-theme-input-border);
    border-radius: 10px;
    padding: 0.4rem 0.5rem;
    font-size: 0.84rem;
    background: var(--ch-theme-input-bg);
    color: var(--ch-theme-input-color);
}

.collab-chat-root .collab-chat-sidebar .collab-chat-theme-row input[type="color"] {
    height: 2.35rem;
    cursor: pointer;
}

.collab-chat-root .collab-chat-sidebar .collab-chat-theme-check {
    font-size: 0.8rem;
    color: var(--ch-theme-check);
}

.collab-chat-root .collab-chat-sidebar .collab-chat-theme-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.25rem;
}

.collab-chat-root .ch-theme-btn {
    border-radius: 999px;
    padding: 0.45rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
    transition: background 0.15s ease, border-color 0.15s ease;
}

.collab-chat-root .ch-theme-btn--primary {
    background: var(--ch-btn-primary-bg);
    color: var(--ch-btn-primary-color);
    border-color: transparent;
}

.collab-chat-root .ch-theme-btn--primary:hover {
    filter: var(--ch-btn-primary-hover-filter);
}

.collab-chat-root .ch-theme-btn--ghost {
    background: transparent;
    color: var(--ch-btn-ghost-color);
    border-color: var(--ch-btn-ghost-border);
}

.collab-chat-root .ch-theme-btn--ghost:hover {
    background: var(--ch-btn-ghost-hover);
}

.collab-chat-root .collab-chat-sidebar .collab-chat-theme-status {
    margin: 0;
    min-height: 1.2rem;
    font-size: 0.78rem;
    color: var(--ch-theme-status);
}

/* Dark mode: groups / discussions / chat follow body.dark-mode (header toggle) */
body.dark-mode .collab-hub {
    --ch-glass: rgba(30, 41, 59, 0.82);
    --ch-glass-border: rgba(100, 116, 139, 0.28);
    --ch-slate: #e8eef7;
    --ch-muted: #94a3b8;
    --ch-line: rgba(100, 116, 139, 0.32);
    --collab-disc-bg: rgba(30, 41, 59, 0.88);
    --collab-disc-border: rgba(71, 85, 105, 0.45);
    --collab-disc-shadow: 0 10px 36px rgba(0, 0, 0, 0.25);
    --collab-group-bg: rgba(30, 41, 59, 0.9);
    --collab-group-border: rgba(71, 85, 105, 0.45);
    --collab-group-body-bg: rgba(30, 41, 59, 0.95);
    --collab-group-shadow: 0 12px 40px rgba(0, 0, 0, 0.28);
    --collab-group-approved-bg: rgba(30, 41, 59, 0.92);
    --collab-group-approved-body-bg: rgba(30, 41, 59, 0.98);
    --collab-panel-bg: rgba(30, 41, 59, 0.88);
    --collab-panel-border: rgba(71, 85, 105, 0.45);
    --collab-panel-shadow: 0 14px 44px rgba(0, 0, 0, 0.22);
    --collab-thread-card-bg: rgba(15, 23, 42, 0.55);
    --collab-thread-card-border: rgba(71, 85, 105, 0.5);
    --collab-compose-bg: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.75) 100%);
    --collab-feed-panel-bg: linear-gradient(165deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.85) 55%, rgba(15, 23, 42, 0.7) 100%);
    --collab-feed-composer-bg: rgba(30, 41, 59, 0.92);
    --collab-feed-post-bg: rgba(30, 41, 59, 0.88);
    --collab-form-shell-bg: rgba(30, 41, 59, 0.9);
    --collab-form-shell-border: rgba(71, 85, 105, 0.45);
    --collab-empty-bg: linear-gradient(160deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.75) 50%);
    --collab-member-chip-bg: rgba(30, 41, 59, 0.85);
    --collab-toolbar-input-bg: rgba(15, 23, 42, 0.65);
    --collab-pill-group-bg: linear-gradient(135deg, rgba(51, 65, 85, 0.9) 0%, rgba(30, 41, 59, 0.95) 100%);
    --collab-feed-composer-textarea-bg: rgba(15, 23, 42, 0.55);
    --collab-feed-composer-textarea-focus-bg: rgba(30, 41, 59, 0.95);
    --collab-feed-composer-toolbar-border: rgba(71, 85, 105, 0.45);
    --collab-feed-composer-attach-bg: rgba(30, 41, 59, 0.75);
    --collab-feed-composer-attach-hover-bg: rgba(51, 65, 85, 0.55);
    --collab-feed-panel-head-icon-color: #e8eef7;
    --collab-feed-avatar-text: #e8eef7;
    --collab-feed-join-bg: rgba(30, 41, 59, 0.88);
    --collab-feed-join-border: rgba(71, 85, 105, 0.45);
    --collab-feed-join-shadow: 0 8px 26px rgba(0, 0, 0, 0.2);
    --collab-feed-join-icon-bg: rgba(6, 182, 212, 0.12);
    --collab-feed-join-icon-border: rgba(6, 182, 212, 0.35);
}

body.dark-mode .collab-chat-root {
    --msg-shell-bg: rgba(30, 41, 59, 0.55);
    --msg-shell-border: rgba(71, 85, 105, 0.45);
    --msg-main-bg: #0f172a;
    --msg-head-bg: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
    --msg-head-border: rgba(100, 116, 139, 0.25);
    --msg-stream-bg: transparent;
    --msg-composer-bg: rgba(30, 41, 59, 0.92);
    --msg-composer-border: rgba(71, 85, 105, 0.45);
    --msg-input-bg: rgba(15, 23, 42, 0.65);
    --msg-input-color: #e8eef7;
    --msg-input-border: rgba(100, 116, 139, 0.35);
    --msg-attach-bg: rgba(30, 41, 59, 0.85);
    --msg-attach-border: rgba(100, 116, 139, 0.35);
    --msg-attach-color: #cbd5e1;
    --msg-author-messenger-other: #a5b4fc;
    --msg-author-messenger-self: #c4b5fd;
    --msg-meta-messenger: rgba(226, 232, 240, 0.55);
    --msg-details-active-border: rgba(167, 139, 250, 0.85);
    --msg-details-active-bg: rgba(167, 139, 250, 0.2);
    --msg-details-active-shadow: 0 0 0 1px rgba(167, 139, 250, 0.4);

    --msg-sidebar-bg: #1e293b;
    --msg-sidebar-border: rgba(71, 85, 105, 0.55);
    --msg-sidebar-done-bg: rgba(148, 163, 184, 0.15);
    --msg-sidebar-done-color: #e8eef7;
    --msg-sidebar-done-hover: rgba(148, 163, 184, 0.28);
    --msg-sidebar-profile-border: rgba(71, 85, 105, 0.5);
    --msg-sidebar-title: #f1f5f9;
    --msg-sidebar-sub: rgba(226, 232, 240, 0.65);
    --msg-scrollbar: rgba(255, 255, 255, 0.22) transparent;

    --msg-acc-bg: rgba(15, 23, 42, 0.55);
    --msg-acc-border: rgba(71, 85, 105, 0.45);
    --msg-acc-summary: #e8eef7;
    --msg-acc-body: rgba(226, 232, 240, 0.82);

    --msg-media-card-bg: rgba(30, 41, 59, 0.95);
    --msg-media-card-border: rgba(71, 85, 105, 0.45);
    --msg-media-preview-bg: rgba(15, 23, 42, 0.65);
    --msg-media-meta: #e8eef7;
    --msg-media-time: rgba(226, 232, 240, 0.55);
    --msg-media-link: #93c5fd;
    --msg-hint: rgba(226, 232, 240, 0.6);

    --ch-tile-bg: rgba(30, 41, 59, 0.9);
    --ch-tile-label: #e8eef7;
    --ch-tile-icon: rgba(226, 232, 240, 0.75);
    --ch-tile-hover-border: rgba(148, 163, 184, 0.35);
    --ch-tile-selected-border: #93c5fd;
    --ch-tile-selected-shadow: 0 0 0 1px rgba(147, 197, 253, 0.45);
    --ch-tile-check-bg: #93c5fd;
    --ch-tile-check-color: #0f172a;
    --ch-adv-bg: rgba(15, 23, 42, 0.55);
    --ch-adv-border: rgba(71, 85, 105, 0.45);
    --ch-adv-summary: #cbd5e1;
    --ch-theme-input-bg: rgba(15, 23, 42, 0.65);
    --ch-theme-input-border: rgba(100, 116, 139, 0.4);
    --ch-theme-input-color: #e8eef7;
    --ch-theme-check: rgba(226, 232, 240, 0.88);
    --ch-btn-primary-bg: linear-gradient(135deg, #548ca8, #355c7d);
    --ch-btn-primary-color: #fff;
    --ch-btn-primary-hover-filter: brightness(1.12);
    --ch-btn-ghost-color: #cbd5e1;
    --ch-btn-ghost-border: rgba(148, 163, 184, 0.35);
    --ch-btn-ghost-hover: rgba(148, 163, 184, 0.12);
    --ch-theme-status: rgba(226, 232, 240, 0.55);
    --msg-chat-layout-bg: rgba(30, 41, 59, 0.88);
    --msg-chat-layout-border: rgba(71, 85, 105, 0.45);
    --msg-chat-layout-shadow: 0 20px 56px rgba(0, 0, 0, 0.35);
}

body.dark-mode .collab-chat-root .collab-chat-stream {
    background-color: #1e293b;
    background-image: radial-gradient(rgba(148, 163, 184, 0.16) 1px, transparent 1px);
}

/* Undo global dark-mode.css `span { color !important }` inside collab UIs */
body.dark-mode .collab-hub span,
body.dark-mode .collab-chat-root span {
    color: inherit !important;
}

@media (max-width: 720px) {
    .collab-chat-root .collab-chat-head {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
    }
    .collab-chat-root .collab-chat-head__main {
        grid-column: 1 / -1;
        order: -1;
    }
    .collab-chat-root .collab-chat-head__back {
        justify-self: start;
    }
    .collab-chat-root .collab-chat-head__tray {
        grid-column: 2;
        justify-self: end;
    }

    .collab-chat-root .collab-chat-layout--messenger.is-sidebar-open .collab-chat-main-column {
        display: none;
    }

    .collab-chat-root .collab-chat-layout--messenger.is-sidebar-open .collab-chat-sidebar {
        width: 100%;
        min-width: 0;
        border-left: 0;
    }
}
</style>
