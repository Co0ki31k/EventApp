<?php
// Profile edit modal (hidden by default)
?>
<div id="profile-edit-modal" class="modal" aria-hidden="true">
    <div class="modal-content">
        <button class="modal-close" aria-label="Zamknij">×</button>
        <h2>Edytuj profil</h2>
        <form id="profile-edit-form">
            <label>Imię
                <input type="text" name="first_name">
            </label>
            <label>Nazwisko
                <input type="text" name="last_name">
            </label>
            <label>Email
                <input type="email" name="email" disabled>
            </label>
            <button type="submit">Zapisz</button>
        </form>
    </div>
</div>
