<?php
    // le plateau doit etre mis dans le repertoire livraison avec le nom board.sol
    $nameLivraison = "Livraison_NU6Z";
    $nameBoard = "board 1"; // le nom qui apparaitra sur la page
    
    // connexion à la base
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=solitaire', 'solitaire', 'sol');
    } catch(Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    // Ajout du plateau à la base
    $reponse = $bdd->query("SELECT * FROM Board WHERE Name=\"$nameBoard\"");
    $dlb = $reponse->fetchAll();
    if (sizeof($dlb) == 0) {
        print("ajout du plateau à la base\n");
        try {
            $reponse = $bdd->query("INSERT INTO Board(Name) VALUES ('$nameBoard')");
        } catch(Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
        $reponse = $bdd->query("SELECT * FROM Board WHERE Name=\"$nameBoard\"");
        $dlb = $reponse->fetchAll();
    }
    $IDsol = $dlb[0]['ID'];
    //print_r($IDsol."\n");
    


    $dirs = array_filter(glob("files/$nameLivraison/*"), 'is_dir');
    $sum=0;
    foreach($dirs as &$dir) {

        // lire les fichiers
        $files = array_filter(glob("$dir/*"), 'is_file');
        if (sizeof($files) != 2) {
            die("pas le bon nombre de fichiers\n");
        }
        if (strstr($files[0],"LisezMoi.txt")) {
            $readme=$files[0];
            $coup=$files[1];
        } else {
            $readme=$files[1];
            $coup=$files[0];
        }

        // Trouver le binome, l'ajouter s'il n'existe pas
        $name = exec("cat $readme | sed -n '2p' | sed 's/ - .* \(.*\) .*/\\1/'"); //lis le nom du premier binome dans le fichier lisezmoi.txt
        $name2 = exec("cat $readme | sed -n '3p' | sed 's/ - .* \(.*\) .*/\\1/'"); //lis le nom du deuxieme binome dans le fichier lisezmoi.txt
        //print_r($name."\n");
        //print_r($name2."\n");
        $reponse = $bdd->query("SELECT * FROM Binome WHERE Binome1=\"$name\" OR Binome2=\"$name\"");
        $dlb = $reponse->fetchAll();
        // binome non présent dans la base
        if (sizeof($dlb) == 0) {
            print("Création d'un binome\n");
            try {
                $reponse = $bdd->query("INSERT INTO Binome(Binome1,Binome2) VALUES ('$name','$name2')");
            } catch(Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
            $reponse = $bdd->query("SELECT * FROM Binome WHERE Binome1=\"$name\" OR Binome2=\"$name\"");
            $dlb = $reponse->fetchAll();
        } 
        $IDbin=$dlb[0]['ID'];
        //print_r($IDbin."\n");
        
        
        
        // Calculer le score
        $score = exec("java -jar scripts/checkSol.jar files/$nameLivraison/board.sol \"$coup\" | grep score | sed 's/score : //'");
        //print_r($score."\n");

        // Ajouter la soumission
        try {
            $reponse = $bdd->query("INSERT INTO Soumission(IDsol,IDbin,score) VALUES ('$IDsol','$IDbin','$score')");
        } catch(Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        print("Ajouter à la base le score ".$score." pour ".$name." et ".$name2."\n");
    }

?>
