        </div> <!-- End admin-page-container -->
    </main> <!-- End admin-content-pro -->

    <style>
        /* Smooth page transitions */
        .admin-page-container {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script src="<?= APP_URL ?>/View/assets/js/module-focus.js"></script>
</body>
</html>
