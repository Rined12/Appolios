</main>
<footer class="bg-light py-3 mt-5">
  <div class="container text-center">© LearnHub - Social Learning</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
if (!defined('ASSET_URL')) {
    require_once __DIR__ . '/../../../../../config/config.php';
}
require_once __DIR__ . '/../../../partials/confirm_modal.php';
?>
<script src="<?= ASSET_URL ?>/js/confirm-modal.js"></script>
</body>
</html>
