<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   MembreModele.php : DAO - gère les opérations SQL sur membres + connexion
   ============================================================================ */

require_once __DIR__ . '/../../includes/bd/ConnexionSingleton.php';

class MembreModele
{
    private PDO $connexion;

    public function __construct()
    {
        $this->connexion = ConnexionSingleton::getInstance();
    }

    /**
     * courrielExiste()
     * Verifie que le courriel n'existe pas
     * avant l'ajout du nouveau membre
     */
    public function courrielExiste($courriel): bool
    {
        try {
            $stmt = $this->connexion->prepare("SELECT 1 FROM connexion WHERE courriel = ?"); // 1 pour alleger la requete
            $stmt->execute([$courriel]);
            return $stmt->fetch() !== false; // retourne faux si rien trouve
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la vérification de l'email : " . $e->getMessage());
        }
    }

    /**
     * insererMembre()
     * Inserer le membre dans la table membre
     */
    public function insererMembre(array $donnees): string
    {
        try {
            $sql = "INSERT INTO membres (nom, prenom, sexe, daten, photo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute([
                $donnees['nom'],
                $donnees['prenom'],
                $donnees['sexe'],
                $donnees['datenaissance'],
                $donnees['photo']
            ]);
            return $this->connexion->lastInsertId(); // attention, retourne un string et non un int
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de l'insertion dans la table membre : " . $e->getMessage());
        }
    }

    /**
     * insererConnexion()
     * Inserer le membre dans la table connexion
     */
    public function insererConnexion($idMembre, $courriel, $pass): void
    {
        try {
            $sql = "INSERT INTO connexion (idc, courriel, pass, statut, role) VALUES (?, ?, ?, 'A', 'M')";
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute([$idMembre, $courriel, $pass]);
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de l'insertion dans la table connexion : " . $e->getMessage());
        }
    }

    /**
     * modifierProfil()
     * Cette méthode met à jour les informations d’un membre dans la base de données,
     * en fonction de son courriel, et des champs à modifier (nom, prénom, photo, mot de passe).
     *
     * Fonctionnement :
     *  
     * 1. On commence par récupérer l’identifiant du membre (idm) via son courriel,
     *    en consultant la table connexion (champ idc).
     *    → SELECT idc FROM connexion WHERE courriel = ?
     *
     * 2. Ensuite, on construit dynamiquement une requête UPDATE membres SET...
     *    en fonction des clés présentes dans le tableau $modifs :
     *      - Si nom existe → ajoute "nom = ?" et sa valeur
     *      - Si prenom existe → ajoute "prenom = ?" et sa valeur
     *      - Si photo existe → ajoute "photo = ?" et sa valeur
     *
     * 3. On assemble les parties modifiables avec implode(", ", $colonnes)
     *    puis on ajoute la condition WHERE idm = ? à la fin de la requête.
     *    → Le tableau $valeurs[] contient les valeurs dans le bon ordre
     *
     * 4. Si un nouveau mot de passe est fourni dans $modifs,
     *    on exécute une seconde requête :
     *    → UPDATE connexion SET pass = ? WHERE idc = ?
     *
     * 5. Les deux requêtes sont préparées avec prepare() pour éviter toute injection SQL.
     *
     * 6. Si tout réussit, on retourne true. Sinon, une exception est levée.
     */
    public function modifierProfil($courriel, $modifs): bool
    {
        try {
            // Récupérer l'idm du membre à partir du courriel
            $stmt = $this->connexion->prepare("SELECT idc FROM connexion WHERE courriel = ?");
            $stmt->execute([$courriel]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // clés associatives seulement 

            if (!$row) {
                throw new Exception("Courriel introuvable.");
            }

            $idm = $row['idc'];

            // Mettre à jour les champs dans la table membres
            $colonnes = [];
            $valeurs = [];

            if (isset($modifs['nom'])) {
                $colonnes[] = "nom = ?";
                $valeurs[] = $modifs['nom'];
            }
            if (isset($modifs['prenom'])) {
                $colonnes[] = "prenom = ?";
                $valeurs[] = $modifs['prenom'];
            }

            if (isset($modifs['photo'])) {
                $colonnes[] = "photo = ?";
                $valeurs[] = $modifs['photo'];
            }

            if (!empty($colonnes)) {
                $sql = "UPDATE membres SET " . implode(separator: ", ", array: $colonnes) . " WHERE idm = ?";
                $valeurs[] = $idm;
                $stmt = $this->connexion->prepare($sql);
                $stmt->execute($valeurs);
            }

            // Mettre à jour le mot de passe dans la table connexion
            if (isset($modifs['motdepasse'])) {
                $stmt = $this->connexion->prepare("UPDATE connexion SET pass = ? WHERE idc = ?");
                $stmt->execute([$modifs['motdepasse'], $idm]);
            }

            return true;
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la modification du profil : " . $e->getMessage());
        }
    }

    /**
     * listerMembres()
     * Liste les membres pour la page AdminMembre.php
     * Retourne un tableau encodé en JSON
     */
    public function listerMembres(): array
    {
        try {
            $sql = "SELECT m.idm AS id, m.nom, m.prenom, c.courriel, c.statut AS statut
                FROM membres m
                JOIN connexion c ON m.idm = c.idc";

            $stmt = $this->connexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // clés associatives seulement 
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors du chargement de la liste membre : " . $e->getMessage());
        }
    }

    /**
    * changerStatut()
    * Change le statut des membres sur la page AdminMembre.php
    */
    public function changerStatut($id, $statut): void
    {
        try {
            $sql = "UPDATE connexion SET statut = ? WHERE idc = ?";
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute([$statut, $id]);
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors du chargement de status : " . $e->getMessage());
        }
    }

    /**
    * getProfil()
    * Charge les donnees du membres pour 
    * le formulaire profil d'AdminMembre.php
    */
    public function getProfil($courriel): mixed
    {
        try {
            $sql = "SELECT m.nom, m.prenom, m.photo
                    FROM membres m
                    JOIN connexion c ON m.idm = c.idc
                    WHERE c.courriel = ?";

            $stmt = $this->connexion->prepare($sql);
            $stmt->execute([$courriel]);

            $profil = $stmt->fetch(PDO::FETCH_ASSOC); // clés associatives seulement 

            if (!$profil) {
                throw new Exception("Profil introuvable pour ce courriel.");
            }
            return $profil;
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors du chargement du profil : " . $e->getMessage());
        }
    }

    /**
     * enregistrerAchats()
     * Cette méthode enregistre une ou plusieurs lignes d'achat dans la table achats,
     * en associant à chaque ligne les informations du membre (nom, prénom) et de l'épice (nom).
     *
     * Fonctionnement :
     *
     * 1. On récupère le nom et prénom du membre à partir de son identifiant (idm),
     *    depuis la table membres.
     *    → Cette information sera enregistrée en clair dans la table achats
     *
     * 2. On prépare deux requêtes SQL réutilisables :
     *    - Une pour récupérer le nom d’une épice à partir de son identifiant (ide)
     *    - Une autre pour insérer une ligne dans la table achats
     *
     * 3. On parcourt le tableau $listeAchats, qui contient des sous-tableaux :
     *    Exemple : [ ['ide' => 3, 'quantite' => 2], ['ide' => 5, 'quantite' => 1], ... ]
     *
     * 4. Pour chaque élément :
     *    - On vérifie que ide et quantite sont présents
     *    - On récupère le nom de l’épice correspondante
     *    - On insère une ligne dans la table achats, avec les champs suivants :
     *        - idm, ide
     *        - nom/prénom du membre
     *        - nom de l’épice
     *        - quantité
     *        - date du jour (CURDATE())
     *
     * 5. Si une erreur SQL se produit à n’importe quelle étape,
     *    une exception est levée avec un message explicite.
     */
    public function enregistrerAchats($idm, $listeAchats): void {
        try {
            // Récupérer nom/prenom du membre
            $stmtMembre = $this->connexion->prepare("SELECT nom, prenom FROM membres WHERE idm = ?");
            $stmtMembre->execute([$idm]);
            $membre = $stmtMembre->fetch(PDO::FETCH_ASSOC); // clés associatives seulement 

            if (!$membre || empty($membre['nom']) || empty($membre['prenom'])) {
                throw new \Exception("Nom ou prénom du membre introuvable.");
            }

            // Récupérer le nom de l'épice
            $stmtEpice = $this->connexion->prepare("SELECT nom FROM epices WHERE ide = :ide");

            // Requête d'insertion complète
            $sql = "INSERT INTO achats (idm, ide, nom_membre, prenom_membre, nom_epice, quantite, date_achat)
                    VALUES (:idm, :ide, :nom_membre, :prenom_membre, :nom_epice, :quantite, CURDATE())";
            $stmtInsert = $this->connexion->prepare($sql);

            foreach ($listeAchats as $achat) {
                if (empty($achat['ide']) || empty($achat['quantite'])) continue;

                $stmtEpice->execute([':ide' => $achat['ide']]);
                $epice = $stmtEpice->fetch(PDO::FETCH_ASSOC); // clés associatives seulement 

                if (!$epice || empty($epice['nom'])) {
                    throw new \Exception("Épice non trouvée ou nom manquant.");
                }

                $stmtInsert->execute([
                    ':idm'           => $idm,
                    ':ide'           => $achat['ide'],
                    ':nom_membre'    => $membre['nom'],
                    ':prenom_membre' => $membre['prenom'],
                    ':nom_epice'     => $epice['nom'],
                    ':quantite'      => $achat['quantite']
                ]);
            }

        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors du paiement : " . $e->getMessage());
        }
    }
}
?>