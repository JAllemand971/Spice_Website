<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   Fichier routes.php : point d'entrée pour toutes les requêtes AJAX JSON
   Ce fichier détermine quel contrôleur et quelle action appeler
   ============================================================================ */

   
// On inclut les contrôleurs nécessaires et la connexion à la base
require_once __DIR__ . "/serveur/Admin/controleurs/AdminControleur.php";
require_once __DIR__ . "/serveur/Membre/controleurs/MembreControleur.php";
require_once __DIR__ . "/serveur/Connexion/controleurs/ConnexionControleur.php";
require_once __DIR__ . "/serveur/includes/bd/ConnexionSingleton.php";

// On force le format des réponses en JSON
header('Content-Type: application/json; charset=utf-8');

// 1) On récupère l'URL demandée (tout ce qui suit le nom du script)
$scriptName = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$uriParts = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// On enlève la partie scriptName pour ne garder que "ressource/action"
$relativePath = substr($uriParts, strlen($scriptName));
$relativePath = trim($relativePath, '/');

// 2) On sépare "ressource" et "action"
$segments = explode('/', $relativePath);
$resource = $segments[0] ?? '';
$action   = $segments[1] ?? ''; // action peut être vide

// 3) En fonction de la ressource, on appelle le contrôleur correspondant
switch ($resource) {
    case 'epices':
        // Toutes les opérations liées aux épices
        $ctrl = new AdminControleur();
        if ($action === 'listerVisiteur') {
            $ctrl->listerEpicesVisiteur();
        } elseif ($action === 'lister') {
            $ctrl->listerEpices();
        } elseif ($action === 'ajouter') {
            $ctrl->ajouterEpice();
        } elseif ($action === 'modifier') {
            $ctrl->modifierEpice();
        } elseif ($action === 'supprimer') {
            $ctrl->supprimerEpice();
        } else {
            erreur404();
        }
        break;

    case 'membres':
        // Opérations d'administration des membres
        if ($action === 'ajouter') {
            (new MembreControleur())->ajouterMembre(); // Inscription d'un nouveau membre
        } elseif ($action === 'listerMembre') {
            (new AdminControleur())->listerMembres();
        } elseif ($action === 'activer') {
            (new AdminControleur())->changerStatutMembre('A');
        } elseif ($action === 'desactiver') {
            (new AdminControleur())->changerStatutMembre('I');
        } else {
            erreur404();
        }
        break;

    case 'membre':
        // Opérations de profil pour le membre connecté
        $ctrl = new MembreControleur();
        if ($action === 'profil') {
            $ctrl->getProfil();
        } elseif ($action === 'modifierProfil') {
            $ctrl->modifierProfil();
        } elseif ($action === 'payer') {
            $ctrl->payer();
        } else {
            erreur404();
        }
        break;

    case 'connexion':
        // Connexion et déconnexion
        $ctrl = new ConnexionControleur();
        if ($action === 'login') {
            $ctrl->login();
        } elseif ($action === 'logout') {
            $ctrl->logout();
        } else {
            erreur404();
        }
        break;

    default:
        // Si la ressource n'existe pas, on renvoie une 404
        erreur404();
}

/**
 * Envoie une réponse 404 en JSON si la route n'est pas trouvée
 */
function erreur404() {
    http_response_code(404);
    echo json_encode(['erreur' => 'Route non trouvée']);
    exit;
}
?>
