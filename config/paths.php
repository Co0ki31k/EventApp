<?php
define("BASE_PATH", dirname(__DIR__));
define("PUBLIC_PATH", BASE_PATH . "/public");
// Projekt przeniósł logikę do katalogu `src/` i widoki częściowe do `src/Views/partials`
define("INCLUDES_PATH", BASE_PATH . "/src/Views/partials");
define("CONFIG_PATH", BASE_PATH . "/config");
define("ACTIONS_PATH", BASE_PATH . "/actions");
define("CLASSES_PATH", BASE_PATH . "/src");

// Dodatkowe pomocnicze ścieżki
define("SRC_PATH", BASE_PATH . "/src");
define("VIEWS_PATH", SRC_PATH . "/Views");

// Upewnij się że BASE_URL odpowiada lokalnej ścieżce (w Twoim projekcie katalog nazywa się 'Projekt')
define("BASE_URL", "http://localhost/Projekt/public");

// Public assets
define("ASSETS_PATH", PUBLIC_PATH . "/assets");
// Public assets URL (use in HTML for src/href)
define("ASSETS_URL", rtrim(BASE_URL, '/') . '/assets');
define("ASSETS_CSS_PATH", ASSETS_PATH . "/css");
define("ASSETS_JS_PATH", ASSETS_PATH . "/js");
define("ASSETS_IMG_PATH", ASSETS_PATH . "/img");
define("ASSETS_SCSS_PATH", ASSETS_PATH . "/scss");

// Widoki podzielone według ról
define("VIEWS_GUEST_PATH", VIEWS_PATH . "/guest");
define("VIEWS_USER_PATH", VIEWS_PATH . "/user");
define("VIEWS_COMPANY_PATH", VIEWS_PATH . "/company");
define("VIEWS_ADMIN_PATH", VIEWS_PATH . "/admin");

// Partiale widoków (części wspólne)
define("PARTIALS_PATH", VIEWS_PATH . "/partials");

