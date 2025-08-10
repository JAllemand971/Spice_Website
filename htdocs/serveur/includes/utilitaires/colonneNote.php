<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   colonneNote.php : ajoute la colonne 'note' à la table 'epices' si elle n'existe pas
   Ce script s'exécute automatiquement via le singleton de connexion.
   ============================================================================ */

try {
    // On récupère l'objet PDO unique
    $connexion = ConnexionSingleton::getInstance();

    // 1) On vérifie si la colonne 'note' existe déjà dans la table 'epices'
    $result = $connexion->query("SHOW COLUMNS FROM epices LIKE 'note'");

    // 2) Si rowCount() == 0, la colonne n'existe pas
    if ($result->rowCount() == 0) {
        // 3) On ajoute la colonne 'note' avec un type DECIMAL et valeur par défaut 0
        $connexion->exec("ALTER TABLE epices ADD note DECIMAL(3,2) DEFAULT 0");

        // 4) Optionnel : initialiser des notes aléatoires entre 1.0 et 5.0
        //    a) On récupère tous les IDs des épices
        $stmt = $connexion->query("SELECT ide FROM epices");
        $epices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //    b) Pour chaque épice, on génère une note aléatoire et on met à jour
        foreach ($epices as $epice) {
            // mt_rand(10,50) génère un entier entre 10 et 50 => on divise par 10 pour avoir 1.0 à 5.0
            $note = mt_rand(10, 50) / 10;
            $update = $connexion->prepare("UPDATE epices SET note = ? WHERE ide = ?");
            $update->execute([$note, $epice['ide']]);
        }
    }
} catch (PDOException $e) {
    // En cas d'erreur SQL ou autre, on peut logger ou afficher un message simple
    error_log('Erreur colonneNote: ' . $e->getMessage());
}
?>
