/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   RequetesConnexion.js : gestion de la connexion et déconnexion en AJAX
   ============================================================================ */

/**
 * verifierConnexion(callback)
 * Envoie les identifiants (courriel et mot de passe) au serveur pour vérifier la connexion
 * @param {Function} callback - fonction à appeler avec le résultat JSON du serveur
 */
function verifierConnexion(callback) {
    // On récupère et on nettoie les valeurs des champs du formulaire
    const data = {
        courriel: document.getElementById('courriel').value.trim(),
        motdepasse: document.getElementById('motdepasse').value.trim()
    };

    // Requête POST JSON vers l'API de connexion
    fetch(BASE_URL + '/connexion/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)  // On envoie les données sérialisées
    })
    .then(response => response.json()) // On parse la réponse en JSON
    .then(result => callback(result))  // On transmet le résultat au callback
    .catch(() => {
        // En cas d'erreur réseau, on affiche un message sous le formulaire
        document.getElementById('erreurConnexion').textContent = 
            "Erreur de communication avec le serveur.";
    });
}

/**
 * deconnexion()
 * Appelle le serveur pour déconnecter l'utilisateur et redirige ensuite
 */
function logout() {
    // Requête GET pour déconnexion (pas de body nécessaire)
    fetch(BASE_URL + '/connexion/logout')
    .then(response => response.json()) // On parse la réponse en JSON
    .then(data => {
        if (data.message) {
            // Si tout s'est bien passé, on retourne à la page d'accueil
            window.location.href = BASE_URL + "/index.php";
        } else if (data.erreur) {
            // Si le serveur renvoie une erreur, on affiche un toast
            montrerToast(data.erreur);
        }
    })
    .catch(() => {
        // En cas d'erreur réseau, on log l'erreur dans la console
        console.error("Erreur lors de la déconnexion");
    });
}
