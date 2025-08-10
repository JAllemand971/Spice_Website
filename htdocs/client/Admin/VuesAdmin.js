/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   VuesAdmin.js : Gère l'affichage des tableaux d'épices et de membres,
   le remplissage du formulaire modal et les interactions utilisateur.
   ============================================================================ */


    /**
     * handleResponse(resp)
     * Gère la réponse après un ajout, modification ou suppression
     */
    function handleResponse(resp) {
        if (resp.message) {
            montrerToast(resp.message);

            // Recharge les épices si on est sur la page des épices
            if (document.getElementById('contenuEpices')) {
                getEpices(afficherTableauEpices);
            }

            // Recharge les membres si on est sur la page des membres
            if (document.getElementById('contenuMembres')) {
                listerMembres(afficherMembres);
            }

            // Ferme la fenêtre modale
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalAjout'));
            if (modal) modal.hide();

            // Réinitialise le formulaire d'épice
            const formEpice = document.getElementById("formEpice");
            if (formEpice) {
                formEpice.reset();
            }
        } else if (resp.erreur) {
            montrerToast(resp.erreur);
        }
    }

    /**
     * handleError(error)
     * Gère les erreurs réseau ou de réponse serveur
     */
    function handleError(error) {
        let msg = error.message || "Erreur serveur inconnue.";

        // Cacher toutes les erreurs HTML d'abord
        document.getElementById("erreurImage").textContent = "";

        let parsed = null;
        try {
            parsed = JSON.parse(error.message);
        } catch {}

        // Si on a un vrai message d'erreur JSON (depuis PHP)
        if (parsed && parsed.erreur) {
            msg = parsed.erreur;
        }

        // Vérifie s'il y a "image" dans le message
        if (msg.toLowerCase().includes("image")) {
            document.getElementById("erreurImage").textContent = msg;
        } else {
            montrerToast(msg);
        }
    }


/**
 * afficherTableauEpices(epices)
 * Affiche un tableau HTML avec les données des épices
 */
function afficherTableauEpices(epices) {
    const container = document.getElementById("contenuEpices");
    if (!container) return;

    let html = `
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    epices.forEach(e => {
        const imgUrl = `/TP3/client/public/images/${encodeURIComponent(e.image)}`;
        html += `
            <tr>
                <td>${e.ide}</td>
                <td>${e.nom}</td>
                <td>${parseFloat(e.prix).toFixed(2)} $</td>
                <td>${e.type}</td>
                <td>${e.note}</td>
                <td><img src="${imgUrl}" alt="${e.nom}" height="40"></td>
                <td>
                    <button class="btn btn-sm btn-warning me-1"
                            data-epice='${JSON.stringify(e).replace(/'/g, "&apos;")}'
                            onclick="remplirFormulaire(this)">
                        Modifier
                    </button>
                    <button class="btn btn-sm btn-danger"
                            onclick="supprimerEpice(${e.ide}, handleResponse, handleError)">
                        Supprimer
                    </button>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
}

/**
 * remplirFormulaire(btn)
 * Remplit automatiquement le formulaire avec les données de l'épice
 * @param {HTMLElement} btn - bouton qui a déclenché l'action
 */
function remplirFormulaire(btn) {
    // Récupère le JSON encodé depuis l'attribut data-epice
    const epiceJson = btn.getAttribute('data-epice').replace(/&apos;/g, "'");
    const epice = JSON.parse(epiceJson);

    // Remplit chaque champ du formulaire avec les valeurs de l'épice
    document.getElementById("ide").value         = epice.ide;
    document.getElementById("nom").value         = epice.nom;
    document.getElementById("type").value        = epice.type;
    document.getElementById("prix").value        = epice.prix;
    document.getElementById("vendeur").value     = epice.vendeur;
    document.getElementById("description").value = epice.description;
    document.getElementById("note").value        = epice.note;
    document.getElementById("oldImage").value    = epice.image;

    // Rend le champ image facultatif en modification
    document.getElementById("image").required = false;

    // Ouvre la modale de modification
    new bootstrap.Modal(document.getElementById('modalAjout')).show();
}


/**
 * afficherMembres(membres)
 * Affiche la table des membres (adminMembres.php)
 */
function afficherMembres(membres) {
    const container = document.getElementById("contenuMembres");
    if (!container) return;

    let html = `
      <table class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Courriel</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
    `;

    membres.forEach(m => {
        const newStatut = (m.statut === 'A') ? 'I' : 'A';
        const labelBtn  = (m.statut === 'A') ? 'Désactiver' : 'Activer';
        html += `
          <tr>
            <td>${m.id}</td>
            <td>${m.nom}</td>
            <td>${m.prenom}</td>
            <td>${m.courriel}</td>
            <td>${m.statut}</td>
            <td>
              <button class="btn btn-sm btn-${newStatut === 'A' ? 'success' : 'warning'}"
                      onclick="changerStatutMembre(${m.id}, '${newStatut}', handleResponse, handleError)">
                ${labelBtn}
              </button>
            </td>
          </tr>
        `;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
}

// Chargement automatique au démarrage selon la page
window.addEventListener('DOMContentLoaded', () => {
    // Si c’est la page Épices
    const containerEpices = document.getElementById('contenuEpices');
    if (containerEpices) {
        getEpices(afficherTableauEpices);

        // Filtrage et recherche
        const filterType = document.getElementById('filterType');
        const searchNom  = document.getElementById('searchNom');
        const btnSearch  = document.getElementById('btnSearch');

        if (btnSearch) {
            btnSearch.addEventListener('click', () => {
                const type   = filterType.value;
                const search = searchNom.value.trim();
                getEpices(afficherTableauEpices, type, search);
            });
        }

        if (filterType) {
            filterType.addEventListener('change', () => {
                const type   = filterType.value;
                const search = searchNom.value.trim();
                getEpices(afficherTableauEpices, type, search);
            });
        }

        // Déclenche la recherche quand on appuie sur "Entrée" dans le champ searchNom
        if (searchNom) {
            searchNom.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Empêche l'envoi du formulaire si jamais
                    const type = filterType.value;
                    const search = searchNom.value.trim();
                    getEpices(afficherTableauEpices, type, search);
                }
            });
        }
    }

    // Si c’est la page Membres (adminMembres.php)
    if (document.getElementById('contenuMembres')) {
        listerMembres(afficherMembres);
    }

    // Ajout d'un gestionnaire pour le formulaire d'ajout/modification
    const formEpice = document.getElementById("formEpice");
    if (formEpice) {
        formEpice.addEventListener("submit", function (e) {
            e.preventDefault(); // Empêche le comportement normal (rechargement de page)

            const formData = new FormData(formEpice);

            if (!formData.get("ide")) {
                // Nettoyer message d'erreur
                document.getElementById("erreurImage").textContent = "";
                // Si ide est vide => nouvel ajout
                ajouterEpice(formData, handleResponse, handleError);
            } else {
                // Nettoyer message d'erreur
                document.getElementById("erreurImage").textContent = "";
                // Sinon => c’est une modification
                modifierEpice(formData, handleResponse, handleError);
            }
        });
    }
});
