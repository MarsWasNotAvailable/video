<?php
    session_start();
    // var_dump($_SESSION);

    if (isset($_SESSION['CurrentUser']))
    {
        header("Location: " . 'index.php');
        die();
    }

    require_once("./components/commons.php");
    require_once("./components/connexion.php");

    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");
?>

<!DOCTYPE html>
<html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video - Connexion</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" >
</head>

<body>
    <section class="login-box">
        <a href="index.php"><img id="Blazon" class="logo" src="./images/icons_site_main.png" alt="L'image principale du site" ></a>
        <h1>Connection</h1>
        <?php
            if (isset($_SESSION['HasFailedLogin']) && $_SESSION['HasFailedLogin'])
            {
                echo '<h4 class="animate__animated animate__shakeX" >Impossible de vous connecter.</h4>';
                
                unset($_SESSION['HasFailedLogin']);
            }
        ?>

        <form action="controller.php" method="POST">
            <div class="input-group">
                <label for="email">Adresse e-mail :</label>
                <input type="text" name="email" required>
            </div>

            <div class="input-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" name="mot_de_passe" required>
            </div>

            <div class="input-group">
                <input name="Intention" value="Login" type="submit">
                <!-- <button name="Intention" value="Login" type="submit">Se connecter</button> -->
            </div>
        </form>

        <h5>Nouveau ici ? Voulez-vous vous <a href="./signin.php">inscrire</a> ?</h5>

    </section>
</body>
</html>
