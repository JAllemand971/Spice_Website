/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   VuesConnexion.js : initialise le formulaire de connexion et gère son envoi
   ============================================================================ */

/**
 * initialiserFormulaireConnexion()
 * Lie le formulaire de connexion à la fonction AJAX verifierConnexion
 * Empêche le rechargement de la page et gère la redirection ou l'affichage d'erreur
 */
function initialiserFormulaireConnexion() {
    // On cherche le formulaire d'identification par son ID
    const form = document.getElementById('formConnexion');

    // Si le formulaire existe sur la page
    if (form) {
        // On écoute l'événement "submit" (clic sur "Se connecter")
        form.addEventListener('submit', function(e) {
            // Empêche le navigateur de soumettre normalement et de rafraîchir
            e.preventDefault();

            // Appel à la fonction AJAX définie dans RequetesConnexion.js
            verifierConnexion(function(response) {
                // Si le serveur renvoie une URL de redirection, on y va
                if (response.redirection) {
                    window.location.href = response.redirection;
                }
                // Sinon, si le serveur renvoie une erreur, on l'affiche en toast
                else if (response.erreur) {
                    montrerToast(response.erreur);
                }
            });
        });
    }
}

// On attend que le DOM soit complètement chargé avant d'initialiser
document.addEventListener('DOMContentLoaded', initialiserFormulaireConnexion);
