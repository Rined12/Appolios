<?php
$teacherSidebarActive = 'exam-builder';
$chapters = isset($chapters) && is_array($chapters) ? $chapters : [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page pro-form-page teacher-quiz-surface">
                <div class="pro-table-head">
                    <div>
                        <h1>Exam Builder (IA)</h1>
                        <p class="pro-table-head-lead">Génère plusieurs quiz automatiquement (par chapitre ou partie), avec aperçu avant création dans votre espace.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz">← Retour à la liste</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card pro-form-card">
                    <div class="pro-form-grid pro-form-grid--exam">
                        <div class="pro-form-field pro-form-field--full">
                            <label for="ex-objective">Objectif (examen)</label>
                            <input id="ex-objective" type="text" class="form-control" maxlength="500" placeholder="Ex. : examen SQL (JOIN, GROUP BY, sous-requêtes) — niveau intermédiaire">
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
                            <input id="ex-quiz-count" type="text" class="form-control" value="3" maxlength="2" inputmode="numeric" aria-describedby="ex-quiz-count-hint">
                            <span id="ex-quiz-count-hint" class="pro-field-micro">1 à 10</span>
                        </div>

                        <div class="pro-form-field">
                            <label for="ex-q-per-quiz">Questions par quiz</label>
                            <input id="ex-q-per-quiz" type="text" class="form-control" value="10" maxlength="2" inputmode="numeric" aria-describedby="ex-q-hint">
                            <span id="ex-q-hint" class="pro-field-micro">3 à 20</span>
                        </div>

                        <div class="pro-form-field pro-form-field--full">
                            <label for="ex-chapters">Chapitres inclus</label>
                            <select id="ex-chapters" class="form-control pro-select-multi" multiple size="6" aria-describedby="ex-chapters-hint">
                                <?php if (empty($chapters)): ?>
                                    <option disabled value="">Aucun chapitre — créez des cours et des chapitres d’abord.</option>
                                <?php endif; ?>
                                <?php foreach ($chapters as $ch): ?>
                                    <option value="<?= (int) $ch['id'] ?>"><?= htmlspecialchars(($ch['course_title'] ?? '') . ' — ' . ($ch['title'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="ex-chapters-hint" class="pro-form-hint">Astuce : Ctrl + clic (Windows) ou ⌘ + clic (Mac) pour en sélectionner plusieurs.</div>
                        </div>
                    </div>

                    <div class="pro-form-actions pro-form-actions--exam">
                        <button type="button" class="btn btn-outline" id="ex-generate">Aperçu IA</button>
                        <div class="pro-form-actions__push" aria-hidden="true"></div>
                        <button type="button" class="btn btn-primary" id="ex-create" disabled>Créer les quiz</button>
                    </div>

                    <div class="pro-preview-panel">
                        <div class="pro-preview-panel__head">
                            <div class="pro-preview-panel__title">Aperçu</div>
                            <div class="pro-preview-panel__meta pro-cell-sub" id="ex-preview-meta"></div>
                        </div>
                        <div id="ex-preview" class="pro-preview-panel__body pro-cell-sub">Aucun aperçu pour le moment. Renseignez l’objectif, choisissez des chapitres, puis lancez « Aperçu IA ».</div>
                    </div>

                    <div class="pro-preview-panel pro-preview-panel--success" id="ex-created" style="display:none;">
                        <div class="pro-preview-panel__title">Création</div>
                        <div id="ex-created-body" class="pro-preview-panel__body pro-cell-sub"></div>
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
                    var previewMeta = document.getElementById('ex-preview-meta');
                    var createdBox = document.getElementById('ex-created');
                    var createdBody = document.getElementById('ex-created-body');
                    if(!objective || !diff || !quizCount || !qPerQuiz || !chapters || !genBtn || !createBtn || !preview) return;

                    var lastPlan = null;
                    var creating = false;
                    var generating = false;

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

                    function setGenerating(on){
                        generating = !!on;
                        genBtn.disabled = generating || creating;
                        createBtn.disabled = creating || generating || !lastPlan;
                        genBtn.textContent = generating ? 'IA…' : 'Aperçu IA';
                    }

                    function setCreating(on){
                        creating = !!on;
                        genBtn.disabled = generating || creating;
                        createBtn.disabled = creating || generating || !lastPlan;
                        createBtn.textContent = creating ? 'Création…' : 'Créer les quiz';
                    }

                    function updateMeta(){
                        if(!previewMeta) return;
                        if(!lastPlan || !Array.isArray(lastPlan.quizzes)){
                            previewMeta.textContent = '';
                            return;
                        }
                        var n = lastPlan.quizzes.length;
                        previewMeta.textContent = n + ' quiz · ' + (generating ? 'génération…' : (creating ? 'création…' : 'prêt'));
                    }

                    function toggleQuestions(idx){
                        var el = document.getElementById('ex-q-list-' + idx);
                        if(!el) return;
                        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
                    }

                    function removeQuiz(idx){
                        if(!lastPlan || !Array.isArray(lastPlan.quizzes)) return;
                        lastPlan.quizzes.splice(idx, 1);
                        renderPlan(lastPlan);
                    }

                    function regenerateQuiz(idx){
                        if(generating || creating) return;
                        if(!lastPlan || !Array.isArray(lastPlan.quizzes) || !lastPlan.quizzes[idx]) return;
                        var qz = lastPlan.quizzes[idx];
                        var obj = String(objective.value || '').trim();
                        if(!obj){
                            alert('Écris un objectif pour l\'examen.');
                            return;
                        }
                        var nQ = normInt(qPerQuiz.value, 10, 3, 20);

                        setGenerating(true);
                        updateMeta();

                        var fd = new FormData();
                        fd.append('objective', obj + ' | Régénérer 1 quiz: ' + String(qz.title || ''));
                        fd.append('difficulty', diff.value || '');
                        fd.append('quiz_count', '1');
                        fd.append('questions_per_quiz', String(nQ));
                        fd.append('chapter_ids[]', String(qz.chapter_id || ''));

                        fetch('<?= APP_ENTRY ?>?url=teacher-quiz/generate-exam-ai', {
                            method: 'POST',
                            body: fd,
                            credentials: 'same-origin'
                        }).then(function(r){ return r.json(); })
                          .then(function(data){
                              if(!data || !data.ok || !data.plan || !Array.isArray(data.plan.quizzes) || !data.plan.quizzes[0]){
                                  alert((data && data.error) ? data.error : 'Erreur IA');
                                  return;
                              }
                              lastPlan.quizzes[idx] = data.plan.quizzes[0];
                              renderPlan(lastPlan);
                          })
                          .catch(function(){ alert('Erreur IA'); })
                          .finally(function(){
                              setGenerating(false);
                              updateMeta();
                          });
                    }

                    window.__ex_toggleQuestions = toggleQuestions;
                    window.__ex_removeQuiz = removeQuiz;
                    window.__ex_regenerateQuiz = regenerateQuiz;

                    function renderPlan(plan){
                        if(!plan || !Array.isArray(plan.quizzes) || plan.quizzes.length === 0){
                            preview.innerHTML = '<div class="pro-cell-sub">Aucun aperçu.</div>';
                            lastPlan = null;
                            setCreating(false);
                            setGenerating(false);
                            if(createdBox) createdBox.style.display = 'none';
                            updateMeta();
                            return;
                        }
                        var html = '';
                        plan.quizzes.forEach(function(qz, i){
                            var qs = Array.isArray(qz.questions) ? qz.questions : [];
                            html += '<div class="pro-preview-quiz-block">' +
                                '<div class="pro-preview-quiz-block__top">' +
                                  '<div class="pro-preview-quiz-block__main">' +
                                    '<div class="pro-preview-quiz-block__title">' + esc(qz.title || ('Quiz ' + (i+1))) + '</div>' +
                                    '<div class="pro-cell-sub" style="margin-top:4px;">Chapitre : ' + esc(qz.chapter_label || '') + '</div>' +
                                    '<div class="pro-cell-sub" style="margin-top:4px;">Difficulté : <strong>' + esc(qz.difficulty || '') + '</strong> · Tags : ' + esc(qz.tags || '') + '</div>' +
                                  '</div>' +
                                  '<div class="pro-preview-quiz-block__side">' +
                                    '<div class="pro-cell-sub">Questions : <strong>' + qs.length + '</strong></div>' +
                                    '<div class="pro-preview-quiz-block__actions">' +
                                      '<button type="button" class="btn btn-outline btn-sm-pro" onclick="window.__ex_toggleQuestions(' + i + ')">Questions</button>' +
                                      '<button type="button" class="btn btn-outline btn-sm-pro" onclick="window.__ex_regenerateQuiz(' + i + ')">Régénérer</button>' +
                                      '<button type="button" class="btn btn-outline btn-sm-pro" onclick="window.__ex_removeQuiz(' + i + ')">Retirer</button>' +
                                    '</div>' +
                                  '</div>' +
                                '</div>' +
                                '<div id="ex-q-list-' + i + '" class="pro-preview-q-list" style="display:none;">' +
                                  (qs.length ? ('<ol style="margin: 0; padding-left: 18px;">' + qs.map(function(x){
                                        var t = String(x.type || 'mcq');
                                        var badge = t === 'tf' ? 'V/F' : 'QCM';
                                        return '<li class="pro-cell-sub" style="margin-bottom: 6px;">' +
                                            '<span class="pro-badge pro-badge--lite" style="margin-right:8px;">' + badge + '</span>' +
                                            esc(x.question || '') +
                                        '</li>';
                                    }).join('') + '</ol>') : '<div class="pro-cell-sub">Aucune question</div>') +
                                '</div>' +
                            '</div>';
                        });
                        preview.innerHTML = html;
                        lastPlan = plan;
                        if(createdBox) createdBox.style.display = 'none';
                        setGenerating(false);
                        setCreating(false);
                        updateMeta();
                    }

                    genBtn.addEventListener('click', function(){
                        if(generating || creating) return;
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

                        setGenerating(true);
                        preview.innerHTML = '<div class="pro-cell-sub">Génération IA…</div>';
                        lastPlan = null;
                        if(createdBox) createdBox.style.display = 'none';
                        updateMeta();

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
                              renderPlan(data.plan || null);
                          })
                          .catch(function(){
                              preview.innerHTML = '<div class="pro-cell-sub">Erreur IA</div>';
                          })
                          .finally(function(){
                              setGenerating(false);
                              updateMeta();
                          });
                    });

                    createBtn.addEventListener('click', function(){
                        if(generating || creating) return;
                        if(!lastPlan){
                            alert('Génère un preview d\'abord.');
                            return;
                        }
                        if(!confirm('Créer les quiz générés ?')) return;
                        setCreating(true);
                        updateMeta();

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
                              var ids = Array.isArray(data.created_ids) ? data.created_ids : [];
                              if(createdBox && createdBody){
                                  createdBox.style.display = 'block';
                                  if(ids.length === 0){
                                      createdBody.innerHTML = 'Création terminée, mais aucun quiz n\'a été créé.';
                                  } else {
                                      createdBody.innerHTML = '<div style="font-weight:900; margin-bottom: 6px;">' + ids.length + ' quiz créé(s)</div>' +
                                          '<div style="display:flex; flex-wrap:wrap; gap: 8px;">' +
                                          ids.map(function(id){
                                              return '<a class="btn btn-outline" style="padding:6px 10px;" href="<?= APP_ENTRY ?>?url=teacher-quiz/edit-quiz/' + id + '">Quiz #' + id + '</a>';
                                          }).join('') +
                                          '</div>' +
                                          '<div style="margin-top: 10px;">' +
                                              '<a class="btn btn-primary" href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz">Retour à la liste</a>' +
                                          '</div>';
                                  }
                              }
                          })
                          .catch(function(){ alert('Erreur'); })
                          .finally(function(){
                              setCreating(false);
                              updateMeta();
                          });
                    });

                    updateMeta();
                })();
                </script>
            </div>
        </div>
    </div>
</div>
