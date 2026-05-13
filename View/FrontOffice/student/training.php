<?php
/**
 * APPOLIOS - Student Training Mode
 */

$studentSidebarActive = 'questions-bank';
$userName = $_SESSION['user_name'] ?? 'Student';
?>

<style>
/* Premium Training UI Styling */
.student-learning-page .pro-table-card {
    background: rgba(30, 41, 59, 0.4) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 20px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    padding: 2rem !important;
}

.training-hero {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(139, 92, 246, 0.15)) !important;
    border: 1px solid rgba(139, 92, 246, 0.3) !important;
    box-shadow: 0 10px 30px rgba(139, 92, 246, 0.2) !important;
    padding: 24px !important;
    border-radius: 24px !important;
    margin-bottom: 2rem !important;
}

.training-hero h1 {
    font-size: 2.2rem !important;
    font-weight: 900 !important;
    background: linear-gradient(to right, #3b82f6, #06b6d4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px !important;
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.training-hero p {
    color: #94a3b8 !important;
    font-size: 1.05rem !important;
    margin: 0;
}

.training-controls {
    background: rgba(15, 23, 42, 0.6) !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    border-radius: 16px !important;
    padding: 1.5rem !important;
    margin-bottom: 2rem !important;
}

.training-controls label {
    color: #94a3b8 !important;
}

.training-controls select {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #f8fafc !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
}

.training-controls select option {
    background: #1e293b !important;
    color: #f8fafc !important;
}

.btn-training-load {
    background: linear-gradient(135deg, #3b82f6, #06b6d4) !important;
    border: none !important;
    color: #ffffff !important;
    border-radius: 12px !important;
    padding: 12px 24px !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3) !important;
    transition: all 0.3s ease !important;
}
.btn-training-load:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5) !important;
}

.training-question-card {
    background: rgba(15, 23, 42, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    border-radius: 16px !important;
    padding: 24px !important;
    transition: all 0.3s ease;
}
.training-question-card:hover {
    background: rgba(15, 23, 42, 0.5) !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
}

.tq-qnum {
    color: #3b82f6 !important;
    font-size: 1.1rem !important;
}

.tq-difficulty {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #cbd5e1 !important;
}

.tq-text {
    color: #f1f5f9 !important;
}

.tq-option {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #cbd5e1 !important;
    padding: 16px 20px !important;
    border-radius: 12px !important;
    transition: all 0.2s ease !important;
}
.tq-option:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}
.tq-letter {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #f8fafc !important;
}
</style>

<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main pro-table-page">
                
                <div class="training-hero">
                    <h1>Practice Mode ✨</h1>
                    <p>Test your knowledge with randomized questions without affecting your ranking.</p>
                </div>

                <div class="pro-table-card">
                        
                        <div class="training-controls">
                            <form action="<?= APP_ENTRY ?>" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                                <input type="hidden" name="url" value="student/training">
                                
                                <div style="flex: 1; min-width: 200px;">
                                    <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">Select Difficulty</label>
                                    <select name="difficulty" style="width: 100%;">
                                        <option value="" <?= empty($difficulty) ? 'selected' : '' ?>>All Difficulties</option>
                                        <option value="beginner" <?= $difficulty === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                                        <option value="intermediate" <?= $difficulty === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                        <option value="advanced" <?= $difficulty === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                                    </select>
                                </div>
                                
                                <div style="flex: 1; min-width: 200px;">
                                    <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">Number of Questions</label>
                                    <select name="count" style="width: 100%;">
                                        <option value="5" <?= $count == 5 ? 'selected' : '' ?>>5 Questions</option>
                                        <option value="10" <?= $count == 10 ? 'selected' : '' ?>>10 Questions</option>
                                        <option value="20" <?= $count == 20 ? 'selected' : '' ?>>20 Questions</option>
                                        <option value="30" <?= $count == 30 ? 'selected' : '' ?>>30 Questions</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn-training-load">Load Questions</button>
                            </form>
                        </div>

                        <?php if (!empty($questions)): ?>
                            <div class="training-questions" style="display: flex; flex-direction: column; gap: 2rem;">
                                <?php foreach ($questions as $index => $q): ?>
                                    <div class="training-question-card">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                            <span class="tq-qnum" style="font-weight: 800;">Question <?= $index + 1 ?></span>
                                            <span class="tq-difficulty" style="font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;"><?= htmlspecialchars($q['difficulty']) ?></span>
                                        </div>
                                        
                                        <h3 class="tq-text" style="margin: 0 0 1.5rem 0; font-size: 1.2rem; line-height: 1.5;"><?= htmlspecialchars($q['question_text']) ?></h3>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.8rem;" class="options-container">
                                            <?php foreach ($q['options'] as $oIdx => $opt): ?>
                                                <button class="tq-option" onclick="revealAnswer(this, <?= $oIdx === (int)$q['correct_answer'] ? 'true' : 'false' ?>)" style="text-align: left; font-size: 1rem; font-family: inherit; font-weight: 600; cursor: pointer;">
                                                    <span class="tq-letter" style="display: inline-block; width: 24px; height: 24px; border-radius: 6px; text-align: center; line-height: 24px; margin-right: 12px; font-size: 0.85rem;"><?= chr(65 + $oIdx) ?></span>
                                                    <?= htmlspecialchars($opt) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="feedback-msg" style="display: none; margin-top: 1.5rem; padding: 1rem; border-radius: 10px; font-weight: 600;"></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <script>
                                function revealAnswer(btn, isCorrect) {
                                    const container = btn.closest('.options-container');
                                    const card = btn.closest('.training-question-card');
                                    const feedback = card.querySelector('.feedback-msg');
                                    
                                    // Disable all buttons in this question
                                    container.querySelectorAll('button').forEach(b => {
                                        b.disabled = true;
                                        b.style.cursor = 'default';
                                        
                                        // If it's the correct answer, highlight it green
                                        if (b.getAttribute('onclick').includes('true')) {
                                            b.style.borderColor = 'rgba(34, 197, 94, 0.5)';
                                            b.style.background = 'rgba(34, 197, 94, 0.1)';
                                            b.style.color = '#4ade80';
                                        }
                                    });
                                    
                                    if (isCorrect) {
                                        feedback.style.display = 'block';
                                        feedback.style.background = 'rgba(34, 197, 94, 0.1)';
                                        feedback.style.color = '#4ade80';
                                        feedback.style.border = '1px solid rgba(34, 197, 94, 0.3)';
                                        feedback.textContent = '✅ Correct ! Bonne réponse.';
                                    } else {
                                        btn.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                                        btn.style.background = 'rgba(239, 68, 68, 0.1)';
                                        btn.style.color = '#f87171';
                                        
                                        feedback.style.display = 'block';
                                        feedback.style.background = 'rgba(239, 68, 68, 0.1)';
                                        feedback.style.color = '#f87171';
                                        feedback.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                                        feedback.textContent = '❌ Incorrect ! Revoyez la leçon pour mieux comprendre.';
                                    }
                                }
                            </script>
                        <?php else: ?>
                            <div style="text-align: center; padding: 3rem 1rem; background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.1); border-radius: 12px;">
                                <p style="color: #94a3b8; margin: 0; font-size: 1rem;">Aucune question trouvée pour ces critères.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
