<!doctype html>
<html lang="<?php echo Session::has('language') ? Session::get('language') : DEFAULT_LANGUAGE; ?>">
    <head>
        
        <!-- Metadata -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="generator" content="Kristal Framework" />
        <?php if (isset($kristal_metadata[$page]) || isset($kristal_metadata["*"])): ?>
            <?php foreach ((isset($kristal_metadata[$page]) ? $kristal_metadata[$page] : $kristal_metadata["*"]) as $key => $value): ?>
                <meta property="<?= htmlspecialchars($key) ?>" content="<?= htmlspecialchars($value) ?>" />
            <?php endforeach; ?>
        <?php endif; ?>

        
        <!-- Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?= css(Session::has("theme") ? Session::get("theme") : DEFAULT_THEME); ?>">
        

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="<?= js("core.js"); ?>"></script>
        <script src="<?= js("main.js"); ?>"></script>


        <!-- Page title -->
        <?php if (!empty($kristal_metadata[$page]->title)): ?>
            <title><?= htmlspecialchars($kristal_metadata[$page]->title); ?></title>
        <?php elseif (!empty($kristal_metadata["*"]->title)): ?>
            <title><?= htmlspecialchars($kristal_metadata["*"]->title); ?></title>
        <?php else: ?>
            <title><?= BASE_URL . $page; ?></title>
        <?php endif; ?>


        <!-- Website icon -->
        <link rel="icon" type="image/gif" href="<?= image("kristal_framework_alt_icon.png"); ?>" />

    </head>
    <body>
