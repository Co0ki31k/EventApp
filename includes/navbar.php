<?php
require_once __DIR__ . "/session.php";

if(isLoggedIn()) {
    $user_type = $_SESSION['user_type'] ?? 'user'; // domyślnie zwykły użytkownik
} else {
    $user_type = 'guest';
}
?>

<nav class="navbar navbar-centered">
    <a href="index.php" class="logo">LOGO</a>
    <div class="nav-links">
        <?php
        if($user_type === 'guest') {
            echo '<a href="explore.php">ODKRYWAJ</a>';
            echo '<a href="about.php">O nas</a>';
            echo '<a href="for-business.php">Dla firm</a>';
        } elseif($user_type === 'user') {
            echo '<a href="explore.php">ODKRYWAJ</a>';
            echo '<a href="categories.php">Kategorie</a>';
            echo '<a href="friends.php">Znajomi</a>';
            echo '<a href="profile.php">Profil</a>';
        } elseif($user_type === 'company') {
            echo '<a href="explore.php">ODKRYWAJ</a>';
            echo '<a href="my-events.php">Moje wydarzenia</a>';
            echo '<a href="add-event.php">Dodaj wydarzenie</a>';
            echo '<a href="company-profile.php">Profil firmy</a>';
        }
        ?>
    </div>
    <a href="<?= isLoggedIn() ? 'logout.php' : 'login.php' ?>" class="btn-login">
        <?= isLoggedIn() ? 'WYLOGUJ' : 'LOGIN' ?>
    </a>
</nav>
