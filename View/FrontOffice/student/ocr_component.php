<!-- ============================================================
     COMPOSANT OCR — Image vers Texte (Tesseract.js)
     Intégrable dans n'importe quelle vue PHP/MVC
     ============================================================ -->
<style>
  /* ── Fonts ──────────────────────────────────────────────── */
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lora:ital,wght@0,400;0,500;1,400&display=swap');

  /* ── Root tokens ─────────────────────────────────────────── */
  .ocr-wrap {
    --ocr-cream: #fdf6ec;
    --ocr-warm: #f5e6d0;
    --ocr-border: #d4b896;
    --ocr-brown: #7c5c3e;
    --ocr-dark: #4a3728;
    --ocr-wine: #8b2635;
    --ocr-wine-h: #a63040;
    --ocr-gold: #c9973b;
    --ocr-shadow: rgba(74, 55, 40, .15);
    --ocr-radius: 16px;
    font-family: 'Lora', Georgia, serif;
  }

  /* ── Wrapper ─────────────────────────────────────────────── */
  .ocr-wrap {
    background: linear-gradient(135deg, var(--ocr-cream) 0%, var(--ocr-warm) 100%);
    border: 2px solid var(--ocr-border);
    border-radius: var(--ocr-radius);
    box-shadow: 0 8px 32px var(--ocr-shadow), inset 0 1px 0 rgba(255, 255, 255, .6);
    padding: 28px 32px 32px;
    max-width: 100%;
    margin: 16px 0;
    position: relative;
    overflow: hidden;
  }

  .ocr-wrap::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--ocr-wine), var(--ocr-gold), var(--ocr-wine));
  }

  /* ── Header ──────────────────────────────────────────────── */
  .ocr-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
  }

  .ocr-header .ocr-icon {
    font-size: 2rem;
    line-height: 1;
    filter: drop-shadow(0 2px 4px var(--ocr-shadow));
  }

  .ocr-header h3 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.35rem;
    font-weight: 600;
    color: var(--ocr-dark);
    margin: 0;
    letter-spacing: .3px;
  }

  .ocr-header p {
    font-size: .8rem;
    color: var(--ocr-brown);
    margin: 2px 0 0;
    font-style: italic;
  }

  /* ── Drop Zone ───────────────────────────────────────────── */
  .ocr-dropzone {
    border: 2.5px dashed var(--ocr-border);
    border-radius: var(--ocr-radius);
    background: rgba(255, 255, 255, .5);
    padding: 32px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .25s ease;
    position: relative;
  }

  .ocr-dropzone:hover,
  .ocr-dropzone.drag-over {
    border-color: var(--ocr-wine);
    background: rgba(139, 38, 53, .05);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px var(--ocr-shadow);
  }

  .ocr-dropzone input[type=file] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }

  .ocr-dropzone-icon {
    font-size: 2.8rem;
    margin-bottom: 10px;
  }

  .ocr-dropzone-text {
    font-family: 'Playfair Display', serif;
    font-size: 1rem;
    color: var(--ocr-brown);
    font-weight: 600;
  }

  .ocr-dropzone-sub {
    font-size: .75rem;
    color: var(--ocr-border);
    margin-top: 4px;
    font-style: italic;
  }

  /* ── Preview ─────────────────────────────────────────────── */
  .ocr-preview-wrap {
    display: none;
    margin-top: 18px;
    text-align: center;
  }

  .ocr-preview-wrap.show {
    display: block;
  }

  .ocr-preview-wrap img {
    max-width: 100%;
    max-height: 220px;
    border-radius: 10px;
    border: 2px solid var(--ocr-border);
    box-shadow: 0 4px 16px var(--ocr-shadow);
    object-fit: contain;
  }

  .ocr-preview-name {
    font-size: .75rem;
    color: var(--ocr-brown);
    margin-top: 6px;
    font-style: italic;
  }

  /* ── Progress ────────────────────────────────────────────── */
  .ocr-progress-wrap {
    display: none;
    margin-top: 16px;
  }

  .ocr-progress-wrap.show {
    display: block;
  }

  .ocr-progress-label {
    font-size: .78rem;
    color: var(--ocr-brown);
    margin-bottom: 6px;
    display: flex;
    justify-content: space-between;
  }

  .ocr-progress-bar-bg {
    background: var(--ocr-warm);
    border: 1px solid var(--ocr-border);
    border-radius: 50px;
    height: 10px;
    overflow: hidden;
  }

  .ocr-progress-bar {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--ocr-wine), var(--ocr-gold));
    border-radius: 50px;
    transition: width .3s ease;
  }

  /* ── Result textarea ─────────────────────────────────────── */
  .ocr-result-wrap {
    display: none;
    margin-top: 18px;
  }

  .ocr-result-wrap.show {
    display: block;
  }

  .ocr-result-label {
    font-family: 'Playfair Display', serif;
    font-size: .85rem;
    color: var(--ocr-dark);
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .ocr-result-textarea {
    width: 100%;
    min-height: 120px;
    background: rgba(255, 255, 255, .7);
    border: 1.5px solid var(--ocr-border);
    border-radius: 10px;
    padding: 12px 14px;
    font-family: 'Lora', Georgia, serif;
    font-size: .9rem;
    color: var(--ocr-dark);
    line-height: 1.65;
    resize: vertical;
    transition: border-color .2s;
    box-sizing: border-box;
  }

  .ocr-result-textarea:focus {
    outline: none;
    border-color: var(--ocr-wine);
    box-shadow: 0 0 0 3px rgba(139, 38, 53, .1);
  }

  /* ── Buttons ─────────────────────────────────────────────── */
  .ocr-btn-row {
    display: flex;
    gap: 12px;
    margin-top: 18px;
    flex-wrap: wrap;
  }

  .ocr-btn {
    padding: 10px 22px;
    border-radius: 50px;
    border: none;
    cursor: pointer;
    font-family: 'Lora', Georgia, serif;
    font-size: .88rem;
    font-weight: 500;
    transition: all .22s ease;
    display: flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
  }

  .ocr-btn:disabled {
    opacity: .5;
    cursor: not-allowed;
  }

  .ocr-btn-primary {
    background: linear-gradient(135deg, var(--ocr-wine), #6b1e2a);
    color: #fff;
    box-shadow: 0 4px 14px rgba(139, 38, 53, .35);
  }

  .ocr-btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--ocr-wine-h), var(--ocr-wine));
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(139, 38, 53, .45);
  }

  .ocr-btn-secondary {
    background: rgba(255, 255, 255, .7);
    color: var(--ocr-brown);
    border: 1.5px solid var(--ocr-border);
  }

  .ocr-btn-secondary:hover:not(:disabled) {
    background: var(--ocr-warm);
    border-color: var(--ocr-brown);
    transform: translateY(-1px);
  }

  .ocr-btn-insert {
    background: linear-gradient(135deg, var(--ocr-gold), #b07d28);
    color: #fff;
    box-shadow: 0 4px 14px rgba(201, 151, 59, .4);
  }

  .ocr-btn-insert:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(201, 151, 59, .5);
  }

  /* ── Toast ───────────────────────────────────────────────── */
  .ocr-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: var(--ocr-dark);
    color: #fdf6ec;
    padding: 12px 20px;
    border-radius: 10px;
    font-family: 'Lora', serif;
    font-size: .85rem;
    box-shadow: 0 6px 24px rgba(0, 0, 0, .3);
    transform: translateY(80px);
    opacity: 0;
    transition: all .35s cubic-bezier(.34, 1.56, .64, 1);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .ocr-toast.show {
    transform: translateY(0);
    opacity: 1;
  }

  /* ── Responsive ──────────────────────────────────────────── */
  @media (max-width: 500px) {
    .ocr-wrap {
      padding: 18px 14px 22px;
    }

    .ocr-btn-row {
      flex-direction: column;
    }

    .ocr-btn {
      justify-content: center;
    }
  }
