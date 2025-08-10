<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   membre.php : interface utilisateur connectée en tant que membre
   Cette page affiche le catalogue d'épices aux membres, gère la session,
   le menu de navigation, le formulaire de profil et les interactions AJAX.
   ============================================================================ */

session_start();

// Si l'utilisateur n'est pas membre, on le redirige vers la page de connexion
if (!isset($_SESSION['courriel']) || $_SESSION['role'] !== 'M') {
    header("Location: /TP3/client/public/pages/connexion.html" . $msg);
    exit;
}

// Message GET pour affichage via toast (ex. message de succès ou erreur)
$msg = isset($_GET['message']) ? urldecode($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Page Membre</title>

    <!-- Icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="icon" href="/TP3/client/public/images/favicon.ico">

    <!-- Feuilles de style -->
    <link href="/TP3/client/public/utilitaires/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/TP3/client/public/css/styles.css">

    <!-- jQuery + config + toast -->
    <script src="/TP3/client/public/utilitaires/jquery-3.7.1.min.js"></script>
    <script src="/TP3/client/public/js/toast.js"></script>
    <script src="/TP3/client/public/js/config.js"></script>

    <!-- Modules JavaScript -->
    <script src="/TP3/client/Epices/RequetesEpices.js"></script>
    <script src="/TP3/client/Epices/VuesEpices.js"></script>
    <script src="/TP3/client/Connexion/RequetesConnexion.js"></script>
    <script src="/TP3/client/Membre/RequetesMembres.js"></script>
    <script src="/TP3/client/Membre/VuesMembres.js"></script>
    <script src="/TP3/client/Admin/RequetesAdmin.js"></script>
    <script src="/TP3/client/Admin/VuesAdmin.js"></script>
    <script src="/TP3/client/public/js/panier.js"></script>

</head>
<body>
<!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
      <!-- Logo / Accueil -->
      <a class="navbar-brand" href="#" onclick="getEpices(afficherEpices)">
        Épices du Monde
      </a>
      <button class="navbar-toggler" type="button"
              data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Liens principaux -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          <li class="nav-item">
            <a class="nav-link active" href="#" onclick="getEpices(afficherEpices)">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="logout()">Déconnexion</a>
          </li>
        </ul>

        <!-- Avatar cliquable + Panier -->
        <!-- Prénom et Nom juste à côté -->
        <span class="me-4 fw-semibold" id="nom">
            
        </span>
        <div class="d-flex align-items-center">
            <!-- Avatar agrandi (40×40) et clic pour ouvrir modal “Mon profil” -->
            <img
                id="avatarNavbar"
                src=""
                class="rounded-circle me-3"
                alt="Avatar"
                style="width:60px; height:60px; cursor:pointer;"
                data-bs-toggle="modal"
                data-bs-target="#modalProfil"
            />

            <!-- Bouton Panier avec compteur -->
            <button class="btn btn-outline-dark btn-sm position-relative d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalPanier">
                <i class="bi bi-cart-fill me-1 fs-6"></i>
                Panier
                <span id="badgePanier"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark text-white">
                    0
                </span>
            </button>
        </div>
      </div>
    </div>
  </nav>

<!-- ENTÊTE DE PAGE PERSONNALISÉ -->
<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Épices du Monde</h1>
            <p class="lead fw-normal text-white-50 mb-0">Explorez nos épices sélectionnées</p>
        </div>
    </div>
</header>

<!-- SECTION PRINCIPALE POUR LES ÉPICES -->
<main class="container my-5" id="contenu">
    <!-- Injecté dynamiquement via JS -->
</main>

<!-- TOAST POUR MESSAGES FLASH -->
<div class="toast posToast" id="toastMessage" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header">
        <img src="/TP3/client/public/images/iconToast.png" class="rounded me-2" alt="Toast icon" style="width: 20px; height: 20px;">
        <strong class="me-auto">Message</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="textToast"></div>
</div>

<!-- MODAL POUR MODIFIER LE PROFIL -->
    <div class="modal fade" id="modalProfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form id="formProfil">
                <!-- Header : on affiche le nom complet ici -->
                <div class="modal-header">
                <h5 class="modal-title">
                    Profil de <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <!-- Prénom -->
                <div class="mb-3">
                    <label for="prenomProfil">Prénom</label>
                    <input type="text" id="prenomProfil" name="prenom" class="form-control" required>
                </div>
                <!-- Nom -->
                <div class="mb-3">
                    <label for="nomProfil">Nom</label>
                    <input type="text" id="nomProfil" name="nom" class="form-control" required>
                </div>
                <!-- Mot de passe -->
                <div class="mb-3">
                    <label for="mdpProfil">Nouveau mot de passe</label>
                    <input type="password" id="mdpProfil" name="motdepasse" class="form-control">
                    <small class="text-muted">Laissez vide pour conserver l'ancien</small>
                </div>
                <!-- Photo -->
                <div class="mb-3">
                    <label for="photoProfil">Photo de profil</label>
                    <input type="file" id="photoProfil" name="photo" class="form-control" accept="image/*">
                </div>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- MODAL : Contenu du panier -->
    <div class="modal fade" id="modalPanier" tabindex="-1" aria-labelledby="modalPanierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalPanierLabel">Votre panier</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
            <table class="table table-hover align-middle">
            <thead>
                <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
                <th></th>
                </tr>
            </thead>
            <tbody id="contenuPanier">
                <!-- Ligne dynamique injectée ici -->
            </tbody>
            </table>
            <div class="text-end fw-bold fs-5">
            Total à payer : <span id="totalPanier">0.00 $</span>
            </div>
        </div>
        <div class="modal-footer">
            <button id="viderPanier" class="btn btn-danger">Vider le panier</button>
            <button class="btn btn-success" onclick="payer()">Payer</button>
            <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
        </div>
    </div>
    </div>

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script initial : charger les épices et afficher message s'il y en a un -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        getEpices(afficherEpices);

        document.getElementById('modalPanier')
            .addEventListener('show.bs.modal', afficherPanier);

        document.getElementById('viderPanier')
            .addEventListener('click', () => {
                localStorage.removeItem('panier');
                updatePanierCount();
                afficherPanier();
                montrerToast('Panier vidé.');
            });

        <?php if (!empty($msg)): ?>
            montrerToast('<?= addslashes($msg) ?>');
        <?php endif; ?>
    });
</script>
</body>
</html>
