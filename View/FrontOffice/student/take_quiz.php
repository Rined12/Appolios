<?php
$studentSidebarActive = 'quiz';
$quizId = (int) ($quiz['id'] ?? 0);
$nq = count($quiz['questions'] ?? []);
$tl = isset($quiz['time_limit_sec']) && (int) $quiz['time_limit_sec'] > 0 ? (int) $quiz['time_limit_sec'] : null;
?>
<style>
    .student-quiz-take { max-width: 920px; margin: 0 auto; }
    .student-quiz-take .sq-timer {
        position: sticky;
        top: 14px;
        z-index: 30;
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        border-radius: 14px;
        border: 1px solid rgba(96, 165, 250, 0.22);
        background: linear-gradient(180deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.85) 100%);
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.45);
        padding: 0.7rem 0.9rem;
        margin: 0 0 0.9rem;
        backdrop-filter: blur(8px);
    }
    .student-quiz-take .sq-timer strong {
        font-weight: 900;
        color: rgba(226, 232, 240, 0.95);
        letter-spacing: -0.01em;
    }
    .student-quiz-take .sq-timer .sq-timer-time {
        font-variant-numeric: tabular-nums;
        font-weight: 900;
        color: rgba(191, 219, 254, 0.98);
        padding: 0.25rem 0.55rem;
        border-radius: 12px;
        border: 1px solid rgba(96, 165, 250, 0.22);
        background: rgba(59, 130, 246, 0.12);
        white-space: nowrap;
    }
    .student-quiz-take .sq-timer .sq-timer-bar {
        flex: 1;
        height: 10px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.16);
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.12);
    }
    .student-quiz-take .sq-timer .sq-timer-bar > span {
        display: block;
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, rgba(96, 165, 250, 0.8) 0%, rgba(59, 130, 246, 0.95) 40%, rgba(34, 211, 238, 0.85) 100%);
        border-radius: 999px;
        transform-origin: left;
        transform: scaleX(1);
        transition: transform 0.2s ease;
    }
    .student-quiz-take .sq-timer.sq-timer--warning {
        border-color: rgba(250, 204, 21, 0.22);
    }
    .student-quiz-take .sq-timer.sq-timer--warning .sq-timer-time {
        border-color: rgba(250, 204, 21, 0.22);
        background: rgba(250, 204, 21, 0.12);
        color: rgba(254, 240, 138, 0.98);
    }
    .student-quiz-take .sq-timer.sq-timer--danger {
        border-color: rgba(248, 113, 113, 0.22);
    }
    .student-quiz-take .sq-timer.sq-timer--danger .sq-timer-time {
        border-color: rgba(248, 113, 113, 0.22);
        background: rgba(248, 113, 113, 0.12);
        color: rgba(254, 202, 202, 0.98);
    }
    .student-quiz-take .sq-timeout-overlay {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(2, 6, 23, 0.72);
        backdrop-filter: blur(8px);
        z-index: 9999;
    }
    .student-quiz-take .sq-timeout-card {
        width: min(560px, 96vw);
        border-radius: 18px;
        border: 1px solid rgba(248, 113, 113, 0.18);
        background: linear-gradient(180deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.92) 100%);
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.55);
        padding: 1.25rem 1.2rem;
        color: rgba(226, 232, 240, 0.92);
        text-align: center;
    }
    .student-quiz-take .sq-timeout-card h2 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .student-quiz-take .sq-timeout-card p {
        margin: 0.6rem 0 0;
        color: rgba(226, 232, 240, 0.7);
        line-height: 1.45;
    }
    .student-quiz-take .sq-breadcrumb {
        display: flex; flex-wrap: wrap; align-items: center; gap: 0.35rem;
        font-size: 0.88rem; font-weight: 600; color: rgba(226, 232, 240, 0.7); margin: 0 0 0.75rem;
    }
    .student-quiz-take .sq-breadcrumb a {
        color: #60a5fa; text-decoration: none;
    }
    .student-quiz-take .sq-breadcrumb a:hover { text-decoration: underline; }
    .student-quiz-take .sq-hero {
        background: linear-gradient(180deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.45);
        margin-bottom: 1.25rem;
    }
    .student-quiz-take .sq-title {
        margin: 0 0 0.65rem;
        font-size: clamp(1.55rem, 2.4vw, 2.05rem);
        font-weight: 900;
        letter-spacing: -0.02em;
        color: rgba(226, 232, 240, 0.95);
        line-height: 1.15;
    }
    .student-quiz-take .sq-meta {
        display: flex; flex-wrap: wrap; gap: 0.5rem;
    }
    .student-quiz-take .sq-pill {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.32rem 0.72rem;
        border-radius: 999px;
        font-size: 0.78rem; font-weight: 700;
        border: 1px solid rgba(96, 165, 250, 0.35);
        background: rgba(59, 130, 246, 0.12);
        color: rgba(191, 219, 254, 0.95);
    }
    .student-quiz-take .sq-pill--muted {
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(148, 163, 184, 0.08);
        color: rgba(226, 232, 240, 0.75);
    }
    .student-quiz-take .sq-form {
        background: linear-gradient(180deg, rgba(30, 41, 59, 0.55) 0%, rgba(15, 23, 42, 0.75) 100%);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 18px;
        padding: 1.35rem 1.4rem 1.5rem;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.4);
    }
    .student-quiz-take .sq-question {
        border: 0;
        margin: 0 0 1.5rem;
        padding: 0;
    }
    .student-quiz-take .sq-question:last-of-type { margin-bottom: 0; }
    .student-quiz-take .sq-qnum {
        float: none;
        width: 100%;
        padding: 0;
        margin: 0 0 0.55rem;
        font-size: 0.78rem; font-weight: 800; letter-spacing: 0.04em;
        text-transform: uppercase; color: rgba(226, 232, 240, 0.65);
    }
    .student-quiz-take .sq-qtext {
        margin: 0 0 1rem;
        font-size: 1.05rem; font-weight: 600; color: rgba(226, 232, 240, 0.92);
        line-height: 1.55;
    }
    .student-quiz-take .sq-options {
        display: flex; flex-direction: column; gap: 0.55rem;
    }
    .student-quiz-take .sq-option {
        position: relative;
        display: flex; align-items: flex-start; gap: 0.75rem;
        margin: 0;
        padding: 0.72rem 0.85rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 12px;
        background: linear-gradient(180deg, rgba(2, 6, 23, 0.35) 0%, rgba(15, 23, 42, 0.65) 100%);
        cursor: pointer;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }
    .student-quiz-take .sq-option:hover {
        border-color: rgba(96, 165, 250, 0.55);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.16);
        transform: translateY(-1px);
    }
    .student-quiz-take .sq-option input {
        position: absolute; opacity: 0; width: 0; height: 0;
    }
    .student-quiz-take .sq-option .sq-radio {
        flex-shrink: 0;
        width: 20px; height: 20px; margin-top: 2px;
        border-radius: 50%;
        border: 2px solid rgba(148, 163, 184, 0.65);
        background: rgba(2, 6, 23, 0.2);
        position: relative;
    }
    .student-quiz-take .sq-option .sq-radio::after {
        content: ""; position: absolute; inset: 4px;
        border-radius: 50%; background: #60a5fa;
        opacity: 0; transform: scale(0.6);
        transition: opacity 0.15s ease, transform 0.15s ease;
    }
    .student-quiz-take .sq-option input:focus-visible + .sq-radio {
        outline: 2px solid #60a5fa; outline-offset: 2px;
    }
    .student-quiz-take .sq-option input:checked + .sq-radio {
        border-color: #60a5fa;
    }
    .student-quiz-take .sq-option input:checked + .sq-radio::after {
        opacity: 1; transform: scale(1);
    }
    .student-quiz-take .sq-option input:checked ~ .sq-label {
        color: rgba(226, 232, 240, 0.95);
    }
    .student-quiz-take .sq-option--selected {
        border-color: rgba(96, 165, 250, 0.8) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18) !important;
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.14) 0%, rgba(15, 23, 42, 0.65) 100%) !important;
    }
    .student-quiz-take .sq-label {
        flex: 1; min-width: 0;
        font-size: 0.95rem; font-weight: 600; color: rgba(226, 232, 240, 0.85);
        line-height: 1.45;
    }
    .student-quiz-take .sq-actions {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(148, 163, 184, 0.16);
        display: flex; flex-wrap: wrap; gap: 0.65rem; align-items: center;
    }
    .student-quiz-take .sq-question.sq-question--invalid .sq-qtext {
        color: rgba(248, 113, 113, 0.95);
    }
    .student-quiz-take .sq-question.sq-question--invalid {
        border-radius: 14px;
        padding: 0.85rem;
        background: rgba(127, 29, 29, 0.16);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14);
    }
    .student-quiz-take .sq-qerror {
        margin-top: 0.55rem;
        font-size: 0.85rem;
        font-weight: 800;
        color: rgba(248, 113, 113, 0.95);
    }
    @media (max-width: 640px) {
        .student-quiz-take .sq-form { padding: 1.1rem 1rem 1.25rem; }
    }
