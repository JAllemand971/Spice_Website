<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   Page administrateur pour la gestion des membres
   Cette page liste tous les membres et permet d'activer/désactiver leur compte.
   ============================================================================ */

session_start();
// Si l'utilisateur n'est pas admin, on redirige vers la connexion
if (!isset($_SESSION['role']) || strtoupper($_SESSION['role']) !== 'A') {
    header("Location: /TP3/client/public/pages/connexion.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administration - Membres</title>

    <!-- Favicon -->
    <link rel="icon" href="/TP3/client/public/images/favicon.ico" />

    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Bootstrap CSS local -->
    <link href="/TP3/client/public/utilitaires/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Styles personnalisés -->
    <link href="/TP3/client/public/css/styles.css" rel="stylesheet" />

    <!-- jQuery (si nécessaire) -->
    <script src="/TP3/client/public/utilitaires/jquery-3.7.1.min.js"></script>
    <!-- Toast pour les messages utilisateur -->
    <script src="/TP3/client/public/js/toast.js"></script>
    <!-- Configuration (BASE_URL, etc.) -->
    <script src="/TP3/client/public/js/config.js"></script>
    <!-- Requêtes AJAX pour membres (getMembres, changerStatutMembre) -->
    <script src="/TP3/client/Admin/RequetesAdmin.js"></script>
    <!-- Vues : afficher la table des membres -->
    <script src="/TP3/client/Admin/VuesAdmin.js"></script>
    <!-- Scripts pour gérer la connexion -->
    <script src="/TP3/client/Connexion/RequetesConnexion.js"></script>
</head>
<body>

<!-- NAVBAR ADMIN -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="/TP3/client/public/pages/admin.php">Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/TP3/serveur/Admin/admin.php">Retour</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="logout()">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENU : Gestion des membres -->
<div class="container mt-5">
    <h2 class="mb-4">Gestion des membres</h2>
    <!-- Ici sera injecté le tableau des membres -->
    <div id="contenuMembres" class="table-responsive mt-4"></div>
</div>

<!-- TOAST pour afficher les messages utilisateur -->
<div class="toast posToast" id="toastMessage" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header">
        <img src="/TP3/client/public/images/iconToast.png" class="rounded me-2" style="width: 20px; height: 20px;" alt="Toast icon">
        <strong class="me-auto">Message</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="textToast"></div>
</div>

<!-- Bootstrap JS (modal, navbar responsive) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Initialisation : chargement de la liste des membres -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Appel AJAX pour récupérer tous les membres et afficher
    listerMembres(afficherMembres);
});
</script>
</body>
</html>
