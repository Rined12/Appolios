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
    font-family: 'Inter', system-ui, sans-serif;
}

/* Core card surfaces (used by group create/show layouts) */
.collab-hub .collab-form-shell,
.collab-hub .collab-detail-sidecard,
.collab-hub .collab-thread-panel {
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(226, 232, 240, 0.95);
    border-radius: 22px;
    box-shadow: 0 16px 44px rgba(15, 23, 42, 0.08);
}

.collab-hub .collab-form-shell {
    padding: 1.75rem 1.8rem;
}

.collab-hub .collab-form-shell label {
    color: var(--ch-slate);
    font-weight: 800;
}

.collab-hub .collab-form-shell input[type="text"],
.collab-hub .collab-form-shell input[type="email"],
.collab-hub .collab-form-shell input[type="number"],
.collab-hub .collab-form-shell textarea,
.collab-hub .collab-form-shell select {
    width: 100%;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: #fff;
    color: var(--ch-slate);
    outline: none;
}

.collab-hub .collab-form-shell textarea {
    resize: vertical;
}

.collab-hub .collab-form-shell input:focus,
.collab-hub .collab-form-shell textarea:focus,
.collab-hub .collab-form-shell select:focus {
    border-color: rgba(84, 140, 168, 0.65);
    box-shadow: 0 0 0 4px rgba(84, 140, 168, 0.18);
}

/* Group detail header layout */
.collab-hub .collab-detail-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 1.25rem;
    align-items: start;
}

@media (max-width: 1024px) {
    .collab-hub .collab-detail-hero {
        grid-template-columns: 1fr;
    }
}

.collab-hub .collab-detail-banner {
    position: relative;
    border-radius: 26px;
    overflow: hidden;
    min-height: 180px;
    background: radial-gradient(900px 320px at 20% 0%, rgba(225, 152, 100, 0.25), transparent 60%),
        radial-gradient(700px 280px at 95% 10%, rgba(84, 140, 168, 0.28), transparent 62%),
        linear-gradient(135deg, rgba(43, 72, 101, 0.95) 0%, rgba(84, 140, 168, 0.85) 100%);
    box-shadow: 0 22px 55px rgba(43, 72, 101, 0.22);
}

.collab-hub .collab-detail-banner > img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.collab-hub .collab-detail-banner::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.55) 0%, rgba(15, 23, 42, 0.18) 55%, rgba(15, 23, 42, 0.05) 100%);
    pointer-events: none;
}

.collab-hub .collab-detail-banner__inner {
    position: relative;
    z-index: 1;
    padding: 1.85rem 2rem;
    color: #fff;
    max-width: 820px;
}

.collab-hub .collab-detail-banner__inner h1 {
    margin: 0;
    font-size: clamp(1.6rem, 3vw, 2.15rem);
    font-weight: 900;
    letter-spacing: -0.03em;
}

.collab-hub .collab-detail-banner__inner p {
    margin: 0.55rem 0 0;
    opacity: 0.92;
    line-height: 1.55;
}

/* Members sidecard */
.collab-hub .collab-detail-sidecard {
    padding: 1.35rem 1.35rem 1.45rem;
}

.collab-hub .collab-detail-sidecard h3 {
    margin: 0 0 1rem 0;
    font-size: 0.95rem;
    font-weight: 900;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ch-muted);
}

.collab-hub .collab-members-list {
    display: grid;
    gap: 0.7rem;
}

.collab-hub .collab-member-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.55rem 0.65rem;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: #fff;
    width: fit-content;
    max-width: 100%;
}

.collab-hub .collab-member-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(135deg, var(--ch-teal-soft), var(--ch-coral));
    box-shadow: 0 10px 22px rgba(43, 72, 101, 0.18);
    flex: 0 0 auto;
}

.collab-hub .collab-member-name {
    font-weight: 800;
    color: var(--ch-slate);
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 160px;
}

.collab-hub .collab-member-role {
    margin-left: 0.2rem;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    color: var(--ch-teal-soft);
}

/* Group discussions composer */
.collab-hub .collab-thread-panel {
    padding: 1.5rem 1.6rem;
    margin-top: 1.25rem;
}

.collab-hub .collab-thread-panel > h3 {
    margin: 0 0 1rem 0;
    font-size: 1.05rem;
    font-weight: 900;
    color: var(--ch-slate);
    letter-spacing: -0.02em;
}

