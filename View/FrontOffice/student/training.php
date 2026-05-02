<?php
$studentSidebarActive = 'questions';
$questions = isset($questions) && is_array($questions) ? $questions : [];
$difficulty = isset($difficulty) ? (string) $difficulty : '';
$count = isset($count) ? (int) $count : 10;

$cfgDiff = $difficulty;
if (!in_array($cfgDiff, ['', 'beginner', 'intermediate', 'advanced'], true)) {
    $cfgDiff = '';
}
$count = max(5, min(30, $count));

$payload = [];
foreach ($questions as $q) {
    $opts = isset($q['options']) && is_array($q['options']) ? array_values($q['options']) : [];
    if (count($opts) < 2) {
        continue;
    }
    $ca = isset($q['correct_answer']) ? (int) $q['correct_answer'] : 0;
    if ($ca < 0 || $ca >= count($opts)) {
        $ca = 0;
    }
    $payload[] = [
        'id' => (int) ($q['id'] ?? 0),
        'title' => (string) ($q['title'] ?? 'Question'),
        'question_text' => (string) ($q['question_text'] ?? ''),
        'difficulty' => (string) ($q['difficulty'] ?? 'beginner'),
        'options' => $opts,
        'correct_answer' => $ca,
    ];
}
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page student-training-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Training Lab</h1>
                        <p>Mode focus : entraînement adaptatif (score, streak, difficulté).</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student/questions-bank" class="btn btn-outline">Retour</a>
                        <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-outline">Quiz</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card" style="padding: 1.2rem;">
                    <div class="training-top">
                        <div class="training-config">
                            <div class="training-kpi">
                                <div class="training-kpi-label">Score</div>
                                <div class="training-kpi-value" id="trScore">0</div>
                            </div>
                            <div class="training-kpi">
                                <div class="training-kpi-label">Streak</div>
                                <div class="training-kpi-value" id="trStreak">0</div>
                            </div>
                            <div class="training-kpi">
                                <div class="training-kpi-label">Niveau</div>
                                <div class="training-kpi-value" id="trLevel">Auto</div>
                            </div>
                        </div>
                        <div class="training-actions">
                            <button class="btn btn-training-pro" type="button" id="trStart">
                                <i class="bi bi-play-fill" aria-hidden="true"></i>
                                Démarrer
                                <span class="btn-training-pro-badge">GO</span>
                            </button>
                            <button class="btn btn-outline" type="button" id="trReset">Reset</button>
                        </div>
                    </div>

                    <div class="training-panel" id="trPanel" style="display:none;">
                        <div class="training-progress">
                            <div class="training-progress-text">
                                <span id="trPos">0</span>/<span id="trTotal">0</span>
                            </div>
                            <div class="training-progress-bar" aria-hidden="true"><span id="trBar"></span></div>
                        </div>

                        <div class="training-card">
                            <div class="training-meta">
                                <span class="training-pill" id="trDiff">beginner</span>
                                <span class="training-pill training-pill--muted" id="trId">#</span>
                            </div>
                            <div class="training-title" id="trTitle"></div>
                            <div class="training-question" id="trQuestion"></div>

                            <div class="training-options" id="trOptions"></div>

                            <div class="training-footer">
                                <div class="training-feedback" id="trFeedback"></div>
                                <div class="training-footer-actions">
                                    <button class="btn btn-primary" type="button" id="trValidate" disabled>Valider</button>
                                    <button class="btn btn-outline" type="button" id="trNext" disabled>Suivant</button>
                                </div>
                            </div>
                        </div>

                        <div class="training-summary" id="trSummary" style="display:none;"></div>
                    </div>

                    <div class="training-empty" id="trEmpty" style="display:none;">
                        Pas assez de questions disponibles pour démarrer.
                    </div>

                    <script>
                    (function () {
                      var BANK = <?php echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                      var MAX_COUNT = <?php echo (int) $count; ?>;
                      var CFG_DIFF = <?php echo json_encode($cfgDiff, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

                      var elScore = document.getElementById('trScore');
                      var elStreak = document.getElementById('trStreak');
                      var elLevel = document.getElementById('trLevel');
                      var elStart = document.getElementById('trStart');
                      var elReset = document.getElementById('trReset');
                      var panel = document.getElementById('trPanel');
                      var empty = document.getElementById('trEmpty');

                      var elPos = document.getElementById('trPos');
                      var elTotal = document.getElementById('trTotal');
                      var elBar = document.getElementById('trBar');

                      var elDiff = document.getElementById('trDiff');
                      var elId = document.getElementById('trId');
                      var elTitle = document.getElementById('trTitle');
                      var elQuestion = document.getElementById('trQuestion');
                      var elOptions = document.getElementById('trOptions');
                      var elFeedback = document.getElementById('trFeedback');
                      var elValidate = document.getElementById('trValidate');
                      var elNext = document.getElementById('trNext');
                      var elSummary = document.getElementById('trSummary');

                      function shuffle(arr) {
                        for (var i = arr.length - 1; i > 0; i--) {
                          var j = Math.floor(Math.random() * (i + 1));
                          var t = arr[i];
                          arr[i] = arr[j];
                          arr[j] = t;
                        }
                        return arr;
                      }

                      function normDiff(d) {
                        d = String(d || '').toLowerCase();
                        if (d === 'advanced') return 'advanced';
                        if (d === 'intermediate') return 'intermediate';
                        return 'beginner';
                      }

                      function diffRank(d) {
                        d = normDiff(d);
                        if (d === 'intermediate') return 2;
                        if (d === 'advanced') return 3;
                        return 1;
                      }

                      function pickPool(levelRank) {
                        var list = BANK.slice();
                        if (CFG_DIFF) {
                          list = list.filter(function (q) { return normDiff(q.difficulty) === CFG_DIFF; });
                        } else {
                          list = list.filter(function (q) {
                            return diffRank(q.difficulty) <= levelRank;
                          });
                        }
                        return list;
                      }

                      var state = {
                        started: false,
                        list: [],
                        i: 0,
                        score: 0,
                        streak: 0,
                        answered: false,
                        selected: null,
                        levelRank: 1,
                        correct: 0,
                        answeredCount: 0,
                        totalQuestions: 0
                      };

                      function reset() {
                        state.started = false;
                        state.list = [];
                        state.i = 0;
                        state.score = 0;
                        state.streak = 0;
                        state.answered = false;
                        state.selected = null;
                        state.levelRank = 1;
                        state.correct = 0;
                        state.answeredCount = 0;
                        state.totalQuestions = 0;

                        elScore.textContent = '0';
                        elStreak.textContent = '0';
                        elLevel.textContent = CFG_DIFF ? CFG_DIFF : 'Auto';
                        elFeedback.textContent = '';
                        elSummary.style.display = 'none';
                        elSummary.innerHTML = '';
                        panel.style.display = 'none';
                        empty.style.display = 'none';
                        elValidate.disabled = true;
                        elNext.disabled = true;
                      }

                      function updateProgress() {
                        elPos.textContent = String(Math.min(state.answeredCount + 1, state.totalQuestions));
                        elTotal.textContent = String(state.totalQuestions);
                        var pct = state.totalQuestions > 0 ? Math.round(((state.answeredCount) / state.totalQuestions) * 100) : 0;
                        elBar.style.width = Math.max(0, Math.min(100, pct)) + '%';
                      }

                      function renderQuestion() {
                        if (!state.started || state.i >= state.list.length) {
                          finish();
                          return;
                        }
                        var q = state.list[state.i];
                        state.answered = false;
                        state.selected = null;

                        elDiff.textContent = normDiff(q.difficulty);
                        elId.textContent = '#' + String(q.id || '');
                        elTitle.textContent = String(q.title || 'Question');
                        elQuestion.textContent = String(q.question_text || '');
                        elFeedback.textContent = '';
                        elFeedback.className = 'training-feedback';

                        elOptions.innerHTML = '';
                        (q.options || []).forEach(function (opt, idx) {
                          var b = document.createElement('button');
                          b.type = 'button';
                          b.className = 'training-option';
                          b.textContent = (idx + 1) + '. ' + String(opt);
                          b.addEventListener('click', function () {
                            if (state.answered) return;
                            state.selected = idx;
                            Array.from(elOptions.querySelectorAll('.training-option')).forEach(function (x) { x.classList.remove('is-selected'); });
                            b.classList.add('is-selected');
                            elValidate.disabled = false;
                          });
                          elOptions.appendChild(b);
                        });

                        elValidate.disabled = true;
                        elNext.disabled = true;
                        updateProgress();
                      }

                      function applyAdaptive(isCorrect) {
                        if (CFG_DIFF) {
                          elLevel.textContent = CFG_DIFF;
                          return;
                        }
                        if (isCorrect) {
                          if (state.streak >= 3 && state.levelRank < 3) {
                            state.levelRank++;
                            state.streak = 0;
                          }
                        } else {
                          if (state.levelRank > 1) {
                            state.levelRank--;
                          }
                          state.streak = 0;
                        }
                        elLevel.textContent = (state.levelRank === 1 ? 'beginner' : (state.levelRank === 2 ? 'intermediate' : 'advanced'));
                      }

                      function validate() {
                        if (!state.started || state.answered) return;
                        var q = state.list[state.i];
                        if (state.selected == null) return;

                        state.answered = true;
                        state.answeredCount++;

                        var correct = (state.selected === q.correct_answer);
                        if (correct) {
                          state.correct++;
                          state.score += 10;
                          state.streak++;
                          elFeedback.textContent = 'Correct. +10';
                          elFeedback.classList.add('is-ok');
                        } else {
                          state.score += 2;
                          elFeedback.textContent = 'Incorrect. Bonne réponse: ' + (q.correct_answer + 1);
                          elFeedback.classList.add('is-bad');
                        }

                        elScore.textContent = String(state.score);
                        elStreak.textContent = String(state.streak);

                        Array.from(elOptions.querySelectorAll('.training-option')).forEach(function (btn, idx) {
                          btn.disabled = true;
                          if (idx === q.correct_answer) btn.classList.add('is-correct');
                          if (idx === state.selected && !correct) btn.classList.add('is-wrong');
                        });

                        applyAdaptive(correct);

                        updateProgress();

                        elValidate.disabled = true;
                        elNext.disabled = false;
                      }

                      function next() {
                        if (!state.started) return;
                        if (!state.answered) return;
                        state.i++;
                        renderQuestion();
                      }

                      function finish() {
                        panel.style.display = 'block';
                        elSummary.style.display = 'block';
                        elBar.style.width = '100%';
                        elPos.textContent = String(state.totalQuestions);
                        elTotal.textContent = String(state.totalQuestions);

                        var acc = state.answeredCount > 0 ? Math.round((state.correct / state.answeredCount) * 100) : 0;
                        elSummary.innerHTML = '';
                        var box = document.createElement('div');
                        box.className = 'training-summary-box';
                        box.innerHTML = '<div class="training-summary-title">Résumé session</div>' +
                          '<div class="training-summary-line"><strong>Score</strong> : ' + state.score + '</div>' +
                          '<div class="training-summary-line"><strong>Précision</strong> : ' + acc + '%</div>' +
                          '<div class="training-summary-line"><strong>Questions</strong> : ' + state.answeredCount + '</div>';
                        elSummary.appendChild(box);

                        elOptions.innerHTML = '';
                        elTitle.textContent = 'Session terminée';
                        elQuestion.textContent = 'Relance une session pour continuer à progresser.';
                        elFeedback.textContent = '';
                        elValidate.disabled = true;
                        elNext.disabled = true;
                      }

                      function start() {
                        reset();
                        var list = pickPool(state.levelRank);
                        if (!list.length) {
                          empty.style.display = 'block';
                          return;
                        }
                        shuffle(list);
                        state.started = true;
                        state.list = list.slice(0, Math.min(MAX_COUNT, list.length));
                        state.i = 0;
                        state.score = 0;
                        state.streak = 0;
                        state.answered = false;
                        state.selected = null;
                        state.correct = 0;
                        state.answeredCount = 0;
                        state.totalQuestions = state.list.length;

                        panel.style.display = 'block';
                        elSummary.style.display = 'none';
                        elLevel.textContent = CFG_DIFF ? CFG_DIFF : 'beginner';
                        renderQuestion();
                      }

                      elStart.addEventListener('click', start);
                      elReset.addEventListener('click', reset);
                      elValidate.addEventListener('click', validate);
                      elNext.addEventListener('click', next);

                      reset();
                    })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
