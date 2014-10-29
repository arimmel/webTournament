
<?php


function generateGraph($labels,$values,$canName) {

    echo "<div style=\"width: 50%; margin-top: 100px\">\n";
    echo "<canvas id=\"$canName\" height=\"300\" width=\"600\" align=\"center\"></canvas>\n";
    echo "</div>\n";

    echo "<script>\n";
    echo "var barChartData = {\n\n";

    echo "labels : [";
        //insert labels
        foreach($labels as $key => &$label) {
            end($labels);
            if ($key === key($labels)) {
                echo "\"".$label."\"],\n";
            }else{
                echo "\"".$label."\", ";
            }
        }
    echo "datasets : [\n";
    echo "   {\n";
    echo "        fillColor : \"rgba(151,187,205,0.5)\",\n";
    echo "        strokeColor : \"rgba(151,187,205,0.8)\",\n";
    echo "        highlightFill : \"rgba(151,187,205,0.75)\",\n";
    echo "        highlightStroke : \"rgba(151,187,205,1)\",\n";
    echo "        data : [";

    //insert values
    foreach($values as $key => &$value) {
            end($values);
            if ($key === key($values)) {
                echo $value."],\n";
            }else{
                echo $value.", ";
            }
    }
    echo "}]}\n";

    echo "var ctx = document.getElementById(\"$canName\").getContext(\"2d\");\n";
    echo "window.myBar = new Chart(ctx).Bar(barChartData, {\n";
    echo "    responsive : true\n";
    echo "});\n";
    echo "</script>\n";
}

function getRank($IDbin,$IDsol,$bdd) {

    // get all the scores
    $reponse = $bdd->query("SELECT * FROM Binome");
    $listScore = array();
    while ($donnees = $reponse->fetch()) {
        $listScore[$donnees['ID']] = getScore($donnees['ID'],$IDsol,$bdd);
    }
    $listIDscore = $listScore;
    sort($listScore);

    // create the rank in function of the score array
    $rank=1;
    $listScoreRank = array();
    foreach($listScore as $score) {
        if (!array_key_exists($score,$listScoreRank)) {
            $listScoreRank[$score] = $rank;
        }
        $rank++;
    }

    // create the rank in function of the ID array
    $listIDrank = array();
    foreach($listIDscore as $id=>$score) {
        $listIDrank[$id] = $listScoreRank[$score];
    }

    return $listIDrank[$IDbin];
}

function getScore($IDbin, $IDsol,$bdd) {
    $reponseScore = $bdd->query("SELECT score FROM Soumission WHERE IDbin=".$IDbin." AND IDsol=".$IDsol." ORDER BY score ASC");
    $donneesScore = $reponseScore->fetch();
    if ($donneesScore['score']) {
        $score = $donneesScore['score'];
    } else {
        $reponseScore = $bdd->query("SELECT score FROM Soumission WHERE IDsol=".$IDsol." ORDER BY score DESC");
        $donneesScore = $reponseScore->fetch();
        $score = $donneesScore['score'] + 10;
    }
    return $score;
}

function getNbCol($bdd) {
    $reponse = $bdd->query("SELECT * FROM Board");
    $donnees= $reponse->fetchAll();
    $nbCol = 2 + 2 + 2*sizeof($donnees);
    print($nbCol."\n");

}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=solitaire', 'solitaire', 'sol');
} catch(Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
  
?>

<script>
    $(function() {
        $("#rank_point").tablesorter({
            sortList: [[ <?php echo getNbCol($bdd)-1; ?> ,0]]
        });
    });
</script>


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
    $reponse = $bdd->query('SELECT * FROM Board ORDER BY ID ASC');
    while ($donnees = $reponse->fetch()) {
        array_push($listID,$donnees['ID']);
        echo "<th style=\"border: 1px solid black\">\n";
        echo $donnees['Name']."\n";
        echo "</th>\n";
        echo "<th style=\"border: 1px solid black\">\n";
        echo "rank\n";
        echo "</th>\n";
    }
    $reponse->closeCursor();
?>

        <th style="border: 1px solid black">
        Total
        </th>
        <th style="border: 1px solid black">
        <b> Av Rank </b>
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
        $sumRank = 0;
        foreach ($listID as &$value) {
            $rank = getRank($donnees['ID'],$value,$bdd);
            $score = getScore($donnees['ID'],$value,$bdd);
            $sumScore += $score;
            $sumRank += $rank;
            echo "<td>\n";
            echo $score;
            echo "</td>\n";
            echo "<td>\n";
            echo $rank;
            echo "</td>\n";
        }
        echo "<td>\n";
        echo $sumScore."\n";
        echo "</td>\n";
        echo "<td>\n";
        echo "<b> ".$sumRank/sizeof($listID)." </b>\n";
        echo "</td>\n";
        echo "</tr>\n";
   }
    $reponse->closeCursor();
     
     
?>
    </tbody>


</table>




<?php 
    arsort($listID);
    foreach($listID as &$IDsol) {
        $listName = array();
        $listScore = array();
        $reponse = $bdd->query("SELECT * FROM Binome");
        while ($donnees = $reponse->fetch()) {
           array_push($listName,$donnees['Binome1']." - ".$donnees['Binome2']); 
           $score = getScore($donnees['ID'],$IDsol,$bdd);
           array_push($listScore,$score);
        }
        generateGraph($listName,$listScore,"Board".$IDsol);
    }

?>