.collab-hub .collab-thread-composer {
    border: 2px dashed rgba(148, 163, 184, 0.4);
    border-radius: 18px;
    padding: 1.2rem 1.2rem 1.25rem;
    margin-bottom: 1.25rem;
    background: linear-gradient(160deg, rgba(248, 250, 252, 0.9) 0%, rgba(255, 255, 255, 0.92) 55%);
}

.collab-hub .collab-thread-composer__eyebrow {
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ch-muted);
    margin-bottom: 0.9rem;
}

.collab-hub .collab-thread-form label {
    display: block;
    margin-bottom: 0.35rem;
}

.collab-hub .collab-thread-form input[type="text"],
.collab-hub .collab-thread-form textarea {
    width: 100%;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: #fff;
    color: var(--ch-slate);
}

.collab-hub .collab-thread-form input:focus,
.collab-hub .collab-thread-form textarea:focus {
    border-color: rgba(84, 140, 168, 0.65);
    box-shadow: 0 0 0 4px rgba(84, 140, 168, 0.18);
    outline: none;
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
    position: relative;
    z-index: 400;
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
    background: rgba(255, 255, 255, 0.95);
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
    background: rgba(255, 255, 255, 0.95);
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

/* ---- Groups ---- */
.collab-hub .collab-group-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
    align-items: stretch;
}

.collab-hub .collab-group-card {
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 12px 34px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 310px;
}

.collab-hub .collab-group-card__media {
    position: relative;
    height: 150px;
    background: linear-gradient(145deg, rgba(59, 130, 246, 0.16), rgba(34, 197, 94, 0.14));
    display: flex;
    align-items: flex-end;
}

.collab-hub .collab-group-card__media img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.collab-hub .collab-group-card__media--fallback {
    background: radial-gradient(900px 260px at 20% 10%, rgba(59, 130, 246, 0.24), transparent 60%),
        radial-gradient(700px 220px at 85% 0%, rgba(34, 197, 94, 0.22), transparent 62%),
        linear-gradient(145deg, rgba(15, 23, 42, 0.06), rgba(15, 23, 42, 0.02));
}

.collab-hub .collab-group-card__ph {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(15, 23, 42, 0.35);
    font-size: 2.1rem;
    position: relative;
    z-index: 1;
}

.collab-hub .collab-group-card__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.55) 100%);
    z-index: 1;
}

.collab-hub .collab-group-card__floating-title {
    position: relative;
    z-index: 2;
    margin: 0;
    padding: 0.95rem 1rem;
    color: #fff;
    font-weight: 900;
    letter-spacing: -0.02em;
    font-size: 1.05rem;
    text-shadow: 0 2px 12px rgba(15, 23, 42, 0.35);
    width: 100%;
}

.collab-hub .collab-group-card__pending-tag {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: rgba(15, 23, 42, 0.7);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.collab-hub .collab-group-card__body {
    padding: 1rem 1rem 1.05rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    flex: 1 1 auto;
    min-height: 0;
}

.collab-hub .collab-group-card__body p {
    margin: 0;
    color: var(--ch-muted);
    font-size: 0.92rem;
    line-height: 1.55;
}

.collab-hub .collab-group-card__body .collab-card-actions {
    margin-top: auto;
}

.collab-hub .collab-group-card--pending {
    border-color: rgba(245, 158, 11, 0.25);
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
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 10px 36px rgba(15, 23, 42, 0.07);
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
    background: linear-gradient(135deg, #e9f1fa 0%, #f1f5f9 100%);
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

.collab-hub .collab-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    border-radius: 18px;
    background: linear-gradient(160deg, #f8fafc 0%, #fff 50%);
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

/* ---- Group feed ---- */
.collab-hub .collab-feed-card {
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 16px 44px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.collab-hub .collab-feed-card__head {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.1rem;
    border-bottom: 1px solid rgba(226, 232, 240, 0.95);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 250, 252, 0.92) 100%);
}

.collab-hub .collab-feed-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(145deg, rgba(84, 140, 168, 0.95), rgba(43, 72, 101, 0.98));
    color: #fff;
    box-shadow: 0 10px 24px rgba(43, 72, 101, 0.22);
}

.collab-hub .collab-feed-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    color: var(--ch-slate);
}

.collab-hub .collab-composer {
    padding: 1.15rem 1.15rem 1.1rem;
}

.collab-hub .collab-composer__grid {
    display: grid;
    grid-template-columns: 52px minmax(0, 1fr);
    gap: 0.95rem;
}

.collab-hub .collab-avatar {
    width: 52px;
    height: 52px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(145deg, #3b82f6, #22c55e);
    box-shadow: 0 14px 34px rgba(59, 130, 246, 0.24);
}

.collab-hub .collab-composer__title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 950;
    letter-spacing: -0.02em;
    color: #0f172a;
}

.collab-hub .collab-composer__name {
    margin-top: 0.15rem;
    color: #0f172a;
    font-weight: 850;
}

.collab-hub .collab-composer textarea {
    width: 100%;
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.95);
    padding: 0.95rem 1rem;
    min-height: 130px;
    resize: vertical;
    background: rgba(248, 250, 252, 0.8);
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
}

