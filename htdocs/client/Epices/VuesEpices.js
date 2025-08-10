/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   VuesEpices.js : fonctions pour afficher les épices et leurs notes en étoiles
   ============================================================================ */

/**
 * genererEtoiles(note)
 * Génère une suite d'icônes étoiles (pleines, demi, vides) selon la note (0 à 5)
 * @param {number} note - note de l'épice (peut avoir un .5)
 * @returns {string} HTML contenant les icônes étoile
 */
function genererEtoiles(note) {
    // On arrondit à l'entier inférieur pour compter les étoiles pleines
    const fullStars = Math.floor(note);
    // Est-ce qu'il reste au moins 0.5 pour une demi-étoile ?
    const halfStar = (note - fullStars) >= 0.5;
    // On initialise la chaîne de caractères HTML
    let html = '';

    // Boucle pour ajouter les étoiles pleines
    for (let i = 0; i < fullStars; i++) {
        html += '<i class="bi bi-star-fill text-warning"></i> ';
    }

    // Si demi-étoile, on l'ajoute après les pleines
    if (halfStar) {
        html += '<i class="bi bi-star-half text-warning"></i> ';
    }

    // Calcul du nombre d'étoiles vides restantes pour arriver à 5
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        html += '<i class="bi bi-star text-warning"></i> ';
    }

    return html;
}

/**
 * afficherEpices(epices)
 * Affiche une grille de cartes pour chaque épice dans la page visiteur
 * @param {Array<Object>} epices - liste d'épices reçue du serveur
 */
function afficherEpices(epices) {
    const contenu = document.getElementById('contenu');
    contenu.innerHTML = `
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                </div>
            </div>
        </section>
    `;

    const row = contenu.querySelector('.row');
    const estMembre = window.location.pathname.includes("membre.php");

    epices.forEach(epice => {
        const imageUrl = `/TP3/client/public/images/${encodeURIComponent(epice.image)}`;

        let carte = `
            <div class="col mb-5">
                <div class="card h-100">
                    <img class="card-img-top" src="${imageUrl}" alt="${epice.nom}">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <h5 class="fw-bolder">${epice.nom}</h5>
                            <div>${genererEtoiles(epice.note)}</div>
                            <small class="text-muted">${epice.type}</small><br>
                            <strong>${Number(epice.prix).toFixed(2)} $</strong>
                        </div>
                    </div>
        `;

        if (estMembre) {
            carte += `
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <button class="btn btn-outline-dark mt-auto btn-ajouter-panier"
                                data-epice='${JSON.stringify(epice).replace(/'/g, "&apos;").replace(/"/g, "&quot;")}'>
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
            `;
        }

        carte += `
                </div>
            </div>
        `;

        row.innerHTML += carte;
    });

    if (estMembre) {
        document.querySelectorAll('.btn-ajouter-panier').forEach(btn => {
            btn.addEventListener('click', () => {
                const epice = JSON.parse(btn.dataset.epice);
                ajouterAuPanier(epice);
            });
        });
    }
}