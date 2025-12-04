<?php
define("BASE_PATH", dirname(__DIR__));
define("PUBLIC_PATH", BASE_PATH . "/public");
define("CONFIG_PATH", BASE_PATH . "/config");

// Główne ścieżki src/
define("SRC_PATH", BASE_PATH . "/src");
define("VIEWS_PATH", SRC_PATH . "/Views");
define("CONTROLLERS_PATH", SRC_PATH . "/Controllers");
define("MODELS_PATH", SRC_PATH . "/Models");
define("HELPERS_PATH", SRC_PATH . "/Helpers");
define("CLASSES_PATH", SRC_PATH . "/classes");

// Widoki podzielone według ról
define("VIEWS_GUEST_PATH", VIEWS_PATH . "/guest");
define("VIEWS_USER_PATH", VIEWS_PATH . "/user");
define("VIEWS_COMPANY_PATH", VIEWS_PATH . "/company");
define("VIEWS_AUTH_PATH", VIEWS_PATH . "/auth");

// Partiale widoków (części wspólne)
define("PARTIALS_PATH", VIEWS_PATH . "/partials");

// Database
define("DATABASE_PATH", BASE_PATH . "/database");

// Upewnij się że BASE_URL odpowiada lokalnej ścieżce
define("BASE_URL", "http://localhost/Projekt/public");

// Public assets
define("ASSETS_PATH", PUBLIC_PATH . "/assets");
define("ASSETS_URL", rtrim(BASE_URL, '/') . '/assets');
define("ASSETS_CSS_PATH", ASSETS_PATH . "/css");
define("ASSETS_JS_PATH", ASSETS_PATH . "/js");
define("ASSETS_IMG_PATH", ASSETS_PATH . "/img");
define("ASSETS_SCSS_PATH", ASSETS_PATH . "/scss");

