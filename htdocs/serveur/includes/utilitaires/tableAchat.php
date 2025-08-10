<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   creerTableAchats.php : Crée la table achats avec les colonnes nécessaires
                          pour stocker les achats d'épices par membre
   ============================================================================ */

require_once(__DIR__ . "/../bd/ConnexionSingleton.php");

// Fonction de création de la table
function creerTableAchats(PDO $pdo) {
    $requete = "
        CREATE TABLE IF NOT EXISTS achats (
            ida             INT AUTO_INCREMENT PRIMARY KEY,
            idm             INT NOT NULL,
            ide             INT NOT NULL,
            nom_membre      VARCHAR(50) NOT NULL,
            prenom_membre   VARCHAR(50) NOT NULL,
            nom_epice       VARCHAR(120) NOT NULL,
            quantite        INT NOT NULL CHECK (quantite > 0),
            date_achat      DATE NOT NULL,
            FOREIGN KEY (idm) REFERENCES membres(idm),
            FOREIGN KEY (ide) REFERENCES epices(ide)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";

    $pdo->exec($requete);
}

try {
    $pdo = ConnexionSingleton::getInstance();
    creerTableAchats($pdo);
} catch (Exception $e) {
    error_log('Erreur tableAchat: ' . $e->getMessage());
}
