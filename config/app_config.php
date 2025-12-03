<?php

define("APP_NAME", "EventApp");
define("SESSION_NAME", "eventapp_session");
define("DEFAULT_TIMEZONE", "Europe/Warsaw");
date_default_timezone_set(DEFAULT_TIMEZONE);

// Środowisko aplikacji (development, production)
define("APP_ENV", "development");

// Tryb debugowania
define("DEBUG", true);
