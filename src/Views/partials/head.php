<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="author" content="">
  <meta name="description" content="">
  <title><?= htmlspecialchars(defined('APP_NAME') ? APP_NAME : 'EventApp') ?></title>
  <?php
  // Use helper functions to generate asset and route URLs
  // Google Fonts: Roboto
  echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
  echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'; 
  echo '<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">';

  echo '<link rel="stylesheet" href="' . htmlspecialchars(asset('css/reset.css')) . '">';
  echo '<link rel="stylesheet" href="' . htmlspecialchars(asset('css/main.css')) . '">';

  echo '<link rel="apple-touch-icon" sizes="180x180" href="' . htmlspecialchars(asset('img/favicon_io/apple-touch-icon.png')) . '">';
  echo '<link rel="icon" type="image/png" sizes="32x32" href="' . htmlspecialchars(asset('img/favicon_io/favicon-32x32.png')) . '">';
  echo '<link rel="icon" type="image/png" sizes="16x16" href="' . htmlspecialchars(asset('img/favicon_io/favicon-16x16.png')) . '">';
  echo '<link rel="manifest" href="' . htmlspecialchars(asset('img/favicon_io/site.webmanifest')) . '">';
  ?>
</head>
<body>
