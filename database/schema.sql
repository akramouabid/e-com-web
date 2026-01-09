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
('Romance', "Histoires d'amour et relations"),
('Mystère', 'Thrillers et livres policiers'),
('Fantasy', 'Mondes fantastiques et magie'),
('Non-fiction', 'Biographies et essais'),
('Jeunesse', 'Livres pour enfants et ados');


-- Insérer l'utilisateur administrateur (password: admin123)
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@gmail.com', 'admin123', 'admin');

-- Note: Le hash ci-dessus est un placeholder. Utilisez PHP pour générer:
-- password_hash('admin123', PASSWORD_DEFAULT)
-- Résultat typique:
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- Insérer des livres de test
INSERT INTO books 
(title, author, description, isbn, publisher, category_id, price, stock, pages, publication_year, cover_image) 
VALUES
('Fondation', 'Isaac Asimov', 
 'Une odyssée galactique où une fondation préserve la connaissance.', 
 '9782070323808', 'Denoël', 1, 19.99, 50, 560, 1951, "image1.jpg"),

('Dune', 'Frank Herbert', 
 'Un epic space opera sur la planète Arrakis et les intrigues de pouvoir.', 
 '9782266106295', 'Pocket', 1, 22.99, 45, 720, 1965, "image2.jpg"),

('Le Seigneur des Anneaux', 'J.R.R. Tolkien', 
 'Une quête épique pour détruire un anneau magique.', 
 '9782253046930', 'Le Livre de Poche', 4, 24.99, 60, 1500, 1954, "image3.jpg"),


('Cryptonomicon', 'Neal Stephenson', 
 'Un thriller technologique mélangeant histoire et cryptographie.', 
 '9782266128199', 'Pocket', 1, 26.99, 30, 960, 1999, "image4.jpg"),

('Harry Potter à l''école des sorciers', 'J.K. Rowling', 
 'Les premières aventures d''un jeune sorcier.', 
 '9782070533042', 'Gallimard', 6, 20.99, 70, 320, 1997, "image5.jpg"),

('Le Hobbit', 'J.R.R. Tolkien', 
 'L''aventure d''un hobbit réticent dans une quête épique.', 
 '9782253048163', 'Le Livre de Poche', 4, 16.99, 55, 400, 1937, "image6.jpg"),

('Neuromancien', 'William Gibson', 
 'Un cyberpunk classique qui a inspiré une génération.', 
 '9782290000011', 'Pocket', 1, 18.99, 40, 280, 1984, "image7.jpg"),

('Le Nom du Vent', 'Patrick Rothfuss', 
 'Les mémoires d''un sorcier dans un monde de fantasy complexe.', 
 '9782226199842', 'Albin Michel', 4, 24.50, 48, 656, 2007, "image8.jpg"),

('Le Meilleur des Mondes', 'Aldous Huxley', 
 'Une dystopie d''un futur supposé idéal.', 
 '9782070317685', 'Plon', 1, 19.99, 42, 300, 1932, "image9.jpg"),;

-- Insérer un utilisateur test
INSERT INTO users (username, email, password_hash, role) VALUES
('user1', 'user@gmail.com', 'user123', 'user');
