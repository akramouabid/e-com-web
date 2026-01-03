-- ============================================
-- Script SQL - LibreBooks E-commerce
-- Création et population de la base de données
-- ============================================

-- Créer la base de données
DROP DATABASE IF EXISTS ecom_bookstore;
CREATE DATABASE ecom_bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecom_bookstore;

-- ============================================
-- TABLE: users (Utilisateurs)
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les recherches
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);

-- ============================================
-- TABLE: categories (Catégories de livres)
-- ============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: books (Livres - Produits)
-- ============================================
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(150) NOT NULL,
    description TEXT,
    isbn VARCHAR(20) UNIQUE,
    publisher VARCHAR(150),
    category_id INT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    pages INT,
    publication_year INT,
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_category (category_id),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: cart (Panier)
-- ============================================
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, book_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: orders (Commandes - Bonus)
-- ============================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: order_items (Détail des commandes)
-- ============================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id),
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES DE TEST
-- ============================================

-- Insérer les catégories
INSERT INTO categories (name, description) VALUES
('Science-Fiction', 'Livres de science-fiction et futur'),
('Romance', 'Histoires d\'amour et relations'),
('Mystère', 'Thrillers et livres policiers'),
('Fantasy', 'Mondes fantastiques et magie'),
('Non-fiction', 'Biographies et essais'),
('Jeunesse', 'Livres pour enfants et ados');

-- Insérer l'utilisateur administrateur (password: admin123)
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@librebooks.local', '$2y$10$YourHashedPasswordHere', 'admin');

-- Note: Le hash ci-dessus est un placeholder. Utilisez PHP pour générer:
-- password_hash('admin123', PASSWORD_DEFAULT)
-- Résultat typique:
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- Insérer des livres de test
INSERT INTO books (title, author, description, isbn, publisher, category_id, price, stock, pages, publication_year, cover_image) VALUES
('Fondation', 'Isaac Asimov', 'Une odyssée galactique où une fondation préserve la connaissance.', '978-2-07-032380-8', 'Denoël', 1, 19.99, 50, 560, 1951, "/public/assets/images/fondation_by_isaac_asimov.jpg"),
('Dune', 'Frank Herbert', 'Un epic space opera sur la planète Arrakis et les intrigues de pouvoir.', '978-2-266-10629-5', 'Pocket', 1, 22.99, 45, 720, 1965, "/public/assets/images/dune_by_frank_herbert.jpg"),
('Le Seigneur des Anneaux', 'J.R.R. Tolkien', 'Une quête épique pour détruire un anneau magique.', '978-2-253-04693-0', 'Le Livre de Poche', 4, 24.99, 60, 1500, 1954, "/public/assets/images/le_seigneur_des_anneaux_by_jrr.jpg"),
('Orgueil et Préjugés', 'Jane Austen', 'Une histoire de romance et de malentendus en Angleterre victorienne.', '978-2-07-036694-0', 'Gallimard', 2, 18.50, 35, 408, 1813, "/public/assets/images/orgueil_et_prejuges_by_jane_austen.jpg"),
('Cryptonomicon', 'Neal Stephenson', 'Un thriller technologique mélangeant histoire et cryptographie.', '978-2-266-12819-9', 'Pocket', 1, 26.99, 30, 960, 1999, "/public/assets/images/cryptonomicon_by_neal_stephenson.jpg"),
('Harry Potter à l\'école des sorciers', 'J.K. Rowling', 'Les premières aventures d\'un jeune sorcier.', '978-2-07-053304-2', 'Gallimard', 6, 20.99, 70, 320, 1997, "/public/assets/images/harry_potter_a_lecole_des_sorciers_by_jk_rowling.jpg"),
('Le Hobbit', 'J.R.R. Tolkien', 'L\'aventure d\'un hobbit réticent dans une quête épique.', '978-2-253-04816-3', 'Le Livre de Poche', 4, 16.99, 55, 400, 1937, "/public/assets/images/the_hobbit_by_jrr.jpg"),
('Neuromancien', 'William Gibson', 'Un cyber-punk classique qui a inspiré une génération.', '978-2-290-00001-1', 'Pocket', 1, 18.99, 40, 280, 1984, "/public/assets/images/neuromancien_by_william_gibson.jpg"),
('Le Nom du Vent', 'Patrick Rothfuss', 'Les mémoires d\'un sorcier dans un monde de fantasy complexe.', '978-2-226-19984-2', 'Albin Michel', 4, 24.50, 48, 656, 2007, "/public/assets/images/le_nom_du_vent_by_patrick_rothfuss.jpg"),
('Le Meilleur des Mondes', 'Aldous Huxley', 'Une dystopie d\'un futur supposé idéal.', '978-2-07-031768-5', 'Plon', 1, 19.99, 42, 300, 1932, "/public/assets/images/le_meilleur_des_mondes_byy_aldous_huxley.jpg");

-- Insérer un utilisateur test
INSERT INTO users (username, email, password_hash, role) VALUES
('jean.dupont', 'jean@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- ============================================
-- VÉRIFICATIONS ET RÉSUMÉ
-- ============================================
-- Afficher le nombre d'enregistrements
SELECT 'Users:' as 'Table', COUNT(*) as 'Count' FROM users
UNION ALL
SELECT 'Categories', COUNT(*) FROM categories
UNION ALL
SELECT 'Books', COUNT(*) FROM books
UNION ALL
SELECT 'Cart', COUNT(*) FROM cart
UNION ALL
SELECT 'Orders', COUNT(*) FROM orders;

-- ============================================
-- NOTES PÉDAGOGIQUES
-- ============================================
/*
 * 1. RELATIONS:
 *    - users (1) -> (N) cart : Un utilisateur peut avoir plusieurs articles au panier
 *    - categories (1) -> (N) books : Une catégorie peut contenir plusieurs livres
 *    - books (1) -> (N) cart : Un livre peut être ajouté plusieurs fois par différents utilisateurs
 *    - users (1) -> (N) orders : Un utilisateur peut passer plusieurs commandes
 *    - orders (1) -> (N) order_items : Une commande contient plusieurs articles
 *
 * 2. SÉCURITÉ:
 *    - Les mots de passe sont hashés avec bcrypt (PASSWORD_DEFAULT)
 *    - Les requêtes utilisent des prepared statements pour éviter SQL injection
 *    - Les rôles (user/admin) contrôlent les accès
 *
 * 3. NORMALISATION:
 *    - Chaque table a une clé primaire (id)
 *    - Les clés étrangères maintiennent l'intégrité référentielle
 *    - Les timestamps tracent la création/modification
 *    - Les INDEX optimisent les recherches fréquentes
 *
 * 4. UTILISATION:
 *    - Importer ce script dans MySQL: mysql -u root < schema.sql
 *    - Ou utiliser phpMyAdmin
 */
