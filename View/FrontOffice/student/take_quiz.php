<?php
$studentSidebarActive = 'quiz';
$quizId = (int) ($quiz['id'] ?? 0);
$nq = count($quiz['questions'] ?? []);
$tl = isset($quiz['time_limit_sec']) && (int) $quiz['time_limit_sec'] > 0 ? (int) $quiz['time_limit_sec'] : null;
?>
<style>
    .student-quiz-take { max-width: 920px; margin: 0 auto; }
    .student-quiz-take .sq-breadcrumb {
        display: flex; flex-wrap: wrap; align-items: center; gap: 0.35rem;
        font-size: 0.88rem; font-weight: 600; color: #5b6f8a; margin: 0 0 0.75rem;
    }
    .student-quiz-take .sq-breadcrumb a {
        color: #2f6fed; text-decoration: none;
    }
    .student-quiz-take .sq-breadcrumb a:hover { text-decoration: underline; }
    .student-quiz-take .sq-hero {
        background: linear-gradient(140deg, #ffffff 0%, #f6f9ff 55%, #eef5ff 100%);
        border: 1px solid #d9e5f4;
        border-radius: 18px;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 12px 28px rgba(19, 43, 79, 0.1);
        margin-bottom: 1.25rem;
    }
    .student-quiz-take .sq-title {
        margin: 0 0 0.65rem;
        font-size: clamp(1.55rem, 2.4vw, 2.05rem);
        font-weight: 900;
        letter-spacing: -0.02em;
        color: #173b6d;
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
        border: 1px solid #d0e3fa;
        background: #eaf4ff;
        color: #24538d;
    }
    .student-quiz-take .sq-pill--muted {
        border-color: #dfe7f2;
        background: #f8fbff;
        color: #4d617c;
    }
    .student-quiz-take .sq-form {
        background: #ffffff;
        border: 1px solid #d9e5f4;
        border-radius: 18px;
        padding: 1.35rem 1.4rem 1.5rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
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
        text-transform: uppercase; color: #5b6f8a;
    }
    .student-quiz-take .sq-qtext {
        margin: 0 0 1rem;
        font-size: 1.05rem; font-weight: 600; color: #1f3555;
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
        border: 1px solid #dbe8f7;
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        cursor: pointer;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }
    .student-quiz-take .sq-option:hover {
        border-color: #9ec4f5;
        box-shadow: 0 6px 16px rgba(47, 111, 237, 0.12);
        transform: translateY(-1px);
    }
    .student-quiz-take .sq-option input {
        position: absolute; opacity: 0; width: 0; height: 0;
    }
    .student-quiz-take .sq-option .sq-radio {
        flex-shrink: 0;
        width: 20px; height: 20px; margin-top: 2px;
        border-radius: 50%;
        border: 2px solid #9db7d8;
        background: #fff;
        position: relative;
    }
    .student-quiz-take .sq-option .sq-radio::after {
        content: ""; position: absolute; inset: 4px;
        border-radius: 50%; background: #2f6fed;
        opacity: 0; transform: scale(0.6);
        transition: opacity 0.15s ease, transform 0.15s ease;
    }
    .student-quiz-take .sq-option input:focus-visible + .sq-radio {
        outline: 2px solid #2f6fed; outline-offset: 2px;
    }
    .student-quiz-take .sq-option input:checked + .sq-radio {
        border-color: #2f6fed;
    }
    .student-quiz-take .sq-option input:checked + .sq-radio::after {
        opacity: 1; transform: scale(1);
    }
    .student-quiz-take .sq-option input:checked ~ .sq-label {
        color: #173b6d;
    }
    .student-quiz-take .sq-option--selected {
        border-color: #7fbfff !important;
        box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.12) !important;
        background: linear-gradient(180deg, #f5f9ff 0%, #ffffff 100%) !important;
    }
    .student-quiz-take .sq-label {
        flex: 1; min-width: 0;
        font-size: 0.95rem; font-weight: 600; color: #2d3f5a;
        line-height: 1.45;
    }
    .student-quiz-take .sq-actions {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e5eef9;
        display: flex; flex-wrap: wrap; gap: 0.65rem; align-items: center;
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
                        <span class="sq-pill" role="listitem"><?= htmlspecialchars(Quiz::difficultyLabelFr($quiz['difficulty'] ?? 'beginner')) ?></span>
                        <?php if ($tl): ?>
                            <span class="sq-pill sq-pill--muted" role="listitem">Temps indicatif : <?= $tl ?> s</span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <form method="post" action="<?= APP_ENTRY ?>?url=student/submit-quiz/<?= $quizId ?>" class="sq-form" onsubmit="return appValidateTakeQuiz(this, <?= (int) $nq ?>);">
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
<script>
function appValidateTakeQuiz(f, n) {
  for (var i = 0; i < n; i++) {
    var name = 'answers[' + i + ']';
    var radios = f[name];
    var ok = false;
    if (!radios) { alert('Erreur de formulaire.'); return false; }
    if (radios.length) {
      for (var j = 0; j < radios.length; j++) {
        if (radios[j].checked) ok = true;
      }
    } else {
      if (radios.checked) ok = true;
    }
    if (!ok) {
      alert('Veuillez répondre à toutes les questions avant d’envoyer.');
      return false;
    }
  }
  return true;
}

document.addEventListener('DOMContentLoaded', function () {
  var root = document.querySelector('.student-quiz-take');
  if (!root) return;
  root.querySelectorAll('.sq-question').forEach(function (fs) {
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
      if (input.checked) opt.classList.add('sq-option--selected');
    });
  });
});
</script>

