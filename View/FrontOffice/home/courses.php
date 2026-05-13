<?php
/**
 * APPOLIOS - Public Courses Page
 */
$brandingLogo = APP_URL . '/View/assets/images/branding/appolios-hero-logo.png';
?>

<style>
    .public-courses-page {
        --pc-ink: #0f172a;
        --pc-muted: #64748b;
        --pc-line: rgba(84, 140, 168, 0.18);
        --pc-accent: #548ca8;
        --pc-warm: #e19864;
    }

    .public-courses-hero {
        position: relative;
        padding: clamp(4.5rem, 10vw, 6.5rem) 0 clamp(2.5rem, 5vw, 3.5rem);
        overflow: hidden;
        background:
            radial-gradient(900px 420px at 12% -20%, rgba(84, 140, 168, 0.18), transparent 55%),
            radial-gradient(700px 360px at 88% 0%, rgba(225, 152, 100, 0.14), transparent 50%),
            linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    }

    .public-courses-hero::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--pc-line), transparent);
    }

    .public-courses-hero-inner {
        position: relative;
        z-index: 1;
        max-width: 720px;
        margin: 0 auto;
        text-align: center;
    }

    .public-courses-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--pc-accent);
        border: 1px solid rgba(84, 140, 168, 0.28);
        background: rgba(255, 255, 255, 0.75);
        margin-bottom: 1rem;
    }

    .public-courses-hero h1 {
        margin: 0 0 0.75rem;
        font-size: clamp(2rem, 4.2vw, 2.75rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 1.12;
        color: var(--pc-ink);
    }

    .public-courses-hero h1 span {
        color: var(--pc-warm);
    }

    .public-courses-hero p {
        margin: 0;
        font-size: 1.05rem;
        line-height: 1.65;
        color: var(--pc-muted);
        max-width: 52ch;
        margin-inline: auto;
    }

    .public-courses-grid-section {
        padding: clamp(2rem, 5vw, 3.5rem) 0 4.5rem;
        background: #f1f5f9;
    }

    .public-courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr));
        gap: clamp(1.25rem, 3vw, 1.75rem);
    }

    .public-course-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--pc-line);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .public-course-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 44px rgba(15, 23, 42, 0.12);
        border-color: rgba(84, 140, 168, 0.35);
    }

    .public-course-card__media {
        position: relative;
        height: 200px;
        flex-shrink: 0;
        background: linear-gradient(145deg, #e8f1f8 0%, #f8fafc 40%, #fff5eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }

    .public-course-card__media::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(120% 80% at 50% 0%, rgba(84, 140, 168, 0.12), transparent 55%);
        pointer-events: none;
    }

    .public-course-card__thumb {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0;
    }

    .public-course-card__brand {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .public-course-card__logo {
        max-width: 220px;
        max-height: 150px;
        width: auto;
        height: auto;
        object-fit: contain;
        filter: drop-shadow(0 8px 20px rgba(15, 23, 42, 0.12));
    }

    .public-course-card__body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 1.35rem 1.4rem 1.5rem;
    }

    .public-course-card__body h3 {
        margin: 0 0 0.65rem;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.3;
        color: var(--pc-ink);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .public-course-card__excerpt {
        margin: 0 0 1rem;
        font-size: 0.92rem;
        line-height: 1.55;
        color: var(--pc-muted);
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .public-course-card__meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding-top: 0.9rem;
        margin-bottom: 1rem;
        border-top: 1px solid rgba(148, 163, 184, 0.25);
        font-size: 0.82rem;
        color: var(--pc-muted);
        font-weight: 600;
    }

    .public-course-card__cta {
        margin-top: auto;
        border-radius: 12px !important;
        font-weight: 700 !important;
        padding: 0.65rem 1rem !important;
        border: none !important;
        background: linear-gradient(135deg, #548ca8 0%, #3d6d8c 100%) !important;
        box-shadow: 0 8px 20px rgba(84, 140, 168, 0.28);
        transition: filter 0.2s ease, transform 0.2s ease !important;
    }

    .public-course-card__cta:hover {
        filter: brightness(1.06);
        transform: translateY(-1px);
        color: #fff !important;
    }

    .public-course-card__cta.btn-secondary {
        background: rgba(248, 250, 252, 0.95) !important;
        color: #334155 !important;
        border: 1px solid rgba(84, 140, 168, 0.35) !important;
        box-shadow: none !important;
    }

    .public-course-card__cta.btn-secondary:hover {
        background: #fff !important;
        border-color: var(--pc-warm) !important;
        color: #0f172a !important;
    }

    .public-courses-empty {
        max-width: 480px;
        margin: 0 auto;
        text-align: center;
        padding: clamp(2.5rem, 6vw, 3.5rem) 1.5rem;
        border-radius: 20px;
        background: #fff;
        border: 1px solid var(--pc-line);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
    }

    .public-courses-empty img {
        width: min(240px, 70%);
        height: auto;
        margin-bottom: 1.25rem;
        opacity: 0.95;
    }

    .public-courses-empty h3 {
        margin: 0 0 0.5rem;
        font-weight: 800;
        color: var(--pc-ink);
    }

    .public-courses-empty p {
        margin: 0;
        color: var(--pc-muted);
        line-height: 1.55;
    }
</style>

<div class="public-courses-page">
    <section class="public-courses-hero">
        <div class="container">
            <div class="public-courses-hero-inner">
                <span class="public-courses-hero-kicker">Catalogue</span>
                <h1>Explore our <span>courses</span></h1>
                <p>Practical paths, mentor-backed content, and clear next steps—pick a course and start building real skills.</p>
            </div>
        </div>
    </section>

    <section class="public-courses-grid-section">
        <div class="container">
            <?php if (!empty($courses)): ?>
                <div class="public-courses-grid">
                    <?php foreach ($courses as $course): ?>
                        <?php
                            $desc = (string) ($course['description'] ?? '');
                            if (function_exists('mb_strlen') && function_exists('mb_substr') && mb_strlen($desc) > 160) {
                                $excerpt = mb_substr($desc, 0, 160) . '…';
                            } elseif (strlen($desc) > 160) {
                                $excerpt = substr($desc, 0, 160) . '…';
                            } else {
                                $excerpt = $desc;
                            }
                        ?>
                        <article class="public-course-card">
                            <div class="public-course-card__media">
                                <?php if (!empty($course['image'])): ?>
                                    <img class="public-course-card__thumb" src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title'] ?? '') ?>">
                                <?php else: ?>
                                    <div class="public-course-card__brand">
                                        <img class="public-course-card__logo" src="<?= htmlspecialchars($brandingLogo) ?>" alt="APPOLIOS" width="220" height="160" loading="lazy">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="public-course-card__body">
                                <h3><?= htmlspecialchars($course['title'] ?? '') ?></h3>
                                <p class="public-course-card__excerpt"><?= htmlspecialchars($excerpt) ?></p>
                                <div class="public-course-card__meta">
                                    <span>By <?= htmlspecialchars($course['creator_name'] ?? 'Instructor') ?></span>
                                    <span><?= !empty($course['created_at']) ? date('M Y', strtotime((string) $course['created_at'])) : '' ?></span>
                                </div>
                                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                    <a href="<?= APP_ENTRY ?>?url=student/course/<?= (int) ($course['id'] ?? 0) ?>" class="btn btn-primary btn-block public-course-card__cta">View course</a>
                                <?php else: ?>
                                    <a href="<?= APP_ENTRY ?>?url=login" class="btn btn-secondary btn-block public-course-card__cta">Sign in to enroll</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="public-courses-empty">
                    <img src="<?= htmlspecialchars($brandingLogo) ?>" alt="APPOLIOS" width="240" height="180" loading="eager">
                    <h3>No courses yet</h3>
                    <p>New programs are on the way. Check back soon or create an account to get notified.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
