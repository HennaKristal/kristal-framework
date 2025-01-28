<nav class="navbar navbar-expand-lg border-bottom">
    <div class="container">

        <!-- Navbar Title (text) -->
        <!-- <a class="navbar-brand" href="<?= route(""); ?>"><?= translate("Kristal Framework"); ?></a> -->

        <!-- Navbar Title (image) -->
        <a class="navbar-brand" href="<?= route(""); ?>"><img class="navbar-logo colorized-fast" src="<?= image("kristal_framework_logo.png"); ?>" /></a>

        <!-- Navbar mobile toggle button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation" data-bs-theme="dark">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar-menu">

            <!-- Navbar links -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" page="frontpage.php" href="<?= route(strtolower(translate("Home"))); ?>"><?= translate("Home"); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" page="demo.php" href="<?= route(strtolower(translate("Demo"))); ?>"><?= translate("Demo"); ?></a>
                </li>
            </ul>

            <!-- Language settings -->
            <?php Block::render("language_menu", ["request" => "change_language"]);  ?>

        </div>
    </div>
</nav>

<!-- Activate correct navigation link -->
<!-- (add page attribute to your navigation links with page file name as it's value, for example page="home.php") -->
<script>$("[page='<?= $page ?>']").addClass("active");</script>
