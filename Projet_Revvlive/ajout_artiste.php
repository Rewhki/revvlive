<?php
//Déclaration de ma variable d'affichage
$message = "";

//Création des variables demandées (non obligatoire sauf artist_name)
$artist_name = "";
$alias = "";
$birth_date = "";
$death_date = "";
$biography = "";

/**
 * Sanitize() : Nettoie les données
 * Return : String
 */
function sanitize($data){
    return htmlentities(strip_tags(stripslashes(trim($data))));
}

// Fonction qui teste les données du formulaire
// Return : array["nom"=>string,"contenu"=>string,"erreur"=>string]
function formArtist(){
    // 1er Etape de Sécurité : vérifier le champ obligatoire
    if(empty($_POST["nom_artiste"])){
        return ["erreur"=>"Veuillez remplir le nom de l'artiste"];
    }

    // 2nd Etape de sécurité : vérifier le format des dates avec une regex (AAAA-MM-JJ) si fournies
    if (!empty($_POST["birth_date"]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["birth_date"])) {
        return ["erreur" => "Date de naissance pas au bon format (AAAA-MM-JJ)"];
    }

    if (!empty($_POST["death_date"]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["death_date"])) {
        return ["erreur" => "Date de décès pas au bon format (AAAA-MM-JJ)"];
    }

    // 3eme Etape de sécurité : nettoyage des données
    $artist_name = sanitize($_POST["nom_artiste"]);
    $alias = !empty($_POST["alias"]) ? sanitize($_POST["alias"]) : null;
    $birth_date = !empty($_POST["date_naissance"]) ? sanitize($_POST["date_naissance"]) : null;
    $death_date = !empty($_POST["date_mort"]) ? sanitize($_POST["date_mort"]) : null;
    $biography = !empty($_POST["biographie"]) ? sanitize($_POST["biographie"]) : null;

    // Je retourne un tableau pour distinguer chaque donnée et les récupérer facilement
    return [
        "nom_artiste" => $artist_name,
        "alias" => $alias,
        "date_naissance" => $birth_date,
        "date_mort" => $death_date,
        "biographie" => $biography,
        "erreur" => ""
    ];
}

// Fonction qui enregistre les données du formulaire en BDD
// Return : string
function addArtist($artist_name, $alias, $birth_date, $death_date, $biography){
    // 1er Etape : Instancie l'objet de connexion PDO
    $bdd = new PDO('mysql:host=localhost;dbname=revvlive', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    // Try... catch
    try {
        // 2nd Etape : préparation de la requête
        $req = $bdd->prepare('INSERT INTO artiste (nom_artiste, alias, date_naissance, date_mort, biographie) VALUES (?,?,?,?,?)');

        // 3eme Etape : je relis les ? à leurs données respectives grâce à bindParam()
        $req->bindParam(1, $artist_name, PDO::PARAM_STR);
        $req->bindParam(2, $alias, PDO::PARAM_STR);
        $req->bindParam(3, $birth_date, PDO::PARAM_STR);
        $req->bindParam(4, $death_date, PDO::PARAM_STR);
        $req->bindParam(5, $biography, PDO::PARAM_STR);

        // 4eme Etape : exécution de la requête
        $req->execute();

        // Pour finir : retourne un message de confirmation
        return "$artist_name a été enregistré avec succès !";

    } catch (EXCEPTION $error) {
        return $error->getMessage();
    }
}

// Je vérifie que je reçois le formulaire
if(isset($_POST["submit"])){
    // Je lance ma fonction de test du formulaire
    $tab = formArtist();

    // Je teste si j'ai une erreur
    if($tab["erreur"] != ""){
        $message = $tab["erreur"];
    } else {
        // J'appelle la fonction addArtist avec les bons paramètres
        $message = addArtist(
            $tab["nom_artiste"],
            $tab["alias"],
            $tab["date_naissance"],
            $tab["date_mort"],
            $tab["biographie"]
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@400;700&display=swap" rel="stylesheet">
    <title>Ajout Artiste</title>
</head>
<body>
    <div class="artist-form">
        <h1 class="form-title">AJOUT D'ARTISTE</h1>
        <form action="" method="post">
            <p>
                Artiste* : <input type="text" name="nom_artiste" placeholder="Nom de l'artiste">
            </p>
            <p>
                Alias : <input type="text" name="alias" placeholder="Alias séparé par une virgule">
            </p>  
            <p>
                Date naissance : <input type="text" name="date_naissance" placeholder="AAAA-MM-JJ">
            </p>
            <p>
                Date mort : <input type="text" name="date_mort" placeholder="AAAA-MM-JJ">
            </p>
            <p>
                Biographie : <input type="text" name="biographie" placeholder="Biographie de l'artiste">
            </p>
            <p>
                <input type="submit" id="add_artist" name="submit">
            </p>
        </form>
        <p><?php echo $message ?></p>
    </div>
</body>
</html>