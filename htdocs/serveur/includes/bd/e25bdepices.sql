--
-- Base de données : `e25bdepices`
--
CREATE DATABASE IF NOT EXISTS `e25bdepices` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `e25bdepices`;

-- --------------------------------------------------------
--
-- Structure de la table `membres`
--

CREATE TABLE `membres` (
  `idm` int(11) AUTO_INCREMENT,
  `prenom` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sexe` varchar(2) DEFAULT NULL,
  `daten` date DEFAULT NULL,
  CONSTRAINT membres_idm_PK PRIMARY KEY(idm)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membres`
--

INSERT INTO `membres` (`idm`, `prenom`, `nom`, `sexe`, `daten`) VALUES
(1, 'admin', 'admin', 'F', '1985-05-10');

--
-- Structure de la table `connexion`
--

CREATE TABLE `connexion` (
  `idc` int(11) NOT NULL,
  `courriel` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` char(1) NOT NULL DEFAULT '',
  `statut` char(1) NOT NULL,
  CONSTRAINT connexion_idc_FK FOREIGN KEY(idc) REFERENCES membres(idm)ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `connexion`
--

INSERT INTO `connexion` (`idc`, `courriel`, `pass`, `role`, `statut`) VALUES
(1, 'admin@epices.com', '12345', 'A', 'A');

CREATE TABLE `epices` (
  `ide` int(11) PRIMARY KEY AUTO_INCREMENT,
  `nom` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` float NOT NULL,
  `vendeur` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `epices`
--

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
