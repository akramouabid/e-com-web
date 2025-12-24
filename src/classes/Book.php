<?php
/**
 * Classe Book - Gestion des livres (produits)
 */
class Book {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Récupérer tous les livres avec pagination
     */
    public function getAll($limit = 12, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, c.name as category_name 
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id
            ORDER BY b.created_at DESC
            LIMIT 12 OFFSET 0
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer un livre par ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT b.*, c.name as category_name 
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Recherche et filtrage des livres avec AJAX
     */
    public function search($keyword = '', $category_id = null, $min_price = null, $max_price = null) {
        $query = "SELECT b.*, c.name as category_name FROM books b
                  LEFT JOIN categories c ON b.category_id = c.id
                  WHERE 1=1";
        $params = [];
        
        // Recherche par mot-clé
        if (!empty($keyword)) {
            $query .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
            $search_term = "%$keyword%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        // Filtrer par catégorie
        if (!empty($category_id)) {
            $query .= " AND b.category_id = ?";
            $params[] = $category_id;
        }
        
        // Filtrer par prix min
        if ($min_price !== null) {
            $query .= " AND b.price >= ?";
            $params[] = (float)$min_price;
        }
        
        // Filtrer par prix max
        if ($max_price !== null) {
            $query .= " AND b.price <= ?";
            $params[] = (float)$max_price;
        }
        
        $query .= " ORDER BY b.title ASC LIMIT 50";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Ajouter un nouveau livre (admin)
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO books (title, author, description, isbn, publisher, category_id, price, stock, pages, publication_year, cover_image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['title'],
            $data['author'],
            $data['description'],
            $data['isbn'],
            $data['publisher'],
            $data['category_id'],
            $data['price'],
            $data['stock'],
            $data['pages'],
            $data['publication_year'],
            $data['cover_image'] ?? null
        ]);
    }
    
    /**
     * Modifier un livre (admin)
     */
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE books SET
                title = ?,
                author = ?,
                description = ?,
                isbn = ?,
                publisher = ?,
                category_id = ?,
                price = ?,
                stock = ?,
                pages = ?,
                publication_year = ?,
                cover_image = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['title'],
            $data['author'],
            $data['description'],
            $data['isbn'],
            $data['publisher'],
            $data['category_id'],
            $data['price'],
            $data['stock'],
            $data['pages'],
            $data['publication_year'],
            $data['cover_image'] ?? null,
            $id
        ]);
    }
    
    /**
     * Supprimer un livre (admin)
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Récupérer le nombre total de livres (pagination)
     */
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM books");
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Récupérer toutes les catégories
     */
    public function getCategories() {
        $stmt = $this->pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Ajouter une catégorie
     */
    public function addCategory($name, $description = '') {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }
}
?>
