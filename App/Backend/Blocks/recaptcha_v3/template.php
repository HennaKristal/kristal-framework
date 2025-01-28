<script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars(RECAPTCHA_V3_SITE_KEY, ENT_QUOTES, 'UTF-8'); ?>"></script>

<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo htmlspecialchars(RECAPTCHA_V3_SITE_KEY, ENT_QUOTES, 'UTF-8'); ?>', {action: 'contact_form'})
            .then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
    });
</script>

<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
