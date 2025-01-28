<?php include "Partials/navigation.php"; ?>


<?php
/** Available variables:
 * - $feedback
 */
?>


<!-- Title -->
<div class="container main-content">
    <h1><?= translate("Kristal Framework Demo Page"); ?></h1>
    <p><?= translate("This page showcases a wide array of the framework's demo features. Explore the numerous possibilities at your disposal."); ?></p>
</div>


<!-- Theme -->
<div class="container main-content">
    
    <h1><?= translate("Theme"); ?></h1>
    <p><?= translate("Chaning theme using form requests"); ?>:</p>

    <div class="theme-selection-div">
        <form action='<?= route(strtolower(translate("Demo"))); ?>' method='post'>
            <?php CSRF::create("change_theme_form"); ?>
            <?php CSRF::request("change_theme"); ?>
            <input type='hidden' name='theme' value='dark'>
            <input type='submit' class='btn btn-dark' value='<?= translate("Activate dark theme"); ?>'>
        </form>

        <form action='<?= route(strtolower(translate("Demo"))); ?>' method='post'>
            <?php CSRF::create("change_theme_form"); ?>
            <?php CSRF::request("change_theme"); ?>
            <input type='hidden' name='theme' value='light'>
            <input type='submit' class='btn btn-light' value='<?= translate("Activate light theme"); ?>'>
        </form>
    </div>

    <p><?= translate("Chaning theme using links"); ?>:</p>
    <p><a href="<?= route("demo/dark"); ?>"><?= translate("Activate dark theme"); ?></a></p>
    <p><a href="<?= route("demo/light"); ?>"><?= translate("Activate light theme"); ?></a></p>

    <?php if (!empty($feedback)): ?>
        <p><?= $feedback; ?></p>
    <?php endif; ?>
</div>


<!-- Countdown Block -->
<div class="container main-content">
    <h1>Countdown Block</h1>
    <?php Block::render("countdown", [
        "date" => "1.1.2024 00:00:00",
        "format" => "⏰ {d} {D} {h}:{m}:{s} ⏰",
        "days" => "days|day",
        "hours" => "h",
        "minutes" => "m",
        "seconds" => "s",
        "expired" => "⏰ 00:00:00 ⏰",
    ]); ?>
</div>


<!-- Emails -->
<div class="container main-content">
    <h1>Emails</h1>
    <form method="post">
        <?php CSRF::create("send_email"); ?>
        <?php CSRF::request("send_email"); ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="receiver" id="receiver">
        </div>

        <div class="form-group">
            <label for="email">Title</label>
            <input type="text" class="form-control" name="title" id="title">
        </div>

        <div class="form-group">
            <label for="comment">Message</label>
            <textarea rows="5" class="form-control" name="message" id="message"></textarea>
        </div>

        <div class="form-group center">
            <input type="submit" class="btn btn-primary" value="Send Email">
        </div>

        <div id="send-email-feedback"></div><br/>
    </form>
</div>


<!-- Login -->
<div class="container main-content">
    <h1>Login</h1>
    <form method="post" autocomplete="off" spellcheck="false">
        <?php CSRF::create("login"); ?>
        <?php CSRF::request("login"); ?>

        <div class="group">
            <label for="login-email" class="label">Email</label>
            <input type="email" class="input" id="login-email" name="login-email"  value="<?= (isset($_POST["login-email"])) ? $_POST["login-email"] : ""; ?>" required autofocus>
        </div>

        <div class="group">
            <label for="login-password" class="label">Password</label>
            <input type="password" class="input" id="login-password" name="login-password" value="" required>
            <a class="popup-link" id="forgot-password-link" popupID="retrieve-password-popup">Forgot password?</a>
        </div>

        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me">
            <label for="login-remember-me" class="custom-control-label">Remember me</label>
        </div>

        <div id="login-feedback" style="padding-top: 30px;"></div>

        <div class="group">
            <input type="submit" class="button button-primary" id="login-button" value="Login">
        </div>
    </form>
</div>


<!-- Registration -->
<div class="container main-content">
    <h1>Registration</h1>
    <form method="post" autocomplete="off" spellcheck="false">
        <?php CSRF::create("registration"); ?>
        <?php CSRF::request("registration"); ?>

        <div class="group">
            <label for="registration-email" class="label">Email</label>
            <input type="email" class="input" id="registration-email" name="registration-email"  value="<?= (isset($_POST["login-email"])) ? $_POST["registration-email"] : ""; ?>" required minlength="4">
        </div>

        <div class="group">
            <label for="registration-password" class="label">Password</label>
            <input type="password" class="input" id="registration-password" name="registration-password" value="" required minlength="8">
        </div>

        <div class="group">
            <label for="registration-confirmation-password" class="label">Confirm Password</label>
            <input type="password" class="input" id="registration-confirmation-password" name="registration-confirmation-password" value="" required minlength="8">
        </div>

        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="terms-of-service-checkbox" name="terms-of-service-checkbox" required>
            <label for="terms-of-service-checkbox" class="custom-control-label"></label>
            <label style="margin-top: 5px;">
                <label for="terms-of-service-checkbox">I Accept the</label>
                <a class="popup-link" id="terms-of-service-link" popupID="terms-of-service-popup">Terms of Service</a>
            </label>
        </div>

        <!-- Feedback -->
        <div id="registration-feedback" style="padding-top: 30px;"></div>

        <!-- Submit -->
        <div class="group">
            <input type="submit" class="button button-primary" id="registration-button" value="Create" disabled>
        </div>
    </form>
</div>
