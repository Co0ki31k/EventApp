<?php

/**
 * InterestsController - zarządzanie wyborem zainteresowań użytkownika po rejestracji
 */
class InterestsController
{
    /**
     * Wyświetla formularz wyboru kategorii głównych
     */
    public static function showCategories(): void
    {
        // Sprawdź czy użytkownik jest zalogowany (z sesji)
        if (!isset($_SESSION['temp_user_id'])) {
            header('Location: login.php');
            exit;
        }
        
        $userId = $_SESSION['temp_user_id'];
        
        // Sprawdź czy użytkownik już nie ma zainteresowań
        if (Category::hasUserInterests($userId)) {
            unset($_SESSION['temp_user_id']);
            header('Location: login.php');
            exit;
        }
        
        // Pobierz wszystkie kategorie
        $categories = Category::getAllCategories();
        
        // Ustaw zmienne dla head
        $html_class = 'auth-root';
        
        // Wyświetl pełny widok z layoutem
        require_once __DIR__ . '/../../Views/partials/head.php';
        echo '<main class="auth-page">';
        
        // Przygotuj formularz jako buffer
        ob_start();
        require_once __DIR__ . '/../../Views/auth/interests_categories.php';
        $formContent = ob_get_clean();
        
        require_once __DIR__ . '/../../Views/partials/auth/layout.php';
        echo '</main>';
        echo '<script src="' . asset('js/auth/slider.js') . '" defer></script>';
        echo '</body></html>';
    }
    
    /**
     * Przetwarza wybór kategorii i wyświetla formularz wyboru podkategorii
     */
    public static function processCategories(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: interests.php');
            exit;
        }
        
        if (!isset($_SESSION['temp_user_id'])) {
            header('Location: login.php');
            exit;
        }
        
        $selectedCategories = $_POST['categories'] ?? [];
        
        // Walidacja - przynajmniej jedna kategoria
        if (empty($selectedCategories)) {
            $_SESSION['interests_error'] = 'Wybierz przynajmniej jedną kategorię.';
            header('Location: interests.php');
            exit;
        }
        
        // Zapisz wybrane kategorie w sesji
        $_SESSION['selected_categories'] = array_map('intval', $selectedCategories);
        $_SESSION['current_category_index'] = 0;
        $_SESSION['user_subcategories'] = [];
        
        // Przekieruj do wyboru podkategorii pierwszej kategorii
        header('Location: interests.php?step=subcategories');
        exit;
    }
    
    /**
     * Wyświetla formularz wyboru podkategorii
     */
    public static function showSubcategories(): void
    {
        if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['selected_categories'])) {
            header('Location: interests.php');
            exit;
        }
        
        $selectedCategories = $_SESSION['selected_categories'];
        $currentIndex = $_SESSION['current_category_index'] ?? 0;
        
        // Sprawdź czy to nie koniec kategorii
        if ($currentIndex >= count($selectedCategories)) {
            // Zapisz wszystkie wybrane podkategorie i aktywuj konto
            self::finishInterestsSelection();
            return;
        }
        
        // Pobierz aktualną kategorię
        $currentCategoryId = $selectedCategories[$currentIndex];
        
        // Pobierz podkategorie tylko dla bieżącej kategorii
        $subcategoriesData = Category::getSubcategoriesByCategories([$currentCategoryId]);
        
        if (empty($subcategoriesData)) {
            // Pomiń tę kategorię jeśli nie ma podkategorii
            $_SESSION['current_category_index']++;
            header('Location: interests.php?step=subcategories');
            exit;
        }
        
        // Pobierz dane pojedynczej kategorii
        $categoryData = reset($subcategoriesData);
        $categoryName = $categoryData['category_name'];
        $subcategories = $categoryData['subcategories'];
        
        $totalCategories = count($selectedCategories);
        $currentStep = $currentIndex + 1;
        
        // Ustaw zmienne dla head
        $html_class = 'auth-root';
        
        // Wyświetl pełny widok z layoutem
        require_once __DIR__ . '/../../Views/partials/head.php';
        echo '<main class="auth-page">';
        
        // Przygotuj formularz jako buffer
        ob_start();
        require_once __DIR__ . '/../../Views/auth/interests_subcategories.php';
        $formContent = ob_get_clean();
        
        require_once __DIR__ . '/../../Views/partials/auth/layout.php';
        echo '</main>';
        echo '<script src="' . asset('js/auth/slider.js') . '" defer></script>';
        echo '</body></html>';
    }
    
    /**
     * Przetwarza wybór podkategorii dla bieżącej kategorii
     */
    public static function processSubcategories(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: interests.php');
            exit;
        }
        
        if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['selected_categories'])) {
            header('Location: interests.php');
            exit;
        }
        
        $selectedSubcategories = $_POST['subcategories'] ?? [];
        
        // Walidacja - przynajmniej jedna podkategoria
        if (empty($selectedSubcategories)) {
            $_SESSION['interests_error'] = 'Wybierz przynajmniej jedno zainteresowanie.';
            header('Location: interests.php?step=subcategories');
            exit;
        }
        
        // Dodaj wybrane podkategorie do listy
        if (!isset($_SESSION['user_subcategories'])) {
            $_SESSION['user_subcategories'] = [];
        }
        $_SESSION['user_subcategories'] = array_merge(
            $_SESSION['user_subcategories'],
            array_map('intval', $selectedSubcategories)
        );
        
        // Przejdź do następnej kategorii
        $_SESSION['current_category_index']++;
        
        // Przekieruj do następnej kategorii
        header('Location: interests.php?step=subcategories');
        exit;
    }
    
    /**
     * Kończy proces wyboru zainteresowań i aktywuje konto
     */
    private static function finishInterestsSelection(): void
    {
        $userId = $_SESSION['temp_user_id'];
        $allSubcategories = $_SESSION['user_subcategories'] ?? [];
        
        if (empty($allSubcategories)) {
            $_SESSION['interests_error'] = 'Nie wybrano żadnych zainteresowań.';
            $_SESSION['current_category_index'] = 0;
            header('Location: interests.php?step=subcategories');
            exit;
        }
        
        // Zapisz zainteresowania użytkownika i aktywuj konto
        $success = Category::saveUserInterests($userId, $allSubcategories);
        
        if ($success) {
            // Wyczyść sesję tymczasową
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['selected_categories']);
            unset($_SESSION['current_category_index']);
            unset($_SESSION['user_subcategories']);
            unset($_SESSION['interests_error']);
            
            // Ustaw komunikat sukcesu
            $_SESSION['registration_complete'] = true;
            
            // Przekieruj do logowania
            header('Location: login.php?registered=1');
            exit;
        } else {
            $_SESSION['interests_error'] = 'Wystąpił błąd podczas zapisywania zainteresowań. Spróbuj ponownie.';
            header('Location: interests.php?step=subcategories');
            exit;
        }
    }
}
