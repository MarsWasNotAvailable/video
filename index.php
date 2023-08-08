<?php
    // session_start();
    // var_dump($_SESSION);

    require_once("./components/connexion.php");
    require_once('./components/commons.php');

    $DatabaseName = "video";

    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");
    // $NewConnection = new MaConnexion("4354353_video", "4354353_video", "marsdemo1", "fdb28.awardspace.net"); //awardspace
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaje: Welcome</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <?php include_once './components/navbar.php' ?>
    </header>

    <main>
        <!-- entête de la page avec l'introduction de la section -->
        <!-- <section class="entete">
            <h1 class="titre"></h1>
            <p class="presentation">Welcome</p>
            <h2>Les nouvelles video du moment ...</h2>
            <p class="presentation">Paf !</p>
        </section> -->
        <!-- <video width="1280" height="720" controls> -->
        <video width="480" controls>
            <source src="./videos/butterfly.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video> 
        
        <!-- Cette section contient tout les articles -->
        <section class="card-container">
            <!-- <?php
                // TODO: currently checking on sous_categorie to hide the drafts, but we should use categorie (makes more sense)
                // will require a INNER JOIN here, replacing the select call
                // $AllVisibleArticles = $NewConnection->select("article", "*", '`article`.`sous_categorie` <> "brouillon"');
                $AllVisibleArticles = $NewConnection->select_full_article_all('`categorie`.`nom` <> "brouillon"');
                foreach($AllVisibleArticles as $display)
                {    
                    echo
                    '<div class="card">
                        <div>
                            <img src="' . GetImagePath( $display['photo_principale'], $display['sous_categorie'] ) . '" class="card-image" alt="">
                        </div>
                        <div class= "card-text">
                            <a href="video.php?id_article=' . $display['id_article'] . '" class="card-title"><h3>' . $display['titre'] . '</h3></a>
                            <p class="date">' . $display['date'] . '</p>
                            <p class="resume">' . $display['resume'] . '</p>
                        </div>
                    </div>';
                }
            ?> -->
        </section> 
    </main>

    <script>
        // window.addEventListener('focus', ()=>{
        //     // We can make the page check if the page session has expired here

        //     let url = "./controller.php";

        //     let form_data = new FormData();
        //         form_data.append('Intention', 'CheckSession');

        //     const Request = fetch(url, {
        //             method: "POST",
        //             mode: "cors",
        //             cache: "no-cache",
        //             credentials: "same-origin",
        //             // headers: { 'Content-Type': 'multipart/form-data' },
        //             redirect: "follow",
        //             referrerPolicy: "no-referrer",
        //             body: form_data
        //         })
        //         .then(function (Response) {
        //             if (!Response.ok)
        //             {
        //                 window.location = 'login.php';
        //             }
        //             // return Response.text();
        //         })
        //         // .then(function (ResponseText) {
        //         //     console.log(ResponseText);
        //         // })
        //         ;
        // });
    </script>

<footer>
    <?php include_once './components/footer.php' ?>
</footer>

</body>
</html>
