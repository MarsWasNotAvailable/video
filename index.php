<?php
    require_once("./components/connexion.php");
    require_once('./components/commons.php');

    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video: Welcome</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">

    <script type="text/javascript" src="./components/thumbnail.js"></script>
</head>

<body>
    <header>
        <?php include_once './components/navbar.php' ?>
    </header>

    <main>
        <!-- Cette section contiendra tout les articles -->
        <section class="card-container">
        </section> 
    </main>

    <script defer >
        // $Each['resume']
        <?php
            $AllVisibleArticles = $NewConnection->select_recent_videos();
            foreach($AllVisibleArticles as $Each):
            $Parameters = '{
                VideoId : ' . $Each['id_video'] . ',
                Path : "' . $Each['path'] . '",
                Titre : "' . $Each['titre'] . '"
            }';
        ?>
            CreateCardForVideo(<?php echo $Parameters; ?>);
        <?php endforeach; ?>

        // window.addEventListener('focus', ()=>{
        //     CheckIfUserIsLoggedIn(()=>{
        //         window.location = 'login.php';
        //     });
        // });
    </script>

    <footer>
        <?php include_once './components/footer.php' ?>
    </footer>
</body>
</html>
