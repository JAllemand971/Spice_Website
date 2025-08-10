/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   RequetesMembre.js : fonctions pour les requêtes AJAX liées aux membres
   ============================================================================ */

/**
 * inscrireMembre(callback)
 * Envoie les informations du formulaire d'inscription au serveur.
 * Effectue quelques vérifications côté client avant envoi.
 *
 * @param {Function} callback - fonction à appeler avec la réponse JSON du serveur
 */
function inscrireMembre(callback) {
    const formData = new FormData();

    formData.append('nom', document.getElementById('nom').value.trim());
    formData.append('prenom', document.getElementById('prenom').value.trim());
    formData.append('courriel', document.getElementById('courriel').value.trim());
    formData.append('sexe', document.querySelector('input[name="sexe"]:checked')?.value);
    formData.append('datenaissance', document.getElementById('datenaissance').value);
    formData.append('motdepasse', document.getElementById('motdepasse').value);
    formData.append('confirmation', document.getElementById('confirmation').value);

    const photoInput = document.getElementById('photo');
    if (photoInput.files.length > 0) {
        formData.append('photo', photoInput.files[0]);
    }

    document.querySelectorAll('.text-danger').forEach(div => div.textContent = "");

    fetch(BASE_URL + '/membres/ajouter', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())     // Attend un JSON propre
        .then(callback)              // Appelle ta fonction callback avec les données reçues
        .catch(err => {
            document.getElementById('erreurServeur').textContent =
                "Erreur serveur : " + err.message;
        });
}


/**
 * modifierProfil(formData, callback, errorCallback)
 * Envoie les modifications de profil au serveur (nom, prénom, mot de passe).
 * N'inclut pas le courriel qui est non-modifiable.
 *
 * @param {FormData} formData - données du formulaire profil à envoyer
 * @param {Function} callback - fonction appelée si la réponse est OK
 * @param {Function} errorCallback - fonction appelée si erreur de réseau ou réponse invalide
 */
function modifierProfil(formData, callback, errorCallback) {
    const hasPhoto = formData.has('photo') && formData.get('photo')?.name;

    let options = {
        method: 'POST',
        body: formData
    };

    if (!hasPhoto) {
        const nom = formData.get('nom')?.trim() || '';
        const prenom = formData.get('prenom')?.trim() || '';
        const motdepasse = formData.get('motdepasse')?.trim();
        const payload = { nom, prenom };
        if (motdepasse) payload.motdepasse = motdepasse;

        options = {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json'
            }
        };
    }

    fetch(BASE_URL + '/membre/modifierProfil', options)
        .then(res => {
            if (!res.ok) return res.text().then(t => { throw new Error(t); });
            return res.json();
        })
        .then(data => callback(data))
        .catch(err => {
            console.error('Erreur modifierProfil :', err);
            if (errorCallback) errorCallback(err);
        });       
}

