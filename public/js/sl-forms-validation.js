// Client-side validation for social-learning forms (novalidate)
document.addEventListener('DOMContentLoaded', function () {
  function showError(input, message) {
    const el = document.querySelector('.error-text[data-for="' + input + '"]');
    if (el) el.textContent = message;
  }
  function clearError(input) {
    const el = document.querySelector('.error-text[data-for="' + input + '"]');
    if (el) el.textContent = '';
  }

  const groupeForm = document.getElementById('groupeForm');
  if (groupeForm) {
    groupeForm.addEventListener('submit', function (e) {
      let ok = true;
      const nom = document.getElementById('nom_groupe').value.trim();
      const desc = document.getElementById('description').value.trim();
      if (nom.length < 3) { showError('nom_groupe', 'Le nom doit contenir au moins 3 caractères.'); ok = false; } else clearError('nom_groupe');
      if (desc.length < 10) { showError('description', 'La description doit contenir au moins 10 caractères.'); ok = false; } else clearError('description');
      if (!ok) e.preventDefault();
    });
  }

  const discussionForm = document.getElementById('discussionForm');
  if (discussionForm) {
    discussionForm.addEventListener('submit', function (e) {
      let ok = true;
      const titre = document.getElementById('titre').value.trim();
      const contenu = document.getElementById('contenu').value.trim();
      if (titre.length < 5) { showError('titre', 'Le titre doit contenir au moins 5 caractères.'); ok = false; } else clearError('titre');
      if (contenu.length < 10) { showError('contenu', 'Le contenu doit contenir au moins 10 caractères.'); ok = false; } else clearError('contenu');
      if (!ok) e.preventDefault();
    });
  }

  const messageForm = document.getElementById('messageForm');
  if (messageForm) {
    messageForm.addEventListener('submit', function (e) {
      let ok = true;
      const contenu = document.getElementById('contenu_msg').value.trim();
      if (contenu.length < 10) { showError('contenu_msg', 'Le message doit contenir au moins 10 caractères.'); ok = false; } else clearError('contenu_msg');
      if (!ok) e.preventDefault();
    });
  }
});
