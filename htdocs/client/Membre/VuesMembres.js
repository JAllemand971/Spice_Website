/* =============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   VueMembre.js : initialise les formulaires d'inscription et de profil
   Ce fichier attend le DOM chargé, puis lie les événements aux formulaires
   ============================================================================= */

// On attend que toute la page soit chargée avant d'exécuter le code
document.addEventListener('DOMContentLoaded', function() {

    // === Inscription des nouveaux membres ===
    const formInscription = document.getElementById('formInscription');
    if (formInscription) {
        formInscription.addEventListener('submit', function (e) {
            e.preventDefault(); // empêcher le rechargement

            // Nettoyer toutes les erreurs précédentes
            document.querySelectorAll('.text-danger').forEach(el => el.textContent = "");

            inscrireMembre(function (response) {
                if (response.message) {
                    montrerToast(response.message);
                    setTimeout(() => window.location.href = "/TP3/index.php", 2000);
                } else if (response.erreur) {
                    const msg = response.erreur.toLowerCase();

                    // Match spécifique selon contenu du message d'erreur
                    if (msg.includes('nom')) {
                        document.getElementById("erreurNom").textContent = response.erreur;
                    } else if (msg.includes('prénom') || msg.includes('prenom')) {
                        document.getElementById("erreurPrenom").textContent = response.erreur;
                    } else if (msg.includes('courriel')) {
                        document.getElementById("erreurCourriel").textContent = response.erreur;
                    } else if (msg.includes('sexe')) {
                        document.getElementById("erreurSexe").textContent = response.erreur;
                    } else if (msg.includes('naissance') || msg.includes('date')) {
                        document.getElementById("erreurDate").textContent = response.erreur;
                    } else if (msg.includes('correspondent')) {
                        document.getElementById("erreurConfirmation").textContent = response.erreur;
                    } else if (msg.includes('mot de passe')) {
                        document.getElementById("erreurConfirmation").textContent = response.erreur;
                    } else {
                        document.getElementById("erreurServeur").textContent = response.erreur;
                    }
                }
            });
        });
    }

    // === Modification du profil existant ===
    const formProfil = document.getElementById('formProfil');
    if (formProfil) {
        // 1) Avant tout, on charge le profil actuel pour pré-remplir le formulaire
        getProfil(function(data) {
            // data = { prenom: "...", nom: "...", photo: "..." }
            document.getElementById('prenomProfil').value = data.prenom;
            document.getElementById('nomProfil').value    = data.nom;
            // (Optionnel) si tu veux afficher un aperçu de la photo actuelle
            // document.getElementById('photoApercu').src = BASE_URL + '/serveur/Membre/photos/' + data.photo;
        }, function(err) {
            console.error('Impossible de charger le profil', err);
            montrerToast("Impossible de charger votre profil");
        });

        // 2) Puis on gère l'envoi du formulaire en AJAX
        formProfil.addEventListener('submit', function(e) {
            e.preventDefault();

            // Prépare un FormData pour inclure texte + éventuelle nouvelle photo
            const fd = new FormData(formProfil);

            // Appel AJAX pour modifier le profil
            modifierProfil(fd,
                function(resp) {
                    // Succès : resp = { message: "Profil mis à jour" }
                    montrerToast(resp.message);
                    // On recharge les infos affichées en haut (nom + avatar)
                    getProfil();
                    // On remet à zéro le formulaire (vide mdp + input file)
                    formProfil.reset();
                    // On cache la modal
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById('modalProfil')
                    );
                    if (modal) modal.hide();
                },
                function(err) {
                    // En cas d'erreur, on essaie de lire un JSON { erreur }
                    let msg = "Erreur serveur inattendue";
                    try {
                        const p = JSON.parse(err.message);
                        if (p.erreur) msg = p.erreur;
                    } catch {}
                    montrerToast(msg);
                }
            );
        });
    }
});

/**
 * getProfil(callback, errorCallback)
 * Récupère les infos du profil (depuis la session côté serveur).
 */
function getProfil(callback, errorCallback) {
    fetch(BASE_URL + '/membre/profil')
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            // Met à jour l'affichage (navbar et titre modal)
            document.querySelector('span.fw-semibold').textContent = `${data.prenom} ${data.nom}`;
            document.querySelector('#modalProfil .modal-title').textContent = `Profil de ${data.prenom} ${data.nom}`;
            document.getElementById('avatarNavbar').src = `/TP3/serveur/Membre/photos/${data.photo}?t=${Date.now()}`;

            if (callback) callback(data); // Continue le flux normal si besoin
        })
        .catch(err => {
            console.error('Erreur getProfil :', err);
            if (errorCallback) errorCallback(err);
        });
}