.collab-hub .collab-composer textarea:focus {
    outline: none;
    border-color: rgba(59, 130, 246, 0.55);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.18);
}

.collab-hub .collab-composer__toolbar {
    margin-top: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.collab-hub .collab-composer__tools {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.55rem 0.65rem;
    border-radius: 14px;
    border: 1px solid rgba(226, 232, 240, 0.95);
    background: rgba(248, 250, 252, 0.9);
}

.collab-hub .collab-tool-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1px solid rgba(226, 232, 240, 0.95);
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #334155;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}

.collab-hub .collab-tool-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.1);
    border-color: rgba(148, 163, 184, 0.7);
}

.collab-hub .collab-publish {
    width: 100%;
    margin-top: 0.95rem;
    border-radius: 14px;
    padding: 0.85rem 1rem;
    font-weight: 900;
}

.collab-hub .collab-post {
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 16px 44px rgba(15, 23, 42, 0.08);
    padding: 1.05rem 1.1rem;
}

.collab-hub .collab-post__meta {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.collab-hub .collab-post__author {
    font-weight: 950;
    color: #0f172a;
}

.collab-hub .collab-post__time {
    color: #64748b;
    font-size: 0.84rem;
    margin-top: 0.1rem;
}

.collab-hub .collab-post__content {
    margin-top: 0.85rem;
    white-space: pre-wrap;
    line-height: 1.65;
    color: #1f2f46;
}

.collab-hub .collab-post__media {
    margin-top: 0.85rem;
}

.collab-hub .collab-post__media img,
.collab-hub .collab-post__media video {
    max-width: 100%;
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.95);
}

.collab-hub .collab-post__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    margin-top: 1rem;
}

.collab-hub .collab-post__comments {
    margin-top: 1rem;
    border-top: 1px solid rgba(226, 232, 240, 0.95);
    padding-top: 1rem;
}

.collab-hub .collab-comment {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
}

.collab-hub .collab-comment__author {
    font-weight: 900;
    color: #0f172a;
}

.collab-hub .collab-comment__time {
    font-weight: 650;
    color: #64748b;
    font-size: 0.82rem;
}

.collab-hub .collab-comment__text {
    color: #1f2f46;
    line-height: 1.55;
    word-break: break-word;
}

/* ---- Live chat ---- */
.collab-chat-root .collab-chat-layout {
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 20px 56px rgba(15, 23, 42, 0.12);
    background: rgba(255, 255, 255, 0.96);
}

.collab-chat-root .collab-chat-head {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 0.85rem 1rem;
    padding: 1.15rem 1.35rem;
    background: linear-gradient(135deg, rgba(43, 72, 101, 0.97) 0%, rgba(53, 92, 125, 0.96) 100%);
    color: #fff;
}

.collab-chat-root .collab-chat-head__center {
    text-align: center;
    justify-self: center;
    min-width: 0;
    max-width: 100%;
}

.collab-chat-root .collab-chat-head__back,
.collab-chat-root .collab-chat-head__details {
    justify-self: start;
    min-width: 6.5rem;
    border-radius: 11px;
    font-weight: 700;
}

.collab-chat-root .collab-chat-head__details {
    justify-self: end;
}

@media (max-width: 520px) {
    .collab-chat-root .collab-chat-head {
        grid-template-columns: 1fr 1fr;
        grid-template-areas:
            "center center"
            "back details";
    }
    .collab-chat-root .collab-chat-head__center {
        grid-area: center;
    }
    .collab-chat-root .collab-chat-head__back {
        grid-area: back;
        justify-self: start;
    }
    .collab-chat-root .collab-chat-head__details {
        grid-area: details;
        justify-self: end;
    }
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
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}

