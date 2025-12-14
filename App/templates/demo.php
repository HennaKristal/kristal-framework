<?php include "partials/navigation.php"; ?>

<?php
/** Available variables:
 * - $message
 */
?>

<!-- Title -->
<div class="container main-content">
    <h1><?php echo translate("Kristal Framework Demo Page"); ?></h1>
    <p><?php echo translate("This page showcases a wide array of the framework's demo features. Explore the numerous possibilities at your disposal."); ?></p>

    <hr>

    <!-- Controller variable -->
    <?php if (!empty($message)): ?>
        <p><?php echo translate("Message from the controller"); ?>: <?php echo esc_html($message); ?></p>
    <?php endif; ?>

    <hr>

    <!-- Theme -->
    <h1><?php echo translate("Theme"); ?></h1>
    <p><?php echo translate("Chaning theme using form requests"); ?>:</p>

    <div class="theme-selection-div">
        <form action='' method='post'>
            <?php CSRF::create("change_theme_dark", "change_theme"); ?>
            <input type='hidden' name='theme-name' value='dark'>
            <input type='submit' class='btn btn-dark' value='<?php echo translate("Activate dark theme"); ?>'>
        </form>

        <form action='' method='post'>
            <?php CSRF::create("change_theme_light", "change_theme"); ?>
            <input type='hidden' name='theme-name' value='light'>
            <input type='submit' class='btn btn-light' value='<?php echo translate("Activate light theme"); ?>'>
        </form>
    </div>

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
