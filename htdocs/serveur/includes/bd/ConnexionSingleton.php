<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   ConnexionSingleton.php : gestion centralisée de la connexion PDO à la base
   Ce fichier assure qu'il n'y a qu'une seule et unique instance de PDO
   et effectue quelques vérifications de colonnes manquantes.
   ============================================================================ */

// On inclut le fichier de configuration contenant les constantes DB_HOST, DB_NAME, etc.
require_once __DIR__ . "/../env/config.php";

class ConnexionSingleton {
    // Propriété statique pour stocker l'instance unique de PDO
    private static $instance = null;

    /**
     * getInstance()
     * Retourne l'objet PDO unique, ou le crée s'il n'existe pas encore.
     * @return PDO L'instance PDO configurée pour la base.
     */
    public static function getInstance() {
        // Si aucune instance n'existe, on en crée une
        if (self::$instance === null) {
            try {
                // 1) Construction de la DSN (Data Source Name)
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=utf8',
                    DB_HOST,
                    DB_NAME
                );

                // 2) Création de l'instance PDO avec user et mot de passe
                self::$instance = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS
                );

                // 3) Mode d'erreur : on veut des exceptions en cas de problème SQL
                self::$instance->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                // 4) Vérifications optionnelles
                //    a) Si la colonne 'note' manque dans la table epices, on la crée
                $result = self::$instance->query(
                    "SHOW COLUMNS FROM epices LIKE 'note'"
                );
                if ($result->rowCount() === 0) {
                    require_once __DIR__ . "/../../includes/utilitaires/colonneNote.php";
                }

                //    b) Si la colonne 'photo' manque dans la table membres, on la crée
                $result = self::$instance->query(
                    "SHOW COLUMNS FROM membres LIKE 'photo'"
                );
                if ($result->rowCount() === 0) {
                    require_once __DIR__ . "/../../includes/utilitaires/colonnePhoto.php";
                }

                //    c) Si la table 'achats' est absente, on la crée
                $result = self::$instance->query("SHOW TABLES LIKE 'achats'");
                if ($result->rowCount() === 0) {
                    require_once __DIR__ . "/../../includes/utilitaires/tableAchat.php";
                    creerTableAchats(self::$instance);
                }

            } catch (PDOException $e) {
                // En cas d'erreur de connexion ou de requête, on arrête tout
                die("Erreur de connexion à la base : " . $e->getMessage());
            }
        }

        // On retourne l'instance existante ou nouvellement créée
        return self::$instance;
    }
}
?>
