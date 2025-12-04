<?php

/**
 * Model Category - zarządzanie kategoriami i podkategoriami
 */
class Category
{
    /**
     * Pobiera wszystkie kategorie główne
     * 
     * @return array Lista kategorii
     */
    public static function getAllCategories(): array
    {
        $result = Database::query(
            "SELECT category_id, name, description, icon FROM Categories ORDER BY name ASC"
        );
        
        return $result ?: [];
    }
    
    /**
     * Pobiera podkategorie dla wybranych kategorii
     * 
     * @param array $categoryIds Lista ID kategorii
     * @return array Lista podkategorii pogrupowana po kategoriach
     */
    public static function getSubcategoriesByCategories(array $categoryIds): array
    {
        if (empty($categoryIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        
        $result = Database::query(
            "SELECT s.subcategory_id, s.category_id, s.name, s.description, c.name as category_name
             FROM Subcategories s
             JOIN Categories c ON s.category_id = c.category_id
             WHERE s.category_id IN ($placeholders)
             ORDER BY c.name ASC, s.name ASC",
            $categoryIds
        );
        
        if (!$result) {
            return [];
        }
        
        // Grupowanie podkategorii według kategorii
        $grouped = [];
        foreach ($result as $row) {
            $catId = $row['category_id'];
            if (!isset($grouped[$catId])) {
                $grouped[$catId] = [
                    'category_name' => $row['category_name'],
                    'subcategories' => []
                ];
            }
            $grouped[$catId]['subcategories'][] = [
                'subcategory_id' => $row['subcategory_id'],
                'name' => $row['name'],
                'description' => $row['description']
            ];
        }
        
        return $grouped;
    }
    
    /**
     * Zapisuje zainteresowania użytkownika
     * 
     * @param int $userId ID użytkownika
     * @param array $subcategoryIds Lista ID podkategorii
     * @return bool
     */
    public static function saveUserInterests(int $userId, array $subcategoryIds): bool
    {
        if (empty($subcategoryIds)) {
            return false;
        }
        
        try {
            // Rozpocznij transakcję
            Database::beginTransaction();
            
            // Usuń stare zainteresowania użytkownika
            Database::query(
                "DELETE FROM UserInterests WHERE user_id = ?",
                [$userId]
            );
            
            // Dodaj nowe zainteresowania
            foreach ($subcategoryIds as $subcatId) {
                Database::query(
                    "INSERT INTO UserInterests (user_id, subcategory_id) VALUES (?, ?)",
                    [$userId, $subcatId]
                );
            }
            
            // Aktywuj konto użytkownika
            Database::query(
                "UPDATE Users SET is_active = 1 WHERE user_id = ?",
                [$userId]
            );
            
            Database::commit();
            return true;
            
        } catch (Exception $e) {
            Database::rollback();
            error_log("Error saving user interests: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sprawdza czy użytkownik ma już wybrane zainteresowania
     * 
     * @param int $userId ID użytkownika
     * @return bool
     */
    public static function hasUserInterests(int $userId): bool
    {
        $result = Database::queryOne(
            "SELECT COUNT(*) as count FROM UserInterests WHERE user_id = ?",
            [$userId]
        );
        
        return $result && $result['count'] > 0;
    }
}
