
<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=solitaire', 'solitaire', 'sol');
} catch(Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
  
?>

<table id="rank_point" class="tablesorter">
    <thead>
    <tr>
        <th style="border: 1px solid black;width:150px">
            Nom 1
        </th>
        <th style="border: 1px solid black;width:150px">
            Nom 2
        </th>

<?php 
    // get every board entered in the table
    $listID = array();
    $reponse = $bdd->query('SELECT * FROM Board');
    while ($donnees = $reponse->fetch()) {
        array_push($listID,$donnees['ID']);
        echo "<th style=\"border: 1px solid black\">\n";
        echo $donnees['Name']."\n";
        echo "</th>\n";
    }
    $reponse->closeCursor();
?>

        <th style="border: 1px solid black">
        <b> Total </b>
        </th>
    </tr>
    </thead>

    <tbody>
<?php 

    
    // get every binome
    $reponse = $bdd->query('SELECT * FROM Binome');
    while ($donnees = $reponse->fetch()) {
        echo "<tr>\n";
        echo "<td>\n";
        echo $donnees['Binome1'];
        echo "</td>\n";
        echo "<td>\n";
        echo $donnees['Binome2'];
        echo "</td>\n";

        $sumScore = 0;
        foreach ($listID as &$value) {
            $reponseScore = $bdd->query("SELECT score FROM Soumission WHERE IDbin=".$donnees['ID']." AND IDsol=".$value." ORDER BY score DESC");
            $donneesScore = $reponseScore->fetch();
            $sumScore += $donneesScore['score'];
            echo "<td>\n";
            echo $donneesScore['score'];
            echo "</td>\n";
        }
        echo "<td>\n";
        echo "<b> ".$sumScore." </b>\n";
        echo "</td>\n";
        echo "</tr>\n";
   }
    $reponse->closeCursor();
     
     
?>
    </tbody>


</table>

