<?php
require_once __DIR__ . "/session.php";

if(isLoggedIn()) {
    $user_type = $_SESSION['user_type'] ?? 'user'; // domyślnie zwykły użytkownik
} else {
    $user_type = 'guest';
}
?>

<nav class="navbar navbar-centered">
    <a href="index.php" class="logo">
        <img src="/EventApp/assets/img/logo.png" alt="<?= APP_NAME ?>">
    </a>
    <div class="nav-links">
        <?php
        if($user_type === 'guest') {
            echo '<a href="#hero" class="nav-btn">Explore</a>';
            echo '<a href="#features" class="nav-btn">About</a>';
            echo '<a href="#business" class="nav-btn">Business</a>';
        } elseif($user_type === 'user') {
            echo '<a href="explore.php">Explore</a>';
            echo '<a href="categories.php">Categories</a>';
            echo '<a href="friends.php">Friends</a>';
            echo '<a href="profile.php">Profile</a>';
        } elseif($user_type === 'company') {
            echo '<a href="explore.php">Explore</a>';
            echo '<a href="my-events.php">My Events</a>';
            echo '<a href="add-event.php">Add Event</a>';
            echo '<a href="company-profile.php">Company Profile</a>';
        }
        ?>
    </div>
    <a href="<?= isLoggedIn() ? 'logout.php' : 'login.php' ?>" class="btn-login">
        <?= isLoggedIn() ? 'Logout' : 'Join' ?>
    </a>
</nav>
