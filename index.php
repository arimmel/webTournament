<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/styletable.css" type="text/css" />
        <title>Classement Solitaire</title>
        <script src="js/Chart.js"></script>
        <script src="js/jquery-latest.js"></script> 
        <script src="js/jquery.tablesorter.js"></script> 
        <script>
            $(function() {
                $("#rank_point").tablesorter({
                    sortList: [[6,0]]
                });
            });
        </script>
    </head>

    <body>

        <div id="main_page"> 

            <div id="top_page">
            <img src="images/solitaire.jpg" alt="Solitaire" style="width:150px;height:150px">

            </div>

            <div id="middle_page">
                <?php include("classement.php"); ?>
            </div>

            <footer>
                <a href="mailto:arpad.rimmel@supelec.fr">Contacter l'admin</a> 
            </footer>
        </div>
    </body>
</html>

