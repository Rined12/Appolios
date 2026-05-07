<?php
$teacherSidebarActive = 'quiz';
$chapters = isset($chapters) && is_array($chapters) ? $chapters : [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page pro-form-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Exam Builder (IA)</h1>
                        <p>Génère plusieurs quiz automatiquement (par chapitre/partie) avec preview avant création.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz">← Retour à la liste</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card pro-form-card">
                    <div class="pro-form-grid">
                        <div class="pro-form-field pro-form-field--full">
                            <label for="ex-objective">Objectif (examen)</label>
                            <input id="ex-objective" type="text" class="form-control" maxlength="500" placeholder="Ex: Examen SQL (JOIN, GROUP BY, sous-requêtes) — niveau intermédiaire">
                        </div>

                        <div class="pro-form-field">
                            <label for="ex-difficulty">Difficulté</label>
                            <select id="ex-difficulty" class="form-control">
                                <option value="">Auto</option>
                                <option value="beginner">Débutant</option>
                                <option value="intermediate" selected>Intermédiaire</option>
                                <option value="advanced">Avancé</option>
                            </select>
                        </div>

                        <div class="pro-form-field">
                            <label for="ex-quiz-count">Nombre de quiz</label>
                            <input id="ex-quiz-count" type="text" class="form-control" value="3" maxlength="2">
                        </div>

                        <div class="pro-form-field">
                            <label for="ex-q-per-quiz">Questions / quiz</label>
                            <input id="ex-q-per-quiz" type="text" class="form-control" value="10" maxlength="2">
                        </div>

                        <div class="pro-form-field pro-form-field--full">
                            <label for="ex-chapters">Chapitres inclus</label>
                            <select id="ex-chapters" class="form-control" multiple style="min-height: 140px;">
                                <?php foreach ($chapters as $ch): ?>
                                    <option value="<?= (int) $ch['id'] ?>"><?= htmlspecialchars(($ch['course_title'] ?? '') . ' — ' . ($ch['title'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pro-form-hint">Astuce: Ctrl+click pour sélectionner plusieurs chapitres.</div>
                        </div>
                    </div>

                    <div class="pro-form-actions">
                        <button type="button" class="btn btn-outline" id="ex-generate">Générer preview (IA)</button>
                        <button type="button" class="btn btn-primary" id="ex-create" disabled>Créer les quiz</button>
                    </div>

                    <div class="pro-table-card" style="padding: 12px; margin-top: 12px; background: rgba(255,255,255,.03);">
                        <div style="font-weight: 950; margin-bottom: 8px;">Preview</div>
                        <div id="ex-preview" class="pro-cell-sub">Aucun preview.</div>
                    </div>
                </div>

                <script>
                (function(){
                    var objective = document.getElementById('ex-objective');
                    var diff = document.getElementById('ex-difficulty');
                    var quizCount = document.getElementById('ex-quiz-count');
                    var qPerQuiz = document.getElementById('ex-q-per-quiz');
                    var chapters = document.getElementById('ex-chapters');
                    var genBtn = document.getElementById('ex-generate');
                    var createBtn = document.getElementById('ex-create');
                    var preview = document.getElementById('ex-preview');
                    if(!objective || !diff || !quizCount || !qPerQuiz || !chapters || !genBtn || !createBtn || !preview) return;

                    var lastPlan = null;

                    function esc(s){
                        s = (s == null ? '' : String(s));
                        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
                    }

                    function normInt(v, d, min, max){
                        v = String(v||'').trim();
                        if(!/^\d+$/.test(v)) return d;
                        var n = parseInt(v, 10);
                        if(n < min) n = min;
                        if(n > max) n = max;
                        return n;
                    }

                    function selectedChapterIds(){
                        return Array.from(chapters.options).filter(function(o){ return o.selected; }).map(function(o){ return o.value; });
                    }

                    function setLoading(on){
                        genBtn.disabled = !!on;
                        genBtn.textContent = on ? 'IA…' : 'Générer preview (IA)';
                    }

                    function renderPlan(plan){
                        if(!plan || !Array.isArray(plan.quizzes) || plan.quizzes.length === 0){
                            preview.innerHTML = '<div class="pro-cell-sub">Preview vide.</div>';
                            createBtn.disabled = true;
                            return;
                        }
                        var html = '';
                        plan.quizzes.forEach(function(qz, i){
                            var qs = Array.isArray(qz.questions) ? qz.questions : [];
                            html += '<div style="padding: 12px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; margin-bottom: 10px; background: rgba(2,6,23,.20);">' +
                                '<div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">' +
                                  '<div><div style="font-weight:900;">' + esc(qz.title || ('Quiz ' + (i+1))) + '</div>' +
                                  '<div class="pro-cell-sub">Chapitre: ' + esc(qz.chapter_label || '') + ' · Diff: ' + esc(qz.difficulty || '') + ' · Tags: ' + esc(qz.tags || '') + '</div></div>' +
                                  '<div class="pro-cell-sub">Questions: ' + qs.length + '</div>' +
                                '</div>' +
                                (qs.length ? ('<ol style="margin: 8px 0 0; padding-left: 18px;">' + qs.map(function(x){ return '<li class="pro-cell-sub">' + esc(x.question || '') + '</li>'; }).join('') + '</ol>') : '') +
                            '</div>';
                        });
                        preview.innerHTML = html;
                        createBtn.disabled = false;
                    }

                    genBtn.addEventListener('click', function(){
                        var obj = String(objective.value || '').trim();
                        if(!obj){
                            alert('Écris un objectif pour l\'examen.');
                            return;
                        }
                        var chIds = selectedChapterIds();
                        if(chIds.length === 0){
                            alert('Sélectionne au moins un chapitre.');
                            return;
                        }
                        var nQuiz = normInt(quizCount.value, 3, 1, 10);
                        var nQ = normInt(qPerQuiz.value, 10, 3, 20);
                        quizCount.value = String(nQuiz);
                        qPerQuiz.value = String(nQ);

                        setLoading(true);
                        createBtn.disabled = true;
                        preview.innerHTML = '<div class="pro-cell-sub">Génération IA…</div>';
                        lastPlan = null;

                        var fd = new FormData();
                        fd.append('objective', obj);
                        fd.append('difficulty', diff.value || '');
                        fd.append('quiz_count', String(nQuiz));
                        fd.append('questions_per_quiz', String(nQ));
                        chIds.forEach(function(id){ fd.append('chapter_ids[]', id); });

                        fetch('<?= APP_ENTRY ?>?url=teacher-quiz/generate-exam-ai', {
                            method: 'POST',
                            body: fd,
                            credentials: 'same-origin'
                        }).then(function(r){ return r.json(); })
                          .then(function(data){
                              if(!data || !data.ok){
                                  alert((data && data.error) ? data.error : 'Erreur IA');
                                  preview.innerHTML = '<div class="pro-cell-sub">Erreur IA</div>';
                                  return;
                              }
                              lastPlan = data.plan || null;
                              renderPlan(lastPlan);
                          })
                          .catch(function(){
                              preview.innerHTML = '<div class="pro-cell-sub">Erreur IA</div>';
                          })
                          .finally(function(){
                              setLoading(false);
                          });
                    });

                    createBtn.addEventListener('click', function(){
                        if(!lastPlan){
                            alert('Génère un preview d\'abord.');
                            return;
                        }
                        if(!confirm('Créer les quiz générés ?')) return;
                        createBtn.disabled = true;
                        createBtn.textContent = 'Création…';

                        var fd = new FormData();
                        fd.append('plan_json', JSON.stringify(lastPlan));

                        fetch('<?= APP_ENTRY ?>?url=teacher-quiz/store-exam-ai', {
                            method: 'POST',
                            body: fd,
                            credentials: 'same-origin'
                        }).then(function(r){ return r.json(); })
                          .then(function(data){
                              if(!data || !data.ok){
                                  alert((data && data.error) ? data.error : 'Erreur');
                                  return;
                              }
                              window.location.href = '<?= APP_ENTRY ?>?url=teacher-quiz/quiz';
                          })
                          .catch(function(){ alert('Erreur'); })
                          .finally(function(){
                              createBtn.disabled = false;
                              createBtn.textContent = 'Créer les quiz';
                          });
                    });
                })();
                </script>
            </div>
        </div>
    </div>
</div>