</style>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main student-quiz-take">
                <div class="sq-timer" id="sqTimer" aria-live="polite">
                    <strong>Temps restant</strong>
                    <div class="sq-timer-bar" aria-hidden="true"><span id="sqTimerBar"></span></div>
                    <span class="sq-timer-time" id="sqTimerTime">--:--</span>
                </div>
                <nav class="sq-breadcrumb" aria-label="Fil d’Ariane">
                    <a href="<?= APP_ENTRY ?>?url=student/quiz">Quiz</a>
                    <span aria-hidden="true">/</span>
                    <span><?= htmlspecialchars($quiz['title'] ?? 'Quiz') ?></span>
                </nav>
                <header class="sq-hero">
                    <h1 class="sq-title"><?= htmlspecialchars($quiz['title'] ?? 'Quiz') ?></h1>
                    <div class="sq-meta" role="list">
                        <?php if (!empty($quiz['course_title'] ?? '')): ?>
                            <span class="sq-pill sq-pill--muted" role="listitem"><?= htmlspecialchars($quiz['course_title']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($quiz['chapter_title'] ?? '')): ?>
                            <span class="sq-pill sq-pill--muted" role="listitem"><?= htmlspecialchars($quiz['chapter_title']) ?></span>
                        <?php endif; ?>
                        <span class="sq-pill" role="listitem"><?= $nq ?> question(s)</span>
                        <span class="sq-pill" role="listitem"><?= htmlspecialchars(difficulty_label_fr((string) ($quiz['difficulty'] ?? 'beginner'))) ?></span>
                        <?php if ($tl): ?>
                            <span class="sq-pill sq-pill--muted" role="listitem">Temps indicatif : <?= $tl ?> s</span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <form method="post" action="<?= APP_ENTRY ?>?url=student/submit-quiz/<?= $quizId ?>" class="sq-form" onsubmit="return appValidateTakeQuiz(this, <?= (int) $nq ?>);">
                    <input type="hidden" name="timed_out" value="0" id="sqTimedOut">
                    <?php foreach (($quiz['questions'] ?? []) as $i => $q): ?>
                        <fieldset class="sq-question student-quiz-question" data-q="<?= (int) $i ?>">
                            <legend class="sq-qnum">Question <?= $i + 1 ?> / <?= $nq ?></legend>
                            <p class="sq-qtext student-quiz-qtext"><?= htmlspecialchars($q['question'] ?? '') ?></p>
                            <div class="sq-options">
                            <?php $opts = $q['options'] ?? []; foreach ($opts as $oi => $opt): ?>
                                <label class="sq-option student-quiz-option">
                                    <input type="radio" name="answers[<?= $i ?>]" value="<?= $oi ?>">
                                    <span class="sq-radio" aria-hidden="true"></span>
                                    <span class="sq-label"><?= htmlspecialchars($opt) ?></span>
                                </label>
                            <?php endforeach; ?>
                            </div>
                            <div class="sq-qerror" hidden>Choisis une réponse pour continuer.</div>
                        </fieldset>
                    <?php endforeach; ?>
                    <div class="sq-actions">
                        <button type="submit" class="btn btn-primary">Soumettre mes réponses</button>
                        <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="sq-timeout-overlay" id="sqTimeoutOverlay" role="dialog" aria-modal="true" aria-labelledby="sqTimeoutTitle">
    <div class="sq-timeout-card">
        <h2 id="sqTimeoutTitle">Temps dépassé</h2>
        <p>Le temps limite du quiz est dépassé. Nous enregistrons maintenant votre tentative.</p>
    </div>
