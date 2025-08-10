/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   RequetesAdmin.js : Fonctions pour appeler les actions CRUD des épices
   et la gestion des membres via AJAX (Fetch API) pour le panneau admin.
   ============================================================================ */

// On utilise BASE_URL défini dans config.js pour construire les URLs

/**
 * getEpices(callback, type, search)
 * Récupère la liste des épices, optionnellement filtrée.
 */
function getEpices(callback, type = '', search = '') {
    let url = BASE_URL + '/epices/lister';
    const params = new URLSearchParams();
    if (type)   params.append('type',   type);
    if (search) params.append('search', search);
    if ([...params].length) url += '?' + params.toString();

    fetch(url)
        .then(resp => {
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            return resp.json();
        })
        .then(data => callback(data))
        .catch(err => console.error('getEpices error:', err));
}

/**
 * ajouterEpice(formData, callback, errorCallback)
 * Crée une nouvelle épice en envoyant FormData (données + image)
 */
function ajouterEpice(formData, callback, errorCallback) {
    fetch(BASE_URL + '/epices/ajouter', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => callback(data))
    .catch(error => {
        console.error("Erreur lors de l'ajout de l'épice :", error);
        if (errorCallback) errorCallback(error);
    });
}

/**
 * modifierEpice(formData, callback, errorCallback)
 * Met à jour une épice existante via FormData
 */
function modifierEpice(formData, callback, errorCallback) {
    fetch(BASE_URL + '/epices/modifier', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => callback(data))
    .catch(error => {
        console.error("Erreur lors de la modification de l'épice :", error);
        if (errorCallback) errorCallback(error);
    });
}

/**
 * supprimerEpice(id, callback, errorCallback)
 * Supprime une épice en envoyant son ID en JSON
 */
function supprimerEpice(id, callback, errorCallback) {
    fetch(BASE_URL + '/epices/supprimer', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => callback(data))
    .catch(error => {
        console.error("Erreur lors de la suppression de l'épice :", error);
        if (errorCallback) errorCallback(error);
    });
}

/**
 * listerMembres(callback)
 * Récupère la liste de tous les membres pour l'administration
 */
function listerMembres(callback) {
    fetch(BASE_URL + '/membres/listerMembre')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            return response.json();
        })
        .then(data => callback(data))
        .catch(error => console.error('Erreur lors de la récupération des membres :', error));
}

/**
 * changerStatutMembre(id, statut, callback, errorCallback)
 * Active (statut = 'A') ou désactive (statut = 'I') un compte membre
 */
function changerStatutMembre(id, statut, callback, errorCallback) {
    console.log("changerStatutMembre appelé avec id =", id, "statut =", statut);

    const action = (statut === 'A') ? 'activer' : 'desactiver';
    fetch(BASE_URL + `/membres/${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => callback(data))
    .catch(error => {
        console.error('Erreur lors du changement de statut membre :', error);
        if (errorCallback) errorCallback(error);
    });
}

