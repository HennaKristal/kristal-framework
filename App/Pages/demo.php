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

    <?php if (!empty($themeFeedback)): ?>
        <div class="feedback">
            <p><?= $themeFeedback; ?></p>
        </div>
    <?php endif; ?>

    <hr>

    <!-- Countdown Block -->
    <h1>Countdown Block</h1>
    <?php Block::render("countdown", [
        "date" => "1.1.2027 00:00:00",
        "format" => "⏰ {d} {D} {h}:{m}:{s} ⏰",
        "days" => "days|day",
        "hours" => "h",
        "minutes" => "m",
        "seconds" => "s",
        "expired" => "⏰ 00:00:00 ⏰",
    ]); ?>

</div>
