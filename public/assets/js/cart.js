/**
 * Fonctions JavaScript pour le panier
 */

// Mettre à jour le compteur du panier
function updateCartCount() {
    fetch('../../src/api/get-cart-count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('cart-count');
                if (badge) {
                    badge.textContent = data.count;
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour du compteur:', error);
        });
}

// Charger le compteur au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});