</div>
<script>
function appIsQuestionAnswered(form, idx) {
  var name = 'answers[' + idx + ']';
  var radios = form[name];
  if (!radios) return null;
  if (radios.length) {
    for (var j = 0; j < radios.length; j++) {
      if (radios[j].checked) return true;
    }
    return false;
  }
  return !!radios.checked;
}

function appSetQuestionValidity(form, idx, valid) {
  var fs = form.querySelector('.sq-question[data-q="' + idx + '"]');
  if (!fs) return;
  var msg = fs.querySelector('.sq-qerror');
  if (valid) {
    fs.classList.remove('sq-question--invalid');
    if (msg) msg.hidden = true;
  } else {
    fs.classList.add('sq-question--invalid');
    if (msg) msg.hidden = false;
  }
}

function appValidateTakeQuiz(form, n) {
  var timedOut = form.querySelector('#sqTimedOut');
  if (timedOut && String(timedOut.value) === '1') {
    return true;
  }
  var firstInvalid = null;
  for (var i = 0; i < n; i++) {
    var ans = appIsQuestionAnswered(form, i);
    if (ans === null) return false;
    if (!ans) {
      appSetQuestionValidity(form, i, false);
      if (firstInvalid === null) firstInvalid = i;
    } else {
      appSetQuestionValidity(form, i, true);
    }
  }
  if (firstInvalid !== null) {
    var first = form.querySelector('.sq-question[data-q="' + firstInvalid + '"]');
    if (first && typeof first.scrollIntoView === 'function') {
      first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return false;
  }
  return true;
}

document.addEventListener('DOMContentLoaded', function () {
  var root = document.querySelector('.student-quiz-take');
  if (!root) return;
  var form = root.querySelector('form.sq-form');

  var timeLimitSec = <?= $tl ? (int) $tl : 0 ?>;
  if (form && timeLimitSec > 0) {
    var timer = document.getElementById('sqTimer');
    var timerTime = document.getElementById('sqTimerTime');
    var timerBar = document.getElementById('sqTimerBar');
    var overlay = document.getElementById('sqTimeoutOverlay');
    var timedOutInput = document.getElementById('sqTimedOut');

    var key = 'quiz_timer_end_' + String(<?= (int) $quizId ?>);
    var now = Date.now();
    var end = 0;
    try { end = parseInt(sessionStorage.getItem(key) || '0', 10); } catch (e) { end = 0; }
    if (!end || end < now) {
      end = now + timeLimitSec * 1000;
      try { sessionStorage.setItem(key, String(end)); } catch (e) {}
    }

    function fmt(ms) {
      var s = Math.max(0, Math.floor(ms / 1000));
      var m = Math.floor(s / 60);
      var r = s % 60;
      var hh = Math.floor(m / 60);
      var mm = m % 60;
      if (hh > 0) {
        return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0') + ':' + String(r).padStart(2, '0');
      }
      return String(mm).padStart(2, '0') + ':' + String(r).padStart(2, '0');
    }

    var totalMs = timeLimitSec * 1000;
    var done = false;
    timer.style.display = 'flex';

    function tick() {
      if (done) return;
      var t = Date.now();
      var rem = end - t;
      var pct = Math.max(0, Math.min(1, rem / totalMs));
      if (timerTime) timerTime.textContent = fmt(rem);
      if (timerBar) timerBar.style.transform = 'scaleX(' + pct + ')';

      timer.classList.remove('sq-timer--warning');
      timer.classList.remove('sq-timer--danger');
      if (rem <= 60000) {
        timer.classList.add('sq-timer--danger');
      } else if (rem <= 180000) {
        timer.classList.add('sq-timer--warning');
      }

      if (rem <= 0) {
        done = true;
        if (timedOutInput) timedOutInput.value = '1';
        if (overlay) overlay.style.display = 'flex';
        try { sessionStorage.removeItem(key); } catch (e) {}

        root.querySelectorAll('input, button, select, textarea').forEach(function (el) {
          if (el && el.id === 'sqTimedOut') return;
          if (el && el.type === 'hidden') return;
          el.disabled = true;
        });

        setTimeout(function () {
          if (form && typeof form.submit === 'function') {
            form.submit();
          }
        }, 800);
        return;
      }
      setTimeout(tick, 250);
    }
    tick();
  }
  root.querySelectorAll('.sq-question').forEach(function (fs) {
    var qIdx = parseInt(fs.getAttribute('data-q') || '0', 10);
    var syncQuestion = function () {
      if (!form) return;
      var answered = appIsQuestionAnswered(form, qIdx);
      if (answered === null) return;
      appSetQuestionValidity(form, qIdx, answered);
    };
    fs.querySelectorAll('.sq-option input[type="radio"]').forEach(function (input) {
      var opt = input.closest('.sq-option');
      if (!opt) return;
      var sync = function () {
        fs.querySelectorAll('.sq-option').forEach(function (el) {
          el.classList.remove('sq-option--selected');
        });
        if (input.checked) opt.classList.add('sq-option--selected');
      };
      input.addEventListener('change', sync);
      input.addEventListener('focus', sync);
      input.addEventListener('change', syncQuestion);
      if (input.checked) opt.classList.add('sq-option--selected');
    });
    syncQuestion();
  });
});
</script>

