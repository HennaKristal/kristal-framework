<!doctype html>
<html lang="en">
    <head>
        <!-- Metadata -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="generator" content="Kristal Framework" />
        <meta name="robots" content="noindex" />

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?= css("maintenance.css"); ?>">
        <style>body { background-image: url("<?= image('Backgrounds/maintenance.jpg'); ?>"); }</style>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?= js("core.js"); ?>"></script>
        <script src="<?= js("maintenance.js"); ?>"></script>

        <!-- Page title -->
        <title><?= translate("Maintenance"); ?></title>

        <!-- Website icon -->
        <link rel="icon" type="image/gif" href="<?= image("kristal_framework_alt_icon.png"); ?>" />
    </head>


    <body>
        
        <!-- language settings -->
        <div id="language-selection">
            <?php Block::render("language_menu", ["request" => "change_language"]);  ?>
        </div>


        <!-- Title -->
        <div class="container maintenance-title">
            <h1 class="maintenance-title"><?= translate("Maintenance"); ?></h1>
        </div>

        
        <!-- Description -->
        <div class="container maintenance-text">
            <p><?= translate("We are currently maintaining our website, we will be back shortly. Thanks for your patience."); ?></p>
        </div>


        <!-- Authentication -->
        <div class="container authentication-container">
            <h2><?= translate("Authentication"); ?></h2>
            <form method="post">

                <div class="mb-3">
                    <input type="password" class="form-control" name="maintenance-password" id="maintenance-password" placeholder="Password" required>
                </div>

                <button type="submit" class="btn btn-primary"><?= translate("Sign In"); ?></button>

                <?php if ($authenticationAttemptLimitReached) : ?>
                    <p id="feedback" style="color: red;">
                        <span><?= translate("Too many login attempts, please wait %s before you are allowed to try again.", $authenticationLockoutLabel); ?></span>
                    </p>
                <?php elseif ($authenticationFailed) : ?>
                    <p id="feedback" style="color: red;"><?= translate("Failed to authenticate."); ?></p>
                <?php else: ?>
                    <p id="feedback" style="padding: 12px;"></p>
                <?php endif; ?>
            </form>
        </div>

        
        <!-- Social links -->
        <div class="social-icons">
            <!-- Email -->
            <a class="social-menu-link" href="mailto:example@example.com" target="_blank" data-bs-toggle="tooltip" data-bs-title="<?= translate("Email"); ?>">
                <img class="social-menu-image" src="<?= image("Icons/email.png") ?>">
            </a>

            <!-- Twitter -->
            <a class="social-menu-link" href="https://twitter.com" target="_blank" data-bs-toggle="tooltip" data-bs-title="Twitter">
                <img class="social-menu-image" src="<?= image("Icons/twitter.png") ?>">
            </a>

            <!-- Facebook -->
            <a class="social-menu-link" href="https://facebook.com" target="_blank" data-bs-toggle="tooltip" data-bs-title="Facebook">
                <img class="social-menu-image" src="<?= image("Icons/facebook.png") ?>">
            </a>

            <!-- Youtube -->
            <a class="social-menu-link" href="https://youtube.com" target="_blank" data-bs-toggle="tooltip" data-bs-title="Youtube">
                <img class="social-menu-image" src="<?= image("Icons/youtube.png") ?>">
            </a>
        </div>

    </body>
</html>
