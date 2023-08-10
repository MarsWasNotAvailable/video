<?php
    session_start();

    require_once("./components/commons.php");
    require_once("./components/connexion.php");
    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");

    // Vérifie si des mot-clés de recherches ont été soumis
    $HaveKeywords = $_GET && isset($_GET["search_query"]);
    $MotsRecherche = $HaveKeywords ? $_GET["search_query"] : '';

    // $HaveKeywords = $MotsRecherche != '';
    $HaveKeywords = !empty($MotsRecherche);

    $CurrentPageName = "Recherche";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video - <?php echo $CurrentPageName; ?></title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="style.css">

    <script type="text/javascript" src="./components/thumbnail.js"></script>
</head>

<body>
    <header>
        <?php include_once './components/navbar.php' ?>
        <!-- TODO: just put the searched keyword back into the search bar -->
    </header>

    <main>
        <?php if ($HaveKeywords): ?>
            <!-- Cette section contiendra tout les articles -->
            <!-- <section class="display-result"> -->
            <section class="card-container">
            </section>

            <script defer >
                <?php
                    $ArticlesCorrespondants = $NewConnection->select("videos", "*", "`titre` LIKE '%$MotsRecherche%' OR `resume` LIKE '%$MotsRecherche%' OR `path` LIKE '%$MotsRecherche%'");
                    foreach($ArticlesCorrespondants as $Each):
                    $Parameters = '{
                        VideoId : ' . $Each['id_video'] . ',
                        Path : "' . $Each['path'] . '",
                        Titre : "' . $Each['titre'] . '"
                    }';
                ?>
                    CreateCardForVideo(<?php echo $Parameters; ?>);
                <?php endforeach; ?>
                
            </script>
        <?php endif ?>
    </main>

    <footer>
        <?php include_once './components/footer.php' ?>
    </footer>

</body>
</html>
