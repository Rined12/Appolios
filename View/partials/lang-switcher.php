<?php
/**
 * Language switcher — expects $lang, $currentLang, $availableLangs from BaseController::view().
 */
$lang = $lang ?? [];
$currentLang = $currentLang ?? 'fr';
$availableLangs = $availableLangs ?? ['fr', 'en', 'ar'];

$returnRoute = isset($_GET['url']) ? trim((string) $_GET['url'], '/') : 'home/index';
if ($returnRoute === '' || str_contains($returnRoute, '..') || !preg_match('#^[a-zA-Z0-9][a-zA-Z0-9\-_/]*$#', $returnRoute)) {
    $returnRoute = 'home/index';
}

$labels = ['fr' => 'FR', 'en' => 'EN', 'ar' => 'ع'];
?>
<div class="appolios-lang-switch" role="navigation" aria-label="<?= htmlspecialchars($lang['lang_switch_aria'] ?? 'Language') ?>">
    <span class="appolios-lang-switch__label visually-hidden"><?= htmlspecialchars($lang['lang_label'] ?? 'Language') ?></span>
    <div class="appolios-lang-switch__group">
        <?php foreach ($availableLangs as $code) :
            $code = strtolower((string) $code);
            $active = $currentLang === $code;
            $href = APP_ENTRY . '?url=' . rawurlencode('language/switch')
                . '&lang=' . rawurlencode($code)
                . '&return=' . rawurlencode($returnRoute);
            ?>
            <a class="appolios-lang-switch__btn<?= $active ? ' is-active' : '' ?>"
               href="<?= htmlspecialchars($href) ?>"
               hreflang="<?= htmlspecialchars($code) ?>"
               lang="<?= htmlspecialchars($code) ?>"
               <?= $active ? 'aria-current="true"' : '' ?>>
                <?= htmlspecialchars($labels[$code] ?? strtoupper($code)) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
