<?php

/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   Page administrateur pour la gestion des épices
   ============================================================================ */

session_start();

// Si l'utilisateur n'est pas connecté en tant qu'admin, on redirige vers la page de connexion
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
    <title>Administration - Épices</title>

    <!-- Favicon -->
    <link rel="icon" href="/TP3/client/public/images/favicon.ico" />

    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Bootstrap CSS local -->
    <link href="/TP3/client/public/utilitaires/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Styles personnalisés -->
    <link href="/TP3/client/public/css/styles.css" rel="stylesheet" />

    <!-- jQuery (utile pour certains plugins ou scripts) -->
    <script src="/TP3/client/public/utilitaires/jquery-3.7.1.min.js"></script>
    <!-- Script pour afficher des toasts (messages pop-up) -->
    <script src="/TP3/client/public/js/toast.js"></script>
    <!-- Configuration (contient par exemple l'URL de base BASE_URL) -->
    <script src="/TP3/client/public/js/config.js"></script>
    <!-- Fonctions d'appel AJAX vers le serveur -->
    <script src="/TP3/client/Admin/RequetesAdmin.js"></script>
    <!-- Fonctions pour mettre à jour la vue (tableau, modal, etc.) -->
    <script src="/TP3/client/Admin/VuesAdmin.js"></script>
    <!-- Scripts pour gérer la connexion -->
    <script src="/TP3/client/Connexion/RequetesConnexion.js"></script>
</head>
<body>

<!-- NAVBAR : barre de navigation en haut -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <!-- Lien vers la page admin principale -->
        <a class="navbar-brand" href="/TP3/client/public/pages/admin.php">Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <!-- Bouton Retour -->
                <li class="nav-item">
                    <a class="nav-link" href="/TP3/serveur/Admin/admin.php">Retour</a>
                </li>
                <!-- Bouton Déconnexion -->
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="logout()">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENU PRINCIPAL -->
<div class="container mt-5">
    <!-- Titre de la page -->
    <h2 class="mb-4">Gestion des épices</h2>
    <!-- Bouton pour ouvrir la modal d'ajout -->
    <button id="btnAjouterEpice" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAjout">
        Ajouter une épice
    </button>
    <!-- FILTRE : catégorie + recherche par nom -->
    <div class="row mb-3">
    <div class="col-md-4">
        <select id="filterType" class="form-select">
        <option value="">Tous les types</option>
        <option value="Épice">Épice</option>
        <option value="Herbe">Herbe</option>
        <!-- …autres types selon votre table… -->
        </select>
    </div>
    <div class="col-md-4 input-group">
        <input type="text"
            id="searchNom"
            class="form-control"
            placeholder="Recherche par nom" />
        <button id="btnSearch" class="btn btn-outline-secondary">Rechercher</button>
    </div>
    </div>
    <div id="contenuEpices" class="table-responsive mt-4"></div>
</div>

<!-- MODAL pour ajouter ou modifier une épice -->
<div class="modal fade" id="modalAjout" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Titre dynamique (Ajouter / Modifier) -->
                <h5 class="modal-title" id="modalLabel">Ajouter / Modifier une épice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEpice" enctype="multipart/form-data" method="POST">
                    <!-- Champ caché pour l'ID (utile en mode modification) -->
                    <input type="hidden" id="ide" name="ide">

                    <!-- Champ Nom -->
                    <div class="mb-3">
                        <label>Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>

                    <!-- Champ Type -->
                    <div class="mb-3">
                        <label>Type</label>
                        <input type="text" class="form-control" id="type" name="type" required>
                    </div>

                    <!-- Champ Prix -->
                    <div class="mb-3">
                        <label>Prix</label>
                        <input type="number" step="0.01" class="form-control" id="prix" name="prix" required>
                    </div>

                    <!-- Champ Vendeur -->
                    <div class="mb-3">
                        <label>Vendeur</label>
                        <input type="text" class="form-control" id="vendeur" name="vendeur" required>
                    </div>

                    <!-- Champ Description -->
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>

                    <!-- Champ Note (0 à 5) -->
                    <div class="mb-3">
                        <label>Note</label>
                        <input type="number" min="0" max="5" step="0.5" class="form-control" id="note" name="note">
                    </div>

                    <!-- Champ caché pour mémoriser le nom de l’image actuelle -->
                    <input type="hidden"
                        id="oldImage"
                        name="oldImage"
                        value="">
                    <div class="mb-3"></div>

                    <!-- Champ Image (upload) -->
                    <div class="mb-3">
                        <label>Image (upload)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <!-- Div pour afficher l'erreur -->
                        <div id="erreurImage" class="form-text text-danger"></div>
                    </div>


                    <!-- Bouton d'envoi du formulaire -->
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TOAST pour afficher les messages utilisateur -->
<div class="toast posToast" id="toastMessage" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header">
        <!-- Icône dans le toast -->
        <img src="/TP3/client/public/images/iconToast.png" class="rounded me-2" style="width: 20px; height: 20px;">
        <strong class="me-auto">Message</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="textToast"></div>
</div>

<!-- Inclusion de Bootstrap JavaScript pour le modal et la navbar responsive -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
