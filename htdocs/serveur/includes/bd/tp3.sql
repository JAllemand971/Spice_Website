/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   TP3.sql : Script complet de la base e25bdepices incluant toutes les
             tables et colonnes nécessaires, incluant les valeurs d'exemple.
   ============================================================================ */

DROP DATABASE IF EXISTS `e25bdepices`;
CREATE DATABASE `e25bdepices` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `e25bdepices`;

-- Table : membres
CREATE TABLE `membres` (
  `idm` INT(11) AUTO_INCREMENT,
  `prenom` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nom` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sexe` VARCHAR(2) DEFAULT NULL,
  `daten` DATE DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  CONSTRAINT `membres_idm_PK` PRIMARY KEY(`idm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Données exemple : membres
INSERT INTO `membres` (`idm`, `prenom`, `nom`, `sexe`, `daten`, `photo`) VALUES
(1, 'admin', 'admin', 'F', '1985-05-10', 'default_femme.png');

-- Table : connexion
CREATE TABLE `connexion` (
  `idc` INT(11) NOT NULL,
  `courriel` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pass` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` CHAR(1) NOT NULL DEFAULT '',
  `statut` CHAR(1) NOT NULL,
  CONSTRAINT `connexion_idc_FK` FOREIGN KEY(`idc`) REFERENCES `membres`(`idm`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Données exemple : connexion
INSERT INTO `connexion` (`idc`, `courriel`, `pass`, `role`, `statut`) VALUES
(1, 'admin@epices.com', '12345', 'A', 'A');

-- Table : epices
CREATE TABLE `epices` (
  `ide` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` FLOAT NOT NULL,
  `vendeur` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` DECIMAL(3,2),
  CHECK (
    `note` IN (1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Données exemple : epices
INSERT INTO `epices` (`nom`, `type`, `prix`, `vendeur`, `image`, `description`) VALUES
( 'Gingembre moulu', 'Épice', 3.1, 'Épices du Monde', 'gingembre.webp', 'Épice chaude et piquante, utilisée dans les pâtisseries, les marinades et les plats asiatiques.'),
( 'Fenugrec moulu', 'Épice', 2.75, 'La Maison des Arômes', 'fenugrec.webp', 'Épice amère et noisettée, utilisée dans les currys, les mélanges d\'épices et pour faire du pain.'),
( 'Piment de Cayenne', 'Épice', 3.5, 'Les Saveurs Exotiques', 'cayenne.webp', 'Épice très piquante, utilisée pour relever les plats, les sauces et les marinades.'),
( 'Clous de girofle moulus', 'Épice', 4.4, 'Au Poivre Sauvage', 'clous_de_girofle.webp', 'Épice piquante et chaude, utilisée dans les plats mijotés, les desserts et pour l\'assaisonnement des viandes.'),
( 'Graines de fenouil', 'Épice', 3, 'Épices Traditionnelles', 'fenouil.webp', 'Épice anisée et douce, utilisée dans les plats de poisson, les sauces et les saucisses.'),
( 'Graines de moutarde', 'Épice', 2.9, 'Épices du Monde', 'moutarde.webp', 'Épice piquante et rustique, utilisée dans la préparation de condiments et pour assaisonner les plats.'),
( 'Piment doux', 'Épice', 3.25, 'La Maison des Arômes', 'piment_doux.webp', 'Épice légèrement piquante et sucrée, utilisée dans les plats mijotés et les sauces.'),
( 'Anis étoilé', 'Épice', 5.5, 'Les Saveurs Exotiques', 'anis_etoile.webp', 'Épice douce avec un goût rappelant la réglisse, utilisée dans les plats sucrés et salés, et pour aromatiser les boissons.'),
( 'Grains de paradis', 'Épice', 6.2, 'Au Poivre Sauvage', 'grains_de_paradis.webp', 'Épice africaine au goût poivré et légèrement citronné, utilisée dans les plats mijotés et les marinades.'),
( 'Sumac', 'Épice', 3.8, 'Épices Traditionnelles', 'sumac.webp', 'Épice acide et fruitée, utilisée dans la cuisine moyen-orientale, pour assaisonner les salades, les viandes et les poissons.'),
( 'Ras el hanout', 'Épice', 4.5, 'Épices du Monde', 'ras_el_hanout.webp', 'Mélange d\'épices nord-africain, utilisé dans les tajines, les soupes et les plats de riz.'),
( 'Herbes de Provence', 'Mélange d\'épices', 3.35, 'La Maison des Arômes', 'herbes_de_provence.webp', 'Mélange d\'herbes aromatiques typique de la cuisine française, utilisé pour les grillades, les ragoûts et les légumes.'),
( 'Poudre de chili', 'Mélange d\'épices', 3.6, 'Les Saveurs Exotiques', 'poudre_de_chili.webp', 'Mélange d\'épices piquant, utilisé dans la préparation du chili con carne et d\'autres plats tex-mex.'),
( 'Curry en poudre', 'Mélange d\'épices', 3.95, 'Au Poivre Sauvage', 'curry.webp', 'Mélange d\'épices indien, utilisé dans les currys, les soupes et comme assaisonnement.'),
( 'Cinq-épices chinois', 'Mélange d\'épices', 4.1, 'Épices Traditionnelles', 'cinq_epices.webp', 'Mélange d\'épices utilisé dans la cuisine chinoise, idéal pour mariner les viandes et parfumer les plats mijotés.'),
( 'Garam masala', 'Mélange d\'épices', 4.25, 'Épices du Monde', 'garam_masala.webp', 'Mélange d\'épices chauffant, utilisé dans les plats indiens pour ajouter de la profondeur et de la chaleur.'),
( 'Thym séché', 'Herbe', 2.65, 'La Maison des Arômes', 'thym.webp', 'Herbe aromatique, utilisée dans les ragoûts, les bouillons et pour assaisonner les légumes.'),
( 'Romarin séché', 'Herbe', 2.75, 'Les Saveurs Exotiques', 'romarin.webp', 'Herbe boisée et parfumée, utilisée dans les plats de viande, les soupes et les pommes de terre.'),
( 'Basilic séché', 'Herbe', 2.85, 'Au Poivre Sauvage', 'basilic.webp', 'Herbe douce et légèrement poivrée, utilisée dans les sauces, les salades et les plats de pâtes.'),
( 'Origan séché', 'Herbe', 2.5, 'Épices Traditionnelles', 'origan.webp', 'Herbe aromatique avec un goût piquant, utilisée dans la cuisine italienne, grecque et mexicaine.');

-- Assigner une note aléatoire entre 1 et 5 à chaque épice
UPDATE epices
SET note = (FLOOR(RAND() * 9) * 0.5 + 1.0);

-- Table : achats
CREATE TABLE achats (
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

-- Fin du script
