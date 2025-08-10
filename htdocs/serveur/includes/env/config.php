<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   config.php : configuration des paramètres de connexion à la base de données
   Ce fichier définit les constantes utilisées par ConnexionSingleton pour PDO.
   ============================================================================ */

// Adresse du serveur de base de données (ex: localhost pour un serveur local)
define('DB_HOST', 'localhost');

// Nom de la base de données à utiliser
// Remplace 'e25bdepices' par le nom exact de la base
define('DB_NAME', 'e25bdepices');

// Identifiant de l'utilisateur de la base (ex: root en local)
define('DB_USER', 'root');

// Mot de passe associé à l'utilisateur (vide par défaut en local)
define('DB_PASS', '');

// Exemple d'utilisation :
// $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
// $pdo = new PDO($dsn, DB_USER, DB_PASS);
?>
