<?php
/**
 * Footer Template
 */
?>
        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-content">
                <p>&copy; 2025 NextCart. All rights reserved.</p>
                <p class="footer-links">
                    <a href="#privacy">Privacy Policy</a> |
                    <a href="#terms">Terms of Service</a> |
                    <a href="#contact">Contact Us</a>
                </p>
            </div>
        </footer>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Shared Utilities (all pages) -->
    <script src="<?php echo APP_URL; ?>/assets/js/utilities.js" data-api-url="<?php echo APP_URL; ?>/api"></script>
    
    <!-- Page-Specific JavaScript (loaded by page controller) -->
    <?php if (isset($page_js)) { ?>
        <?php foreach ((array)$page_js as $js_file) { ?>
            <script src="<?php echo APP_URL; ?>/assets/js/pages/<?php echo escape($js_file); ?>"></script>
        <?php } ?>
    <?php } ?>
</body>
</html>
