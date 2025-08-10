<?php
/* ============================================================================
   Nom       : Allemand Jeremy
   Matricule : 20312390
   Cours     : IFT-1147
   TP        : TP3 - E25
   ---------------------------------------------------------------------------
   MembreControleur.php : gère l'ajout d'un nouveau membre et la création de sa connexion
   Cette page reçoit les données POST d'inscription, valide, gère la photo,
   puis insère dans deux tables (membres et connexion) dans une transaction.
   ============================================================================ */

require_once(__DIR__ . "/../../includes/bd/ConnexionSingleton.php");
require_once(__DIR__ . "/../../Membre/modeles/MembreModele.php");

class MembreControleur {
    private $connexion;
    private $dao;

    public function __construct() {
        $this->connexion = ConnexionSingleton::getInstance();
        $this->dao = new MembreModele();
    }

    /**
     * ajouterMembre()
     * Ajoute un nouveau membre apres avoir 
     * rempli le formulaire dans inscription.html
     */
    public function ajouterMembre(): void {
        try {
            // On force le format JSON pour la réponse
            header('Content-Type: application/json; charset=utf-8');

            $nom          = $_POST['nom']          ?? null;
            $prenom       = $_POST['prenom']       ?? null;
            $courriel     = $_POST['courriel']     ?? null;
            $sexe         = $_POST['sexe']         ?? null;
            $datenaissance = $_POST['datenaissance'] ?? null;
            $motdepasse   = $_POST['motdepasse']   ?? null;
            $confirmation = $_POST['confirmation'] ?? null;

            if (!$nom || !$prenom || !$courriel || !$sexe || !$datenaissance || !$motdepasse || !$confirmation) {
                echo json_encode(["erreur" => "Tous les champs obligatoires doivent être remplis"]);
                return;
            }

            // 1) Verifier le bon formatage des champs du formulaire d'inscription
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-_])[A-Za-z\d\-_]{8,10}$/';
            if (!preg_match($regex, $motdepasse)) {
                echo json_encode(["erreur" =>
                    "mot de passe invalide (8-10 caractères avec majuscules, chiffres, -_)"]);
                return;
            }

            if ($motdepasse !== $confirmation) {
                echo json_encode(["erreur" => "Les mots de passe ne correspondent pas"]);
                return;
            }

            if ($this->dao->courrielExiste($courriel)) {
                echo json_encode(["erreur" => "Ce courriel existe déjà"]);
                return;
            }

            // Upload la photo du membre dans le bon repertoire
            $photoNomFinal = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $dossier = __DIR__ . "/../../../serveur/Membre/photos/";

                // On cree le dossier si il existe pas
                if (!is_dir(filename: $dossier)) mkdir($dossier, 0777, true);

                // On genere un identifiant unique et on garde l'extension 
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photoNomFinal = uniqid('user_', true) . ".{$ext}";

