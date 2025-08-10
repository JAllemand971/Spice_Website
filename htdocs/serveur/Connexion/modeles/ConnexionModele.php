<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   ConnexionModele.php : gère la vérification des identifiants utilisateurs
   ============================================================================ */

require_once(__DIR__ . '/../../includes/bd/ConnexionSingleton.php');

class ConnexionModele {

    private PDO $connexion;

    public function __construct()
    {
        $this->connexion = ConnexionSingleton::getInstance();
    }

    /**
     * verifierConnexion($courriel, $motdepasse)
     * Cherche en base l'utilisateur correspondant aux identifiants.
     * @param string $courriel   Adresse email saisie
     * @param string $motdepasse Mot de passe saisi
     * @return array|false       Tableau associatif (idc, role, nom, prenom) ou false si non trouvé
     */
    public function verifierConnexion($courriel, $motdepasse): mixed {
        try{

        // 1) Requête SQL : on joint la table connexion et membres pour obtenir nom+prénom
        $sql = "SELECT c.idc, c.role, c.statut, m.nom, m.prenom, m.photo
                FROM connexion c
                JOIN membres m ON c.idc = m.idm
                WHERE c.courriel = ? AND c.pass = ?";

        // 2) On prépare la requête pour éviter les injections SQL
        $stmt = $this->connexion->prepare($sql);
        // 3) On exécute avec les valeurs fournies
        $stmt->execute([$courriel, $motdepasse]);

        // 4) fetch() renvoie le premier résultat ou false si aucun
        return $stmt->fetch(PDO::FETCH_ASSOC); // clés associatives seulement 
       } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de la connexion : " . $e->getMessage());
       }
    }
}
?>
