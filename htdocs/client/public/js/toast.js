/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP2 - E25
   ---------------------------------------------------------------------------
   toast.js : fonction pour afficher un toast Bootstrap simple et réutilisable
   ============================================================================ */

/**
 * montrerToast(message)
 * Affiche un toast Bootstrap avec le texte passé en paramètre.
 * @param {string} message - Texte à afficher dans le toast.
 */
function montrerToast(message) {
    // 1) Si le message est vide ou ne contient que des espaces, on ne fait rien
    if (message.trim() === '') {
        return;
    }

    // 2) On récupère l'élément HTML du toast et son corps
    const toastEl = document.getElementById('toastMessage');
    const toastBody = document.getElementById('textToast');

    // 3) On place le message dans la div du toast
    toastBody.textContent = message;

    // 4) Initialisation et affichage via Bootstrap Toast
    //    new bootstrap.Toast(toastEl) : crée une instance de Toast
    //    .show() : affiche le toast et gère la temporisation
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
