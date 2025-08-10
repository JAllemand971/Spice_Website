<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   EpiceModele.php : modèle (DAO) pour la gestion CRUD des épices en base
   Ce fichier contient les méthodes pour lister, ajouter, modifier et supprimer des épices.
   ============================================================================ */

// Inclusion du singleton pour la connexion PDO
require_once __DIR__ . "/../../includes/bd/ConnexionSingleton.php";

class EpiceModele
{

    private PDO $connexion;

    public function __construct()
    {
        $this->connexion = ConnexionSingleton::getInstance();
    }

    /**
     * listerEpicesVisiteur()
     * Récupère les 8 épices les mieux notées pour les visiteurs.
     * Retourne du JSON (array) ou une erreur HTTP 500.
     */
    public function listerEpicesVisiteur(): void
    {
        // On définit le header pour renvoyer du JSON en UTF-8
        header('Content-Type: application/json; charset=utf-8');

        try {
            // 1) On exécute directement la requête (query) pour récupérer 8 épices
            $sql = "SELECT * FROM epices ORDER BY note DESC LIMIT 8";
            $stmt = $this->connexion->query($sql);
            // 2) On récupère tous les résultats sous forme de tableau associatif
            $epices = $stmt->fetchAll(PDO::FETCH_ASSOC); // cles associatives seulement
            // 3) On encode le tableau en JSON et on l'affiche
            echo json_encode($epices);
        } catch (PDOException $e) {
            // En cas d'erreur SQL, on renvoie un code 500 et un message JSON
            http_response_code(500);
            echo json_encode([
                'erreur' => 'Erreur base de données : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * listerEpices()
     * Cette méthode retourne toutes les épices en JSON, avec des filtres optionnels
     * sur le type d'épice et le nom
     *
     * Fonctionnement :
     *
     * 1. On commence avec une requête SQL de base : "SELECT * FROM epices".
     * 2. Si l'utilisateur fournit un filtre dans l'URL comme :
     *      - ?type=Herbe     → on ajoute "type = :type"
     *      - ?search=poiv    → on ajoute "nom LIKE :search"
     * 3. Toutes les conditions sont stockées dans un tableau $conds[]
     *    et assemblées ensuite dans le SQL avec "AND".
     *    Ex : WHERE type = :type AND nom LIKE :search
     * 4. Les vraies valeurs (ex: 'Herbe', '%poiv%') sont stockées dans $params
     *    et liées aux emplacements :type, :search via execute($params)
     * 5. On utilise prepare() pour éviter les injections SQL, et fetchAll()
     *    pour récupérer toutes les lignes sous forme de tableau associatif.
     * 6. Le tableau final est converti en JSON pour être utilisé dans du JavaScript (fetch).
     *
     */

    public function listerEpices(): void
    {
        // On indique que la réponse sera en format JSON
        header('Content-Type: application/json; charset=utf-8');

        // Début de la requête SQL pour sélectionner toutes les épices
        $sql = "SELECT * FROM epices";

        // Initialisation des conditions dynamiques et des paramètres SQL
        $conds = [];    // contiendra les conditions WHERE (ex: "type = :type")
        $params = [];   // contiendra les valeurs associées aux conditions (ex: ":type" => "Herbe")

        // --- Filtrage par type (catégorie d'épice) ---
        if (!empty($_GET['type'])) {
            // Si un type est passé dans l'URL, on ajoute une condition SQL
            $conds[] = "type = :type";
            $params[':type'] = $_GET['type'];  // valeur réelle liée au placeholder :type
        }

        // --- Recherche par nom d'épice (partielle, via LIKE) ---
        if (!empty($_GET['search'])) {
            // Si un texte de recherche est passé, on ajoute une condition LIKE
            $conds[] = "nom LIKE :search";
            $params[':search'] = '%' . $_GET['search'] . '%';  // % permet la recherche partielle
        }

        // --- Ajout du WHERE si au moins une condition est présente ---
        if ($conds) {
            // On ajoute toutes les conditions reliées par "AND"
            $sql .= " WHERE " . implode(" AND ", $conds);
        }

        // --- Tri des résultats par note décroissante ---
        $sql .= " ORDER BY note DESC";

        // --- Exécution de la requête préparée ---
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute($params);

        // --- Envoi des résultats au format JSON ---
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)); // cles associatives seulement
    }


    /**
     * ajouter(array $donnees)
     * Insère une nouvelle épice en base.
     * @param array $donnees Clé/valeur : nom, type, prix, vendeur, image, description, note
     * @throws Exception si l'insertion échoue.
     */
    public function ajouter(array $donnees): void
    {
        try {
            // Requête préparée pour éviter les injections SQL
            $sql = "INSERT INTO epices
                    (nom, type, prix, vendeur, image, description, note)
                    VALUES
                    (:nom, :type, :prix, :vendeur, :image, :description, :note)";
            $stmt = $this->connexion->prepare($sql);
            // On lie chaque paramètre manuellement
            $stmt->bindValue(':nom', $donnees['nom']);
            $stmt->bindValue(':type', $donnees['type']);
            $stmt->bindValue(':prix', $donnees['prix']);
            $stmt->bindValue(':vendeur', $donnees['vendeur']);
            $stmt->bindValue(':image', $donnees['image']);
            $stmt->bindValue(':description', $donnees['description']);
            $stmt->bindValue(':note', $donnees['note']);
            // Exécution de la requête préparée
            $stmt->execute(); // exécute sans paramètres, car ils sont déjà liés
        } catch (PDOException $e) {
            // On renvoie une exception qui sera capturée par le contrôleur
            throw new Exception('Erreur lors de l\'ajout de l\'épice : ' . $e->getMessage());
        }
    }

    /**
     * modifier(array $donnees)
     * Met à jour une épice existante.
     * Le champ "image" est optionnel selon que l'on fournit un nouveau fichier.
     * @param array $donnees Clé/valeur : ide, nom, type, prix, vendeur, description, note, [image]
     * @throws Exception si la mise à jour échoue.
     */
    public function modifier(array $donnees): void
    {
        try {
            // Début de la requête SQL sans la partie image
            $sql = "UPDATE epices
                    SET nom = :nom,
                        type = :type,
                        prix = :prix,
                        vendeur = :vendeur,
                        description = :description,
                        note = :note";

            // Si on a une nouvelle image, on ajoute la colonne
            if (!empty($donnees['image'])) {
                $sql .= ", image = :image";
            }

            // Clause WHERE pour cibler l'épice à modifier
            $sql .= " WHERE ide = :ide";
            $stmt = $this->connexion->prepare($sql);

            // On lie les paramètres obligatoires manuellement
            $stmt->bindValue(':nom', $donnees['nom']);
            $stmt->bindValue(':type', $donnees['type']);
            $stmt->bindValue(':prix', $donnees['prix']);
            $stmt->bindValue(':vendeur', $donnees['vendeur']);
            $stmt->bindValue(':description', $donnees['description']);
            $stmt->bindValue(':note', $donnees['note']);
            $stmt->bindValue(':ide', $donnees['ide'], PDO::PARAM_INT); // dit clairement le type
            // Si image présente, on la lie aussi
            if (!empty($donnees['image'])) {
                $stmt->bindValue(':image', $donnees['image']);
            }

            // Exécution de la mise à jour
            $stmt->execute(); // exécute sans paramètres, car ils sont déjà liés
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la modification de l\'épice : ' . $e->getMessage());
        }
    }

    /**
     * supprimer(int $ide)
     * Supprime une épice en base selon son identifiant.
     * @param int $ide ID de l'épice à supprimer
     * @throws Exception si la suppression échoue.
     */

    public function supprimer(int $ide): void
    {
        try {
            // Supprimer d'abord les achats liés
            $stmt1 = $this->connexion->prepare("DELETE FROM achats WHERE ide = :ide");
            $stmt1->bindValue(':ide', $ide, PDO::PARAM_INT);
            $stmt1->execute();

            // Puis supprimer l'épice elle-même
            $stmt2 = $this->connexion->prepare("DELETE FROM epices WHERE ide = :ide");
            $stmt2->bindValue(':ide', $ide, PDO::PARAM_INT);
            $stmt2->execute();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la suppression de l\'épice : ' . $e->getMessage());
        }
    }

}
?>