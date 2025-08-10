/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   panier.js : Gère les opérations du panier d'achat via localStorage
   Ajout, suppression, incrémentation, affichage, et paiement AJAX
   ============================================================================ */

// Lecture/écriture dans le localStorage
function getPanier() {
    return JSON.parse(localStorage.getItem('panier')) || [];
}

function setPanier(panier) {
    localStorage.setItem('panier', JSON.stringify(panier));
    updatePanierCount();
}

// Initialiser le badge du panier au chargement
document.addEventListener('DOMContentLoaded', updatePanierCount);

// Ajoute une épice au panier (ou incrémente la quantité)
function ajouterAuPanier(epice) {
    let panier = getPanier();
    const index = panier.findIndex(e => e.ide === epice.ide);

    if (index > -1) {
        panier[index].quantite += 1;
    } else {
        panier.push({ ...epice, quantite: 1 });
    }

    setPanier(panier);
    montrerToast('Ajouté au panier !');
}

// Met à jour le badge visuel du panier
function updatePanierCount() {
    const count = getPanier().reduce((acc, item) => acc + item.quantite, 0);
    const badge = document.getElementById('badgePanier');
    if (badge) badge.textContent = count;
}

// Affiche les éléments du panier dans un tableau
function afficherPanier() {
    const panier = getPanier();
    const tbody = document.getElementById('contenuPanier');
    tbody.innerHTML = '';

    let total = 0;
    panier.forEach(item => {
        const prixTotal = item.quantite * item.prix;
        total += prixTotal;

        const ligne = document.createElement('tr');
        ligne.innerHTML = `
            <td><img src="/TP3/client/public/images/${encodeURIComponent(item.image)}" width="50" height="50" alt="${item.nom}" class="img-thumbnail"></td>
            <td>${item.nom}</td>
            <td>${item.prix.toFixed(2)} $</td>
            <td>
                <button class="btn btn-sm btn-outline-secondary me-1" onclick="decrementerQuantite(${item.ide})">–</button>
                ${item.quantite}
                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="incrementerQuantite(${item.ide})">+</button>
            </td>
            <td>${prixTotal.toFixed(2)} $</td>
            <td>
                <button class="btn btn-sm btn-outline-danger" onclick="supprimerDuPanier(${item.ide})">🗑️</button>
            </td>
        `;
        tbody.appendChild(ligne);
    });

    document.getElementById('totalPanier').textContent = total.toFixed(2) + ' $';
}

// Incrémente la quantité d’un item
function incrementerQuantite(ide) {
    let panier = getPanier();
    const item = panier.find(e => e.ide === ide);
    if (item) {
        item.quantite++;
        setPanier(panier);
        afficherPanier();
    }
}

// Diminue la quantité ou supprime l’item si quantité = 1
function decrementerQuantite(ide) {
    let panier = getPanier();
    const item = panier.find(e => e.ide === ide);
    if (item && item.quantite > 1) {
        item.quantite--;
        setPanier(panier);
        afficherPanier();
    } else if (item && item.quantite === 1) {
        supprimerDuPanier(ide);
    }
}

// Retire un item du panier
function supprimerDuPanier(ide) {
    let panier = getPanier().filter(e => e.ide !== ide);
    setPanier(panier);
    afficherPanier();
    montrerToast('Supprimé du panier');
}

// Simule un paiement via fetch
function payer() {
    const panier = getPanier();

    if (panier.length === 0) {
        montrerToast("Votre panier est vide.");
        return;
    }

    fetch("/TP3/membre/payer", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(panier)
    })
    .then(res => res.json())
    .then(data => {
        if (data.message) {
            localStorage.removeItem('panier');
            updatePanierCount();
            afficherPanier();
            montrerToast(data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalPanier'));
            if (modal) modal.hide();
        } else if (data.erreur) {
            montrerToast(data.erreur);
        }
    })
    .catch(() => montrerToast("Erreur réseau lors du paiement."));
}

// Expose certaines fonctions globalement
window.ajouterAuPanier = ajouterAuPanier;
window.incrementerQuantite = incrementerQuantite;
window.decrementerQuantite = decrementerQuantite;
window.supprimerDuPanier = supprimerDuPanier;
