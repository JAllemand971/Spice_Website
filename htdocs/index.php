<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   Fichier principal SPA (Single Page Application)
   ============================================================================ */

session_start();
$msg = isset($_GET['message']) ? urldecode($_GET['message']) : '';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Épices du Monde - SPA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" href="client/public/images/favicon.ico">

    <!-- Bootstrap CSS complet -->
    <link href="client/public/utilitaires/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ton CSS perso -->
    <link rel="stylesheet" href="client/public/css/styles.css">

    <!-- JQuery -->
    <script src="client/public/utilitaires/jquery-3.7.1.min.js"></script>

    <!-- Scripts généraux -->
    <script src="client/public/js/toast.js"></script>

    <!-- JS Requêtes + Vues -->
    <script src="/TP3/client/Admin/RequetesAdmin.js"></script>
    <script src="/TP3/client/Admin/VuesAdmin.js"></script>
    <script src="/TP3/client/Epices/RequetesEpices.js"></script>
    <script src="/TP3/client/Epices/VuesEpices.js"></script>
    <script src="/TP3/client/Connexion/RequetesConnexion.js"></script>
    <script src="/TP3/client/Membre/RequetesMembres.js"></script>
    <script src="/TP3/client/Membre/VuesMembres.js"></script>
    <script src="/TP3/client/public/js/config.js"></script>
</head>

<body onload="getEpicesVisiteur(afficherEpices); montrerToast('<?= $msg ?>')">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="#" onclick="getEpices(afficherEpices)">Épices du Monde</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link active" href="#" onclick="getEpices(afficherEpices)">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/TP3/client/public/pages/inscription.html">Devenir membre</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="/TP3/client/public/pages/connexion.html"
                class="btn btn-outline-dark btn-sm d-flex align-items-center"
                data-bs-toggle="tooltip" title="Connexion">
                    <i class="bi bi-person-circle me-1 fs-6"></i>
                    Connexion
                </a>

                <a href="/TP3/client/public/pages/connexion.html" class="btn btn-outline-dark btn-sm position-relative d-flex align-items-center"
                data-bs-toggle="tooltip" title="Panier">
                    <i class="bi bi-cart-fill me-1 fs-6"></i>
                    Panier
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark text-white">
                        0
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- HEADER -->
<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Épices du Monde</h1>
            <p class="lead fw-normal text-white-50 mb-0">Découvrez notre sélection raffinée</p>
        </div>
    </div>
</header>

<!-- Contenu dynamique -->
<main id="contenu">
    <!-- Le JS viendra injecter le contenu ici -->
</main>

<!-- TOAST -->
<div class="toast posToast" id="toastMessage" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header">
        <img src="client/public/images/iconToast.png" class="rounded me-2" alt="Toast icon" style="width: 20px; height: 20px;">
        <strong class="me-auto">Message</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="textToast"></div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Catalogue d'Epices -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        getEpicesVisiteur(afficherEpices);
        montrerToast('<?= addslashes($msg) ?>');
    });
</script>

</body>
</html>
