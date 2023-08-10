<?php
    require_once("./components/commons.php");
    require_once("./components/connexion.php");

    if (session_id() == "")
    {
        session_start();
    }

    $IsUserLoggedIn = isset($_SESSION['CurrentUser']);
    $CanEditArticles = (isset($_SESSION['UserRole']) && CanEditArticles($_SESSION['UserRole']));

?>

<!-- Because this link element is set as rel stylesheet, it is body-ok
    That means, it does not need to be placed in the head of each documents:
    https://webmasters.stackexchange.com/questions/55130/can-i-use-link-tags-in-the-body-of-an-html-document/137977#137977
    It is clearer to have it there
 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    /* Styles spécifiques pour la navbar */

    .logo {
        width: 200px;
        height: auto;
        display: block;
        margin: 10px auto;
    }

    .navbar {
        background-color: #40374f;

        padding-top: 1em;
        padding-right: 3em;
        padding-bottom: 1em;
        padding-left: 3em;
        display: flex;
        position: relative;
        z-index: 2;
    }

    .navbar a {
        color: white;
        font-size: 1em;
        margin-right: 2em;
        /* Ajuste l'espace entre les onglets */
        text-decoration: none;
        text-transform: uppercase;
    }

    .navbar a:hover {
        color: #4CAF50;
    }

    .dropdown {
        position: relative;
        display: inline-block;
        
    }

    @media (max-width:1023px) {
        .topnav.responsive .dropdown {
            margin-left: 7%;
        }
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        padding: 8px 0;
        z-index: 1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
        border-radius: 16px;
    }

    .dropdown-content a {
        color: #333;
        text-decoration: none;
        display: block;
        padding: 8px 16px;
    }

    .dropdown-content a:hover {
        /* background-color: #f1f1f1; */
        /* enleve le fond gris dans le texte exemple: chine ,japon etcc pour les autres */  
        background-color: transparent;
    }
   

    #search {
        display: inline-block;
    }

    #search input[type="text"] {
        padding: 5px;
        border-radius: 3px;
        border: none;
    }

    #search button {
        padding: 5px 10px;
        background-color: #f46dab;
        color: white;
        border: none;
        cursor: pointer;
    }

    .icon {
        display: none;
    }

    @media (max-width:1023px) {

        .navbar {
            padding: 3% 0;
            display: flex;
            justify-content: space-between;
        }

        .topnav a,
        .topnav input,
        .topnav button {
            display: none;
        }

        .topnav a.icon {
            float: right;
            display: block;
            margin-right: 0%;
            color: #000000;
        }

        .topnav.responsive {
            position: relative;
            display: grid;
        }

        .topnav.responsive .icon {
            position: absolute;
            right: 0;
            top: 0;
        }

        .topnav.responsive a,
        .topnav.responsive input {
            float: none;
            display: block;
            text-align: left;
        }

        #search{
            width: 30%;
            display: inline-grid;
            justify-content: center;
        }
        .topnav.responsive input{
            width: 3em;
        }
    }

    .float-login {
        float: right;        
    }
</style>

<nav class="navbar">

    <div class="dropdown">
        <a id="HomeLink" href="index.php"><img id="Blazon" class="logo" src="./images/icons_site_main.png" alt="image principale du site">
        </a>
    </div>


    <div class="topnav" id="myTopnav">

        <div id="search" class="dropdown">
            <form method="GET" action="recherche.php">
                <input type="text" name="search_query" id="search_query" placeholder="Entrez votre recherche ici">
                <button type="submit">Rechercher</button>
                <a href="javascript:void(0);" class="icon" onclick="searchLogo()">
                    <i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
                </a>
            </form>
        </div>

        <?php if($IsUserLoggedIn && $CanEditArticles): ?>
            <div class="dropdown">
                <a href="./gestion.php">MY PAGE</a>
            </div>
        <?php endif; ?>

        <div id="SiteHead" class="dropdown">
            <div >
                <?php if ($IsUserLoggedIn): $UserIcon = './images/icons_user_role_' . $_SESSION['UserRole'] . '.png';
                    ?>
                    <form method="POST" action="controller.php"><input type="submit" name="Intention" value="Logout" class="ConnexionButtons red-button" ></form>
                    <a href="./profile.php?profile=<?php echo $_SESSION['UserID']; ?>"><img src=<?php echo '"' . $UserIcon . '"'; ?> alt="User Role Image" style="width: 32px; height: 32px;"></a>
                <?php else: ?>
                    <button class="ConnexionButtons green-button" onclick="window.location='./login.php'">Login</button>
                <?php endif ?>
            </div>
        </div>

        <?php if ($CurrentPageName): ?>
            <div class="dropdown">
                <h2><?php echo $CurrentPageName; ?></h2>
            </div>
        <?php endif; ?>

        <!-- <a href="javascript:void(0);" class="icon" onclick="burgerMenu()">
            <i class="fa fa-bars"></i>
        </a>  -->
    </div>

    
</nav>

<script>
    function burgerMenu() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }

    function searchLogo() {
        var x = document.getElementById("search");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
</script> 