                // On deplace la photo
                move_uploaded_file($_FILES['photo']['tmp_name'],$dossier . $photoNomFinal);
            } else {
                $photoNomFinal = ($sexe === 'F')
                    ? 'default_femme.png'
                    : 'default_homme.png';
            }

            // On commence une transaction SQL pour envoyer les requetes
            $this->connexion->beginTransaction();

            $donneesMembre = [
                'nom'          => $nom,
                'prenom'       => $prenom,
                'sexe'         => $sexe,
                'datenaissance' => $datenaissance,
                'photo'        => $photoNomFinal
            ];

            // 1e Requete SQL vers tableau membre
            $idMembre = $this->dao->insererMembre($donneesMembre);
            //2e Requete SQL vers tableau connexion
            $this->dao->insererConnexion($idMembre, $courriel, $motdepasse);
            // On valide la transaction
            $this->connexion->commit();
            // On envoie un message apres succes
            echo json_encode(["message" => "Membre ajouté avec succès"]);
        }
        catch (Exception $e) {
            if ($this->connexion->inTransaction()) {
                // On coupe la transaction si elle est toujours ouverte
                $this->connexion->rollBack();
            }
            http_response_code(500); // code 500 : erreur serveur
            echo json_encode(["erreur" => "Erreur serveur : " . $e->getMessage()]);
        }
    }

    /**
     * modifierProfil()
     * Modifie le profil du membre
     */
    public function modifierProfil(): void {
        // On teste que l'user a le droit de modifier le profil
        session_start();
        if (!isset($_SESSION['courriel'])) {
            echo json_encode(["erreur" => "Non autorisé"]);
            exit;
        }

        // On lit le JSON
        $donnees = $_SERVER['CONTENT_TYPE'] === 'application/json'
            ? json_decode(file_get_contents("php://input"), true)
            : $_POST;

        // On valide les donnees    
        if (!isset($donnees['nom']) || !isset($donnees['prenom'])) {
            echo json_encode(["erreur" => "Champs manquants"]);
            exit;
        }

        // Tableau qui contiendra les modifications du profil
        $modifications = [
            'nom'    => htmlspecialchars($donnees['nom']),
            'prenom' => htmlspecialchars($donnees['prenom'])
        ];

        // On ajoute le mot de passe dans la table modification si non vide
        if (!empty($donnees['motdepasse'])) {
            $modifications['motdepasse'] = $donnees['motdepasse'];
        }

        // On ajoute la photo dans la table modification si non vide
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $dossier = __DIR__ . "/../../../serveur/Membre/photos/";
            if (!is_dir($dossier)) mkdir($dossier, 0777, true);
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photoNom = uniqid('user_', true) . ".{$ext}";
            move_uploaded_file($_FILES['photo']['tmp_name'], $dossier . $photoNom);
            $modifications['photo'] = $photoNom;

        // Update la photo dans la session
            $_SESSION['photo'] = $photoNom;
        }

        // Update le nom, prenom dans la session
        $_SESSION['nom'] = $modifications['nom'];
        $_SESSION['prenom'] = $modifications['prenom'];

        // Appel a la methode du modele pour faire les changements a la bd
        try {
            $this->dao->modifierProfil($_SESSION['courriel'], $modifications);
            echo json_encode(["message" => "Profil mis à jour avec succès"]);
        } catch (Exception $e) {
            echo json_encode(["erreur" => $e->getMessage()]);
        }
    }


    /**
    * getProfil()
    * Charge les donnees du membres pour 
    * le formulaire profil d'AdminMembre.php
    */
    public function getProfil(): void {
        // On verifie l'utilisateur
        session_start();
        if (!isset($_SESSION['courriel'])) {
            echo json_encode(["erreur" => "Utilisateur non connecté"]);
            return;
        }

        // Appelle au modele pour recharger le profil
        try {
            $profil = $this->dao->getProfil($_SESSION['courriel']);
            echo json_encode($profil);
        } catch (Exception $e) {
            echo json_encode(["erreur" => $e->getMessage()]);
        }
    }

    /**
    * payer()
    * Cette méthode enregistre une ou plusieurs lignes d'achat dans la table achats,
    * en associant à chaque ligne les informations du membre (nom, prénom) et de l'épice (nom).
    */ 
    public function payer(): void {
        // On verifie l'utilisateur
        session_start();
        if (!isset($_SESSION['id'])) {
            http_response_code(403); // code 403 : non autorise
            echo json_encode(["erreur" => "Non autorisé"]);
            return;
        }

        try {
            // On lit le JSON
            $listeAchats = json_decode(file_get_contents("php://input"), true);

            // On verifie que la listte n'est pas vide
            if (!is_array($listeAchats) || empty($listeAchats)) {
                http_response_code(400); // code 400 : erreur requete
                echo json_encode(["erreur" => "Liste d'achats invalide"]);
                return;
            }

            // On appelle le modele pour valider l'achat 
            $this->dao->enregistrerAchats($_SESSION['id'], $listeAchats);
            echo json_encode(["message" => "Paiement effectué avec succès"]);

        } catch (Exception $e) {
            http_response_code(500); // code 500 : erreur serveur
            echo json_encode(["erreur" => "Erreur serveur : " . $e->getMessage()]);
        }
    }
}