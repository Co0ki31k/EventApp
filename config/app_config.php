<?php

define("APP_NAME", "EventApp");
define("SESSION_NAME", "eventapp_session");
define("DEFAULT_TIMEZONE", "Europe/Warsaw");
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'eventapp4@gmail.com');
define('SMTP_FROM_EMAIL', 'eventapp4@gmail.com');
define('SMTP_FROM_NAME', 'EventApp:noreply');
date_default_timezone_set(DEFAULT_TIMEZONE);

// Środowisko aplikacji (development, production)
define("APP_ENV", "development");

// Tryb debugowania
define("DEBUG", true);
