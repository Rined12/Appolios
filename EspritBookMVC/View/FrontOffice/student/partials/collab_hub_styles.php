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
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
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
    background: rgba(255, 255, 255, 0.98);
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
    background: rgba(255, 255, 255, 0.92);
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
    background: rgba(255, 255, 255, 0.94);
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 14px 44px rgba(15, 23, 42, 0.06);
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
    background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
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
    background: #fafbfc;
    border: 1px solid #eef2f6;
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

/* ---- Forms (create / edit discussion) ---- */
.collab-hub .collab-form-shell {
    max-width: 720px;
    margin-top: 0.5rem;
    padding: 1.65rem 1.65rem 1.85rem;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid rgba(226, 232, 240, 0.95);
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

/* ---- Live chat ---- */
.collab-chat-root .collab-chat-layout {
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 20px 56px rgba(15, 23, 42, 0.12);
    background: rgba(255, 255, 255, 0.96);
}

.collab-chat-root .collab-chat-head {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: 1.15rem 1.35rem;
    background: linear-gradient(135deg, rgba(43, 72, 101, 0.97) 0%, rgba(53, 92, 125, 0.96) 100%);
    color: #fff;
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
</style>
