<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   colonnePhoto.php : ajoute la colonne 'photo' à la table 'membres' si elle n'existe pas
   Ce script s'exécute automatiquement via le singleton de connexion.
   Il initialise ensuite chaque membre avec une photo par défaut.
   ============================================================================ */

try {
    // On récupère l'instance PDO
    $connexion = ConnexionSingleton::getInstance();

    // 1) Vérifie l'existence de la colonne 'photo' dans la table 'membres'
    $result = $connexion->query("SHOW COLUMNS FROM membres LIKE 'photo'");

    // 2) Si rowCount() == 0, la colonne n'existe pas et doit être ajoutée
    if ($result->rowCount() == 0) {
        // a) On ajoute la colonne 'photo' au format VARCHAR(255) nullable
        $connexion->exec("ALTER TABLE membres ADD photo VARCHAR(255) DEFAULT NULL");

        // b) On récupère tous les IDs des membres pour initialiser une photo par défaut
        $stmt = $connexion->query("SELECT idm FROM membres");
        $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // c) Pour chaque membre, on génère un nom de fichier (ex: user5.jpg) et on met à jour la colonne
        foreach ($membres as $membre) {
            // Construction du nom de la photo basé sur l'ID du membre
            $photo = "user" . $membre['idm'] . ".jpg";
            // Prépare et exécute la mise à jour
            $update = $connexion->prepare("UPDATE membres SET photo = ? WHERE idm = ?");
            $update->execute([$photo, $membre['idm']]);
        }
    }
} catch (PDOException $e) {
    // En cas d'erreur SQL, on peut logger l'erreur pour debug
    error_log('Erreur colonnePhoto: ' . $e->getMessage());
}
?>