</style>

<!-- ── Markup ───────────────────────────────────────────────── -->
<div class="ocr-wrap" id="ocrComponent">

  <!-- Drop Zone -->
  <div class="ocr-dropzone" id="ocrDropzone">
    <input type="file" id="ocrFileInput" accept="image/*" aria-label="Choisir une image">
    <div class="ocr-dropzone-icon">🖼️</div>
    <div class="ocr-dropzone-text">Glissez une image ici ou cliquez pour parcourir</div>
    <div class="ocr-dropzone-sub">JPG, PNG, WEBP, BMP — jusqu'à 10 Mo</div>
  </div>

  <!-- Preview -->
  <div class="ocr-preview-wrap" id="ocrPreviewWrap">
    <img id="ocrPreviewImg" src="" alt="Aperçu de l'image">
    <div class="ocr-preview-name" id="ocrFileName"></div>
  </div>

  <!-- Boutons d'action (Analyser + Réinitialiser) -->
  <div class="ocr-btn-row" id="ocrBtnRow" style="display:none;">
    <button class="ocr-btn ocr-btn-primary" id="ocrAnalyzeBtn" disabled>
      <span>🔍</span> Analyser l'image
    </button>
    <button class="ocr-btn ocr-btn-secondary" id="ocrResetBtn">
      <span>↩️</span> Réinitialiser
    </button>
  </div>

  <!-- Barre de progression -->
  <div class="ocr-progress-wrap" id="ocrProgressWrap">
    <div class="ocr-progress-label">
      <span id="ocrProgressMsg">Initialisation…</span>
      <span id="ocrProgressPct">0 %</span>
    </div>
    <div class="ocr-progress-bar-bg">
      <div class="ocr-progress-bar" id="ocrProgressBar"></div>
    </div>
  </div>

  <!-- Résultat -->
  <div class="ocr-result-wrap" id="ocrResultWrap">
    <div class="ocr-result-label">✍️ Texte extrait <span
        style="font-style:italic;font-weight:400;font-size:.78rem;color:var(--ocr-brown)">(modifiable)</span></div>
    <textarea class="ocr-result-textarea" id="ocrResultText" rows="6"
      placeholder="Le texte reconnu apparaîtra ici…"></textarea>
    <div class="ocr-btn-row">
      <button class="ocr-btn ocr-btn-primary" id="ocrAutoFillBtn" style="display:none; background: linear-gradient(135deg, #2B4865, #548CA8); box-shadow: 0 4px 14px rgba(43, 72, 101, 0.3);">
        <span>✨</span> Auto-Remplir Tout
      </button>
      <button class="ocr-btn ocr-btn-secondary" id="ocrCopyBtn">
        <span>📋</span> Copier
      </button>
    </div>
  </div>

