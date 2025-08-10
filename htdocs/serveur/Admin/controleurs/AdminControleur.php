<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   Contrôleur admin pour gérer les épices et les membres
   ============================================================================ */

// On inclut la connexion à la base et les modèles (DAO)
require_once(__DIR__ . "/../../includes/bd/ConnexionSingleton.php");
require_once(__DIR__ . "/../../Epices/modeles/EpiceModele.php");
require_once(__DIR__ . "/../../Membre/modeles/MembreModele.php");

class AdminControleur
{

    private $epiceDao;
    private $membreDao;

    public function __construct()
    {
        $this->epiceDao = new EpiceModele();
        $this->membreDao = new MembreModele();
    }

    // Fonction pour lire le JSON
    private function lireJson(): array {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->erreur("Format JSON invalide ou vide.");
        }

        return $data;
    }

    /*
    * <--- Gestion des epices (admin) --->
    */

    // --- Affichage des épices pour les visiteurs ---
    public function listerEpicesVisiteur(): void
    {
        // On affiche les 8 meilleures épices
        $this->epiceDao->listerEpicesVisiteur();
    }

    // --- Affichage de toutes les épices pour l'admin ---
    public function listerEpices()
    {
        // On affiche toutes les épices
        $this->epiceDao->listerEpices();
    }

    // --- Ajout d'une nouvelle épice ---
    public function ajouterEpice(): never
    {
        // On force toujours la réponse en JSON
        header('Content-Type: application/json; charset=utf-8');

        // 1) On vérifie que tous les champs sont présents
        if (
            !isset(
            $_POST['nom'],
            $_POST['type'],
            $_POST['prix'],
            $_POST['vendeur'],
            $_POST['description'],
            $_POST['note']
        )
            || !isset($_FILES['image'])
        ) {
            // On renvoie une erreur si un champ manque
            $this->erreur("Champs ou fichier manquant.");
        }

        // 2) On traite l'image uploadée
        $image = $_FILES['image'];
        // Si erreur à l'upload, on renvoie une erreur
        if ($image['error'] !== UPLOAD_ERR_OK) {
            $this->erreur("Image obligatoire");
        }

        // On récupère le nom du fichier et on construit le chemin de destination
        $imageName = basename($image['name']);
        $uploadDir = __DIR__ . "/../../../client/public/images/";
        $uploadPath = $uploadDir . $imageName;

        // On cree le dossier si il n;existe pas
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // On déplace le fichier temporaire vers le dossier
        try {
            if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
                // En cas d’erreur, on renvoie un code 400 et le message d’erreur
                $this->erreur("Échec du déplacement du fichier.");
            }
        } catch (Exception $e) {
            // En cas d’erreur, on renvoie un code 400 et le message d’erreur
            $this->erreur($e->getMessage());
        }

        // 3) On prépare les données à passer au modèle
        $donnees = [
            'nom' => $_POST['nom'],
            'type' => $_POST['type'],
            'prix' => $_POST['prix'],
            'vendeur' => $_POST['vendeur'],
            'description' => $_POST['description'],
            'note' => $_POST['note'],
            'image' => $imageName
        ];

        // 4) On appelle le modèle pour insérer en base, avec gestion d'erreur
        try {
            $this->epiceDao->ajouter($donnees);
        } catch (\Exception $e) {
            // En cas d’erreur, on renvoie un code 400 et le message d’erreur
            $this->erreur("Erreur base de données : " . $e->getMessage());
        }

        // 5) Si tout est OK, on renvoie un message de succès
        echo json_encode(["message" => "Épice ajoutée avec succès."]);
        exit;
    }

    // --- Modification d'une épice existante ---
    public function modifierEpice(): never
    {
        // On force la réponse en JSON
        header('Content-Type: application/json; charset=utf-8');

        // 1) Vérifier que l’ID de l’épice est présent
        if (empty($_POST['ide'])) {
            $this->erreur("ID manquant pour la modification d'une épice.");
        }

        // 2) Récupérer le nom de l’ancienne image depuis le champ caché "oldImage"
        $imageName = $_POST['oldImage'] ?? '';

        // 3) Si un nouveau fichier a été uploadé sans erreur, on remplace l’ancienne image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['image'];
            // On garde seulement le nom du fichier
            $imageName = basename($f['name']);
            // Répertoire public où stocker les images
            $uploadDir = __DIR__ . "/../../../client/public/images/";
            // Déplacement du fichier temporaire vers le dossier final
            try {
                if (!move_uploaded_file($f['tmp_name'], $uploadDir . $imageName)) {
                    throw new Exception("Échec du déplacement du fichier image.");
                }
            } catch (Exception $e) {
                // En cas d’erreur, on renvoie un code 400 et le message d’erreur
                $this->erreur($e->getMessage());
            }
        }
        // Si pas de fichier, $imageName reste l’ancienne valeur

        // 4) Validation des autres champs obligatoires
        if (
            !isset(
            $_POST['nom'],
            $_POST['type'],
            $_POST['prix'],
            $_POST['vendeur'],
            $_POST['description'],
            $_POST['note']
        )
        ) {
            $this->erreur("Certains champs obligatoires sont manquants.");
        }

        // 5) Préparer les données à envoyer au modèle
        $donnees = [
            'ide' => (int) $_POST['ide'],
            'nom' => $_POST['nom'],
            'type' => $_POST['type'],
            'prix' => $_POST['prix'],
            'vendeur' => $_POST['vendeur'],
            'description' => $_POST['description'],
            'note' => $_POST['note'],
            'image' => $imageName
        ];

        // 6) Appeler le DAO pour effectuer la mise à jour
        try {
            $this->epiceDao->modifier($donnees);
            // Succès : on renvoie un message
            echo json_encode(["message" => "Épice modifiée avec succès."]);
        } catch (\Exception $e) {
            // En cas d’erreur, on renvoie un code 400 et le message d’erreur
            $this->erreur("Erreur base de données : " . $e->getMessage());
        }

        exit;
    }


    // --- Suppression d'une épice ---
    public function supprimerEpice(): never
    {
        // On indique que la réponse sera en format JSON
        header('Content-Type: application/json; charset=utf-8');

        // On lit l'ID envoyé en JSON
        $donnees = $this->lireJson();
        if (empty($donnees['id'])) {
            $this->erreur("ID manquant pour la suppression d'une épice");
        }

        // On appelle le modèle pour supprimer et on renvoie un message
        try {
            $this->epiceDao->supprimer($donnees['id']);
            echo json_encode(["message" => "Épice supprimée avec succès."]);
        } catch (\Exception $e) {
            $this->erreur("Erreur base de données : " . $e->getMessage());
        }
        exit;
    }

    /*
    * <--- Gestion des membres (admin) --->
    */

    // Affiche la liste des membres
    public function listerMembres(): never
    {
        // 1) On force la réponse en JSON
        header('Content-Type: application/json; charset=utf-8');

        // 2) Appel au modèle pour récupérer tous les membres
        try {
            $membres = $this->membreDao->listerMembres();
            echo json_encode($membres);
        } catch (\Exception $e) {
            // 3) En cas d’erreur, on renvoie un code 400 et le message d’erreur
            $this->erreur("Erreur base de données : " . $e->getMessage());
        }

        // 4) On termine le script pour éviter tout HTML supplémentaire
        exit;
    }

    // Change le statut d'un membre (A = actif, I = inactif)
    public function changerStatutMembre($statut): never
    {
        // On force
        header('Content-Type: application/json; charset=utf-8');
        // On lit le json
        $donnees = $this->lireJson();
        // Si vide, on retourne un message
        if (empty($donnees['id'])) {
            $this->erreur("ID manquant pour changement de statut membre");
        }
        // Sinon, on modifie le statut
        try {
            $this->membreDao->changerStatut($donnees['id'], $statut);
            echo json_encode(["message" => "Statut du membre mis à jour."]);
        } catch (\Exception $e) {
            $this->erreur("Erreur base de données : " . $e->getMessage());
        }
        exit;
    }

    // Fonction pour renvoyer une erreur JSON
    private function erreur($message): never
    {
        http_response_code(400); // code 400 : erreur requete
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["erreur" => $message]);
        exit;
    }
}
?>