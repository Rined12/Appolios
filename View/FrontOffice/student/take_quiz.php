<?php
$studentSidebarActive = 'quiz';
$quizId = (int) ($quiz['id'] ?? 0);
$nq = count($quiz['questions'] ?? []);
$tl = isset($quiz['time_limit_sec']) && (int) $quiz['time_limit_sec'] > 0 ? (int) $quiz['time_limit_sec'] : null;
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page student-quiz-take">
                <div class="pro-table-head">
                    <div>
                        <h1><?= htmlspecialchars($quiz['title'] ?? 'Quiz') ?></h1>
                        <p>
                            <?= htmlspecialchars((string) ($quiz['course_title'] ?? '')) ?>
                            <?= !empty($quiz['chapter_title'] ?? '') ? ' · ' . htmlspecialchars((string) ($quiz['chapter_title'] ?? '')) : '' ?>
                        </p>
                    </div>
                    <div class="pro-table-actions">
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=student/quiz">Annuler</a>
                    </div>
                </div>

                <div class="sq-timer" id="sqTimer" aria-live="polite">
                    <strong>Temps restant</strong>
                    <div class="sq-timer-bar" aria-hidden="true"><span id="sqTimerBar"></span></div>
                    <span class="sq-timer-time" id="sqTimerTime">--:--</span>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card" style="padding: 1.2rem;">
                    <div style="display:flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
                        <span class="pro-badge"><?= (int) $nq ?> question(s)</span>
                        <span class="pro-badge"><?= htmlspecialchars(difficulty_label_fr((string) ($quiz['difficulty'] ?? 'beginner'))) ?></span>
                        <?php if ($tl): ?>
                            <span class="pro-badge"><?= (int) $tl ?> s max</span>
                        <?php endif; ?>
                    </div>

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