</div><!-- /.ocr-wrap -->

<!-- ── Toast ────────────────────────────────────────────────── -->
<div class="ocr-toast" id="ocrToast" role="status" aria-live="polite"></div>

<!-- ── Tesseract.js CDN ──────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

<script>
  (function () {
    'use strict';

    /* ── Config ───────────────────────────────────────────────── */
    const OCR_LANG = 'fra';           // langue principale
    const CONTE_ID = document.getElementById('contenu_conte') ? 'contenu_conte' : 'name';
    const MAX_MB = 10;

    /* ── DOM refs ─────────────────────────────────────────────── */
    const dropzone = document.getElementById('ocrDropzone');
    const fileInput = document.getElementById('ocrFileInput');
    const previewWrap = document.getElementById('ocrPreviewWrap');
    const previewImg = document.getElementById('ocrPreviewImg');
    const fileName = document.getElementById('ocrFileName');
    const btnRow = document.getElementById('ocrBtnRow');
    const analyzeBtn = document.getElementById('ocrAnalyzeBtn');
    const resetBtn = document.getElementById('ocrResetBtn');
    const progressWrap = document.getElementById('ocrProgressWrap');
    const progressBar = document.getElementById('ocrProgressBar');
    const progressMsg = document.getElementById('ocrProgressMsg');
    const progressPct = document.getElementById('ocrProgressPct');
    const resultWrap = document.getElementById('ocrResultWrap');
    const resultText = document.getElementById('ocrResultText');
    const autoFillBtn = document.getElementById('ocrAutoFillBtn');
    const copyBtn = document.getElementById('ocrCopyBtn');
    const toast = document.getElementById('ocrToast');

    // Show auto-fill button if name or email field exists (Wait for DOM)
    document.addEventListener('DOMContentLoaded', () => {
      if (document.getElementById('name') || document.getElementById('email')) {
        autoFillBtn.style.display = 'flex';
      }
    });

    let currentFile = null;

    /* ── Toast helper ─────────────────────────────────────────── */
    let toastTimer;
    function showToast(msg, duration = 2800) {
      toast.textContent = msg;
      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('show'), duration);
    }

    /* ── Drag & drop ──────────────────────────────────────────── */
    dropzone.addEventListener('dragover', e => {
      e.preventDefault();
      dropzone.classList.add('drag-over');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone.addEventListener('drop', e => {
      e.preventDefault();
      dropzone.classList.remove('drag-over');
      const file = e.dataTransfer.files[0];
      if (file) handleFile(file);
    });

    /* ── File input change ────────────────────────────────────── */
    fileInput.addEventListener('change', () => {
      if (fileInput.files[0]) handleFile(fileInput.files[0]);
    });

    /* ── Handle file ──────────────────────────────────────────── */
    function handleFile(file) {
      if (!file.type.startsWith('image/')) {
        showToast('⚠️ Veuillez choisir un fichier image valide.');
        return;
      }
      if (file.size > MAX_MB * 1024 * 1024) {
        showToast(`⚠️ Image trop lourde (max ${MAX_MB} Mo).`);
        return;
      }
      currentFile = file;

      const reader = new FileReader();
      reader.onload = e => {
        previewImg.src = e.target.result;
        previewWrap.classList.add('show');
      };
      reader.readAsDataURL(file);

      fileName.textContent = file.name + ' — ' + (file.size / 1024).toFixed(0) + ' Ko';
      btnRow.style.display = 'flex';
      analyzeBtn.disabled = false;

      // Cacher les résultats précédents
      resultWrap.classList.remove('show');
      progressWrap.classList.remove('show');
      resultText.value = '';
    }

    /* ── Analyse OCR ──────────────────────────────────────────── */
    analyzeBtn.addEventListener('click', async () => {
      if (!currentFile) return;

      // Check for internet connection
      if (!navigator.onLine) {
        showToast('❌ Erreur : Connexion internet requise pour charger le moteur OCR.');
        return;
      }

      analyzeBtn.disabled = true;
      progressWrap.classList.add('show');
      resultWrap.classList.remove('show');
      setProgress(0, 'Chargement de Tesseract…');

      let worker = null;
      try {
        // Tesseract.js v4+ API
        worker = await Tesseract.createWorker({
          logger: m => {
            if (m.status === 'recognizing text') {
              const pct = Math.round(m.progress * 100);
              setProgress(pct, 'Reconnaissance en cours…');
            } else if (m.status === 'loading language traineddata') {
              setProgress(10, 'Chargement de la langue…');
            } else if (m.status === 'initializing api') {
              setProgress(20, 'Initialisation…');
            }
          }
        });

        await worker.loadLanguage(OCR_LANG);
        await worker.initialize(OCR_LANG);

        setProgress(30, 'Préparation de l\'image…');
        const { data: { text } } = await worker.recognize(currentFile);
        
        setProgress(100, 'Terminé !');
        resultText.value = text.trim() || '(Aucun texte détecté dans cette image)';
        resultWrap.classList.add('show');
        showToast('✅ Texte extrait avec succès !');

      } catch (err) {
        console.error('[OCR]', err);
        showToast('❌ Erreur d\'analyse. Vérifiez votre connexion internet.');
        setProgress(0, 'Erreur');
      } finally {
        if (worker) await worker.terminate();
        analyzeBtn.disabled = false;
      }
    });

    /* ── Progress helper ──────────────────────────────────────── */
    function setProgress(pct, msg) {
      progressBar.style.width = pct + '%';
      progressMsg.textContent = msg;
      progressPct.textContent = pct + ' %';
    }

    /* ── Reset ────────────────────────────────────────────────── */
    resetBtn.addEventListener('click', () => {
      currentFile = null;
      fileInput.value = '';
      previewImg.src = '';
      previewWrap.classList.remove('show');
      btnRow.style.display = 'none';
      progressWrap.classList.remove('show');
      resultWrap.classList.remove('show');
      resultText.value = '';
      analyzeBtn.disabled = true;
    });

    /* ── Auto-Fill Form ────────────────────────────────────────── */
    autoFillBtn.addEventListener('click', () => {
      const text = resultText.value.trim();
      if (!text) { showToast('⚠️ Aucun texte à extraire.'); return; }

      let filledCount = 0;
      const lines = text.split('\n').map(l => l.trim()).filter(l => l.length > 2);

      // 1. Detect Email with OCR Repair (0 vs @)
      // First, try to repair common OCR errors in potential email lines
      let repairedText = text;
      // If we find something like "word0gmail.com" or "wordOgmail.com", fix it
      repairedText = repairedText.replace(/([a-zA-Z0-9._%+-]+)[0oO8]([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/gi, '$1@$2');

      const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i;
      const emailMatch = repairedText.match(emailRegex);
      const emailInput = document.getElementById('email');
      
      if (emailMatch && emailInput) {
        emailInput.value = emailMatch[0].trim();
        emailInput.dispatchEvent(new Event('input', { bubbles: true }));
        filledCount++;
      }

      // 2. Detect Name
      const nameInput = document.getElementById('name');
      if (nameInput) {
        const nameLabelRegex = /^(?:nom|name|full name|prénom|user)\s*[:\-;]?\s*(.*)/i;
        let detectedName = "";
        
        for (const line of lines) {
          const match = line.match(nameLabelRegex);
          if (match && match[1] && match[1].trim().length > 2) {
            detectedName = match[1].trim();
            break;
          }
        }

        if (!detectedName && lines.length > 0) {
          const firstMeaningfulLine = lines.find(l => !emailRegex.test(l) && !l.toLowerCase().includes('pass'));
          if (firstMeaningfulLine) {
            detectedName = firstMeaningfulLine.replace(/^(nom|name|full name|prénom|user)\s*[:\-;]?\s*/i, '').trim();
          }
        }

        if (detectedName) {
          nameInput.value = detectedName;
          nameInput.dispatchEvent(new Event('input', { bubbles: true }));
          filledCount++;
        }
      }

      // 3. Detect Password
      const pwdInput = document.getElementById('password');
      const confirmInput = document.getElementById('confirm_password');
      if (pwdInput) {
        const pwdLabelRegex = /(?:mot de passe|password|pass|pwd)\s*[:\-;]\s*(.*)/i;
        let detectedPwd = "";

        for (const line of lines) {
          const match = line.match(pwdLabelRegex);
          if (match && match[1] && match[1].trim().length >= 4) {
            detectedPwd = match[1].trim();
            break;
          }
        }

        if (detectedPwd) {
          pwdInput.value = detectedPwd;
          pwdInput.dispatchEvent(new Event('input', { bubbles: true }));
          if (confirmInput) {
            confirmInput.value = detectedPwd;
            confirmInput.dispatchEvent(new Event('input', { bubbles: true }));
          }
          filledCount++;
        }
      }

      if (filledCount > 0) {
        showToast(`✨ ${filledCount} champ(s) rempli(s) avec succès !`);
      } else {
        showToast('⚠️ Impossible de détecter les coordonnées.');
      }
    });

    /* ── Insérer dans le conte (Supprimé) ───────────────────────── */

    /* ── Copier ───────────────────────────────────────────────── */
    copyBtn.addEventListener('click', () => {
      const txt = resultText.value.trim();
      if (!txt) { showToast('⚠️ Rien à copier.'); return; }
      copyToClipboard(txt);
      showToast('📋 Texte copié !');
    });

    function copyToClipboard(text) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).catch(() => fallbackCopy(text));
      } else {
        fallbackCopy(text);
      }
    }
    function fallbackCopy(text) {
      const ta = document.createElement('textarea');
      ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
    }

  })();
</script>