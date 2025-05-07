document.addEventListener('DOMContentLoaded', function() {
    // 1. D'abord tester la connexion de base
    async function testPanierConnection() {
        try {
            const response = await fetch('/panier/ajouter-produit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'test=1'
            });
            return await response.json();
        } catch (error) {
            console.error("Test de connexion échoué:", error);
            return {success: false};
        }
    }

    // 2. Votre fonction existante améliorée
    async function ajouterAuPanier(bouton) {
        // [Votre code existant inchangé...]
    }

    // 3. Initialisation
    async function initPanier() {
        // Test initial
        const testResult = await testPanierConnection();
        console.log("Résultat du test:", testResult);
        
        if (!testResult.success) {
            alert("Attention : Le panier ne répond pas. Veuillez recharger la page.");
            return;
        }

       // Version corrigée avec gestion des erreurs
       document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            try {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
                // URL CORRIGÉE (sans le /panier/ en double)
                const response = await fetch('/panier/ajouter-produit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: button.dataset.id,
                        nom: "Produit Test",
                        prix: 19.99,
                        image: "test.jpg"
                    })
                });
    
                const result = await response.json();
                console.log("Résultat:", result);
                
                if (result.success) {
                    // Mise à jour du compteur
                    document.querySelectorAll('.panier-compteur').forEach(el => {
                        el.textContent = result.count;
                    });
                    alert("Produit ajouté avec succès !");
                }
            } catch (error) {
                console.error("Erreur:", error);
                alert("Erreur lors de l'ajout au panier");
            } finally {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-shopping-cart"></i> Ajouter';
            }
        });
    });
    }

    // Démarrer
    initPanier();
});