.collab-chat-root .chat-row.self .chat-bubble {
    border-radius: 16px 16px 6px 16px;
    background: linear-gradient(145deg, #dbeafe 0%, #eff6ff 100%);
    border-color: rgba(59, 130, 246, 0.35);
}

.collab-chat-root .chat-text {
    color: #0f172a;
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

/* Browser-style omnibox suggestions (groups / discussions search) */
.collab-hub .hub-omnibox-wrap {
    position: relative;
    z-index: 2;
}

.collab-hub .hub-omnibox-dropdown {
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + 10px);
    z-index: 5000;
    display: none;
    max-height: min(380px, 52vh);
    overflow-y: auto;
    border-radius: 18px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: #ffffff;
    box-shadow: 0 22px 50px rgba(15, 23, 42, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.collab-hub .hub-omnibox-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    width: 100%;
    border: 0;
    border-bottom: 1px solid rgba(226, 232, 240, 0.85);
    background: transparent;
    text-align: left;
    padding: 11px 14px;
    cursor: pointer;
    transition: background 0.14s ease;
}

.collab-hub .hub-omnibox-item:last-child {
    border-bottom: 0;
}

.collab-hub .hub-omnibox-item:hover,
.collab-hub .hub-omnibox-item.active {
    background: linear-gradient(90deg, rgba(239, 246, 255, 0.95), rgba(248, 250, 252, 0.6));
}

.collab-hub .hub-omnibox-icon {
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    background: linear-gradient(145deg, #e0f2fe, #dbeafe);
    color: #1d4ed8;
    border: 1px solid rgba(59, 130, 246, 0.28);
}

.collab-hub .hub-omnibox-icon--group {
    background: linear-gradient(145deg, #ffedd5, #fed7aa);
    color: #c2410c;
    border-color: rgba(234, 88, 12, 0.35);
}

.collab-hub .hub-omnibox-body {
    flex: 1 1 auto;
    min-width: 0;
}

.collab-hub .hub-omnibox-primary {
    display: block;
    font-size: 0.92rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.015em;
    line-height: 1.25;
    word-break: break-word;
}

.collab-hub .hub-omnibox-secondary {
    display: block;
    margin-top: 3px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
    line-height: 1.35;
    word-break: break-word;
}

.collab-hub .hub-omnibox-badge {
    flex: 0 0 auto;
    align-self: center;
    font-size: 0.65rem;
    font-weight: 900;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 4px 8px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.06);
    color: #475569;
}

.collab-hub .hub-omnibox-empty {
    padding: 12px 14px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #94a3b8;
}

body.dark-mode .collab-hub .hub-omnibox-dropdown {
    border-color: rgba(255, 255, 255, 0.08);
    background: linear-gradient(165deg, #35262c 0%, #2a1e24 45%, #231a20 100%);
    box-shadow: 0 26px 56px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

body.dark-mode .collab-hub .hub-omnibox-item {
    border-bottom-color: rgba(255, 255, 255, 0.06);
}

body.dark-mode .collab-hub .hub-omnibox-item:hover,
body.dark-mode .collab-hub .hub-omnibox-item.active {
    background: rgba(255, 255, 255, 0.06);
}

body.dark-mode .collab-hub .hub-omnibox-primary {
    color: #f8fafc;
}

body.dark-mode .collab-hub .hub-omnibox-secondary {
    color: rgba(248, 250, 252, 0.68);
}

body.dark-mode .collab-hub .hub-omnibox-badge {
    background: rgba(255, 255, 255, 0.08);
    color: #e2e8f0;
}

body.dark-mode .collab-hub .hub-omnibox-icon {
    background: rgba(255, 255, 255, 0.08);
    color: #93c5fd;
    border-color: rgba(147, 197, 253, 0.25);
}

body.dark-mode .collab-hub .hub-omnibox-icon--group {
    color: #fdba74;
    border-color: rgba(251, 146, 60, 0.35);
}

body.dark-mode .collab-hub .hub-omnibox-empty {
    color: rgba(248, 250, 252, 0.55);
}

/* ---- Student / Teacher shell (Groups / Discussions list + related) ---- */
.student-events-page.collab-hub .admin-main,
.teacher-collab-page.collab-hub .admin-main {
    background: transparent;
}

.student-events-page.collab-hub .collab-hero,
.teacher-collab-page.collab-hub .collab-hero {
    background: linear-gradient(125deg, #0f172a 0%, #1e293b 42%, #3d556d 100%);
    box-shadow: 0 20px 52px rgba(15, 23, 42, 0.24);
}

.student-events-page.collab-hub .collab-hero::before,
.teacher-collab-page.collab-hub .collab-hero::before {
    opacity: 0.95;
}

.student-events-page.collab-hub .collab-hero p,
.teacher-collab-page.collab-hub .collab-hero p {
    color: rgba(203, 213, 225, 0.95);
    opacity: 1;
}

/* Light shell only — dark mode uses body.dark-mode rules below */
body:not(.dark-mode) .student-events-page.collab-hub .collab-toolbar,
body:not(.dark-mode) .teacher-collab-page.collab-hub .collab-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
    box-shadow: 0 4px 22px rgba(15, 23, 42, 0.08);
}

body:not(.dark-mode) .student-events-page.collab-hub .collab-toolbar .collab-search-row input[type="text"],
body:not(.dark-mode) .teacher-collab-page.collab-hub .collab-toolbar .collab-search-row input[type="text"] {
    background: #f8fafc;
    border-color: #e2e8f0;
}

body:not(.dark-mode) .student-events-page.collab-hub .collab-toolbar select,
body:not(.dark-mode) .teacher-collab-page.collab-hub .collab-toolbar select {
    background: #f8fafc;
    border-color: #e2e8f0;
}

body.dark-mode .student-events-page.collab-hub .collab-toolbar,
body.dark-mode .teacher-collab-page.collab-hub .collab-toolbar {
    background: rgba(30, 41, 59, 0.94) !important;
    border: 1px solid rgba(84, 140, 168, 0.28) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

body.dark-mode .student-events-page.collab-hub .collab-toolbar label,
body.dark-mode .teacher-collab-page.collab-hub .collab-toolbar label {
    color: #94a3b8 !important;
}

body.dark-mode .student-events-page.collab-hub .collab-toolbar .collab-search-row input[type="text"],
body.dark-mode .teacher-collab-page.collab-hub .collab-toolbar .collab-search-row input[type="text"] {
    background: rgba(15, 23, 42, 0.85) !important;
    border-color: rgba(84, 140, 168, 0.35) !important;
    color: #e2e8f0 !important;
}

body.dark-mode .student-events-page.collab-hub .collab-toolbar select,
body.dark-mode .teacher-collab-page.collab-hub .collab-toolbar select {
    background: rgba(15, 23, 42, 0.85) !important;
    border-color: rgba(84, 140, 168, 0.35) !important;
    color: #e2e8f0 !important;
}

body.dark-mode .student-events-page.collab-hub .collab-empty,
body.dark-mode .teacher-collab-page.collab-hub .collab-empty {
    background: linear-gradient(160deg, rgba(30, 41, 59, 0.96) 0%, rgba(15, 23, 42, 0.9) 50%) !important;
    border-color: rgba(84, 140, 168, 0.35) !important;
    color: #cbd5e1 !important;
}

body.dark-mode .student-events-page.collab-hub .collab-empty h3,
body.dark-mode .teacher-collab-page.collab-hub .collab-empty h3 {
    color: #f8fafc !important;
}

body.dark-mode .student-events-page.collab-hub .collab-empty p,
body.dark-mode .teacher-collab-page.collab-hub .collab-empty p {
    color: #94a3b8 !important;
}

body.dark-mode .student-events-page.collab-hub .collab-empty .collab-empty-icon--glyph,
body.dark-mode .teacher-collab-page.collab-hub .collab-empty .collab-empty-icon--glyph {
    font-size: 2.75rem;
    line-height: 1;
    margin-bottom: 0.75rem;
    opacity: 1;
    color: #94a3b8 !important;
}

body.dark-mode .student-events-page.collab-hub .collab-empty .collab-empty-icon--glyph.collab-empty-icon--discussions,
body.dark-mode .teacher-collab-page.collab-hub .collab-empty .collab-empty-icon--glyph.collab-empty-icon--discussions {
    color: #c4b5fd !important;
    opacity: 0.95;
}

body:not(.dark-mode) .student-events-page.collab-hub .collab-empty .collab-empty-icon--glyph,
body:not(.dark-mode) .teacher-collab-page.collab-hub .collab-empty .collab-empty-icon--glyph {
    font-size: 2.75rem;
    line-height: 1;
    margin-bottom: 0.75rem;
    opacity: 1;
    color: #64748b;
}

body:not(.dark-mode) .student-events-page.collab-hub .collab-empty .collab-empty-icon--glyph.collab-empty-icon--discussions,
body:not(.dark-mode) .teacher-collab-page.collab-hub .collab-empty .collab-empty-icon--glyph.collab-empty-icon--discussions {
    color: #8b5cf6;
    opacity: 0.9;
}
</style>
