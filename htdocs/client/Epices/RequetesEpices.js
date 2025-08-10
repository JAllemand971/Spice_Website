/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   RequetesEpicesVisiteur.js : fonction pour récupérer les épices pour les visiteurs
   ============================================================================ */

/**
 * getEpicesVisiteur(callback)
 * Envoie une requête GET pour obtenir les 8 épices les mieux notées
 * Affiche les données dans la page visiteur via la fonction callback
 *
 * @param {Function} callback - fonction à appeler avec le tableau d'épices
 */
function getEpicesVisiteur(callback) {
    // On envoie la requête au serveur
    fetch(BASE_URL + '/epices/listerVisiteur')
        .then(response => {
            // On convertit la réponse en JSON
            return response.json();
        })
        .then(data => {
            // On passe le tableau d'épices à la fonction callback
            callback(data);
        })
        .catch(error => {
            // En cas d'erreur réseau, on affiche un message dans la console
            console.error('Erreur lors de la récupération des épices :', error);
        });
}
