<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   ConnexionControleur.php : gère la connexion et la déconnexion des utilisateurs
   ============================================================================ */

// On inclut la connexion à la base de données et le modèle de connexion
require_once(__DIR__ . '/../../includes/bd/ConnexionSingleton.php');
require_once(__DIR__ . '/../modeles/ConnexionModele.php');

class ConnexionControleur {
    
    private $dao;
    public function __construct() {
        $this->dao = new ConnexionModele();
        $this->connexion = ConnexionSingleton::getInstance();
    }

    /**
     * login()
     * Méthode appelée pour vérifier les identifiants et démarrer la session
     * Renvoie du JSON avec 'redirection' ou 'erreur'
     */
    public function login() {
        // On force le format JSON pour la réponse
        header('Content-Type: application/json; charset=utf-8');

        // 1) On lit les données brutes JSON envoyées en POST
        $data = json_decode(file_get_contents('php://input'), true);

        // 2) Validation : on vérifie la présence des champs courriel et motdepasse
        if (!isset($data['courriel'], $data['motdepasse'])) {
            echo json_encode(['erreur' => 'Données manquantes']);
            return;
        }

        // 3) On appelle le modèle pour vérifier les identifiants en base
        $resultat = $this->dao->verifierConnexion(
            $data['courriel'],
            $data['motdepasse']
        );

        // 4) Vérifie si que l'email existe
        if (!$resultat) {
            echo json_encode(['erreur' => 'Courriel ou mot de passe invalide']);
            return;
        }

        // 5) Vérifie si le membre est actif
        if ($resultat['statut'] !== 'A') {
            echo json_encode(['erreur' => 'Utilisateur inactif. Contactez l’administrateur.']);
            return;
        }

        // 6) Si le modèle retourne un utilisateur, on démarre la session
        if ($resultat) {
            session_start();
            $_SESSION['role']   = $resultat['role'];
            $_SESSION['id']     = $resultat['idc'];
            $_SESSION['nom']    = $resultat['nom'];
            $_SESSION['prenom'] = $resultat['prenom'];
            $_SESSION['photo']  = $resultat['photo'];
            $_SESSION['courriel'] = $data['courriel'];

            // On choisit la page de redirection selon le rôle
            $redirection = ($resultat['role'] === 'A')
                ? '/TP3/serveur/Admin/admin.php'
                : '/TP3/serveur/Membre/membre.php';

            // On renvoie la réponse JSON avec l'URL de redirection
            echo json_encode(['redirection' => $redirection]);
        } else {
            // 7) Sinon, identifiants invalides : on renvoie une erreur JSON
            echo json_encode(['erreur' => 'Courriel ou mot de passe invalide']);
        }
    }

    /**
     * deconnexion()
     * Méthode pour détruire la session et déconnecter l'utilisateur
     * Renvoie du JSON avec un message de succès
     */
    public function logout() {
        // On force le format JSON pour la réponse
        header('Content-Type: application/json; charset=utf-8');

        // 1) Démarrage de la session (pour l'effacer)
        session_start();
        session_unset();   // On supprime toutes les variables de session
        session_destroy(); // On détruit la session

        // 2) On renvoie un message de succès au client
        echo json_encode(['message' => 'Déconnexion réussie']);
    }
}
?>
