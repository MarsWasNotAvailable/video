<?php
    session_start();

    require_once("./components/commons.php");
    require_once("./components/connexion.php");



    $HaveProfileKey = $_GET && isset($_GET["profile"]) && !empty($_GET['profile']);

    if (!$HaveProfileKey)
    {
        header("Location: " . 'index.php');
        die();
    }

    $ProfileKey = $_GET['profile'];
    $IsUserLoggedIn = isset($_SESSION['CurrentUser']) && ($ProfileKey == $_SESSION['UserID']);

    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");

    $CurrentPageName = 'Profile'; 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video - <?php echo $CurrentPageName; ?></title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">

    <script type="text/javascript" src="./components/thumbnail.js"></script>
</head>

<body >
    <header>
        <?php include_once './components/navbar.php'; ?>
    </header>

    <main class="one-or-two-part">
        <!-- Cette section contiendra tout les articles -->
        <section class="card-container">
            <?php if ($IsUserLoggedIn):?>

                <form method="POST" action="controller.php" class="card-title card">
                    <input type="image" alt="Push To Create New Video" src="./images/icons_plus.png" height="240" />
                    <input type="hidden" required name="Intention" value="CreateVideo" />
                    <h4 >Create New Video</h4>
            </form>
            <?php endif; ?>
        </section>

        <?php if ($IsUserLoggedIn):
            $UserIcon = './images/icons_user_role_' . $_SESSION['UserRole'] . '.png';
            ?>
            <section class="login-box">
                <form class="profile-box" action="controller.php" method="POST">
                    <img src=<?php echo '"' . $UserIcon . '"'; ?> alt="User Role Image" style="width: 256px; height: 256px;">

                    <input type="hidden" name="id_user" value=<?php echo '"' . $_SESSION['UserID'] . '"'; ?> required >

                    <div class="input-group">
                        <label for="name">Nom :</label>
                        <input type="text" name="name" value=<?php echo '"' . $_SESSION['CurrentUserName'] . '"'; ?> required >
                    </div>

                    <!-- <div class="input-group">
                        <label for="email">Adresse e-mail :</label>
                        <input type="text" name="email" value=<?php echo '"' . $_SESSION['CurrentUser'] . '"'; ?> required >
                    </div> -->

                    <!-- <div class="input-group">
                        <label for="mot_de_passe">Mot de passe :</label>
                        <input type="password" name="mot_de_passe" required>
                    </div> -->

                    <div class="input-group">
                        <!-- <input name="Intention" value="UpdateProfile" type="submit"> -->
                        <button name="Intention" value="UpdateProfile" type="submit">Mettre à jour</button>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </main>



    <script defer >

        // $Each['resume']
        <?php
            $ProfilesVideo = $NewConnection->select("videos", "*", "`fk_user` = $ProfileKey");
            foreach($ProfilesVideo as $Each):
            $Parameters = '{
                IsEditable : true,
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
