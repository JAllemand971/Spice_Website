<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   Page administrateur pour la gestion des épices et des membres
   ============================================================================ */

session_start();

// Si l'utilisateur n'est pas admin, on le redirige vers la page de connexion
if (!isset($_SESSION['role']) || strtoupper($_SESSION['role']) !== 'A') {
    header("Location: /TP3/client/public/pages/connexion.html");
    exit;
}

// On récupère un message éventuel passé en paramètre pour l'afficher en toast
$msg = isset($_GET['message']) ? urldecode($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Page admin</title>

    <!-- ICONE de la page -->
    <link rel="icon" href="/TP3/client/public/images/favicon.ico" />
    <!-- Bibliothèque d'icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- CSS personnalisé -->
    <link href="/TP3/client/public/css/styles.css" rel="stylesheet" />
    <!-- CSS de Bootstrap local -->
    <link href="/TP3/client/public/utilitaires/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- JS de Bootstrap (modals, navbar responsive, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script pour afficher des toasts (messages) -->
    <script src="/TP3/client/public/js/toast.js"></script>
    <!-- Scripts pour gérer la connexion -->
    <script src="/TP3/client/Connexion/RequetesConnexion.js"></script>
    <!-- Scripts pour gérer l'affichage des épices visiteurs -->
    <script src="/TP3/client/Epices/RequetesEpices.js"></script>
    <script src="/TP3/client/Epices/VuesEpices.js"></script>
    <!-- Scripts pour l'administration (épices + membres) -->
    <script src="/TP3/client/Admin/RequetesAdmin.js"></script>
    <script src="/TP3/client/Admin/VuesAdmin.js"></script>
    <!-- Config (définit BASE_URL, etc.) -->
    <script src="/TP3/client/public/js/config.js"></script>
</head>

<!-- Au chargement, on récupère les épices et on affiche un toast si message -->
<body onload="getEpices(afficherEpices); <?php if (!empty($msg)) echo "montrerToast('" . addslashes($msg) . "');"; ?>">

<!-- NAVBAR : barre de navigation en haut -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <!-- Titre/Admin -->
        <a class="navbar-brand" href="#">ADMIN</a>
        <!-- Bouton hamburger pour écrans mobiles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Liens de navigation -->
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link active" href="#">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="#" onclick="logout()">Déconnexion</a></li>
            </ul>
            <!-- Panier (inactif) -->
            <a href="#" class="btn btn-outline-dark btn-sm position-relative d-flex align-items-center" data-bs-toggle="tooltip" title="Panier">
                <i class="bi bi-cart-fill me-1 fs-6"></i>
                Panier
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark text-white">0</span>
            </a>
        </div>
    </div>
</nav>

<!-- HEADER : titre principal de la page -->
<header class="bg-secondary py-5 text-white">
    <div class="container px-4 px-lg-5">
        <div class="text-center">
            <h1 class="display-4 fw-bold">Page d’administration</h1>
            <p class="lead">Gestion des épices et des membres</p>
        </div>
    </div>
</header>

<!-- CONTENU PRINCIPAL -->
<main class="py-5">
    <div class="container px-4">
        <div class="row mb-4">
            <!-- Carte pour gérer les épices -->
            <div class="col-md-6">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Épices</h5>
                        <p class="card-text">Liste, ajout, modification, suppression des épices.</p>
                        <!-- Lien vers la page d'administration des épices -->
                        <a href="/TP3/serveur/Admin/adminEpices.php" class="btn btn-primary">Gérer les épices</a>
                    </div>
                </div>
            </div>
            <!-- Carte pour gérer les membres -->
            <div class="col-md-6">
                <div class="card border-secondary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Membres</h5>
                        <p class="card-text">Voir, activer ou désactiver des comptes membres.</p>
                        <!-- Lien vers la page d'administration des membres (à implémenter) -->
                        <a href="/TP3/serveur/Admin/adminMembres.php" class="btn btn-secondary">Gérer les membres</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION : Catalogue d'épices -->
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <!-- Zone où JavaScript injectera les cartes d'épices -->
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center" id="zoneEpices">
                </div>
            </div>
        </section>
    </div>
</main>

<!-- TOAST : zone pour afficher les messages -->
<div class="toast align-items-center position-fixed top-50 start-50 translate-middle z-3" id="toastMessage" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header">
        <!-- Icône du toast -->
        <img src="/TP3/client/public/images/iconToast.png" class="rounded me-2" alt="Toast icon" style="width: 20px; height: 20px;">
        <strong class="me-auto">Message</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="textToast"></div>
</div>

<!-- FOOTER : bas de page -->
<footer class="py-4 bg-dark text-white">
    <div class="container text-center">
        <p class="m-0">© Épices du Monde 2025 — Interface d'administration</p>
    </div>
</footer>

</body>
</html>
