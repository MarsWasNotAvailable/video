<?php
    require_once("./components/commons.php");
    require_once("./components/connexion.php");
    $NewConnection = new MaConnexion("viaje", "root", "", "localhost");

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

    
/* Styles des éléments de la navbar */
.parallax {
    /* display: inline-block; */
    /* padding: 0 20px; */
    cursor: pointer;
    /* color: #FFD700; Couleur du texte par défaut */
}

/* Animation des éléments de la navbar lors du clic */
.parallax.clicked {
    animation: whirl-shrink-and-grow 2s linear infinite, text-fade 2s linear infinite; /* Animation au clic */
}

/* Animation d'effet de tourbillon et de rétrécissement/agrandissement */
@keyframes whirl-shrink-and-grow {
    0% { transform: rotate(0deg) scale(1); }
    25% { transform: rotate(90deg) scale(0.3); }
    50% { transform: rotate(180deg) scale(0.2); }
    75% { transform: rotate(270deg) scale(0.3); }
    100% { transform: rotate(360deg) scale(1); }
}

/* Animation de changement de couleur */
@keyframes text-fade {
    0%, 100% { color: #FFD700; }
    25% { color: #FFA500; }
    50% { color: #FF8C00; }
    75% { color: #FF4500; }    
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
                    <a href="./profile.php"><img src=<?php echo '"' . $UserIcon . '"'; ?> alt="User Role Image" style="width: 32px; height: 32px;"></a>
                <?php else: ?>
                    <button class="ConnexionButtons green-button" onclick="window.location='./login.php'">Login</button>
                <?php endif ?>
            </div>
        </div>

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

    // Fonction d'animation de l'effet gyroscopique sur l'image de fond
    // function animateBackground() {
    //     const body = document.body;
    //     body.style.animation = "gyroscopic-effect 2s linear"; // Animation d'effet gyroscopique pour l'image de fond
    //     setTimeout(() => {
    //         body.style.animation = "none"; // Réinitialise l'animation après 2 secondes (ajuste cette valeur selon ton préférence)
    //     }, 2000);
    // }

    // Ajout d'un gestionnaire d'événement au clic pour chaque élément de la navbar
/*     constElements = document.querySelectorAll(".parallax");
    Elements.forEach((element) => {
        element.addEventListener("click", function (event) {
            event.preventDefault();
            this.classList.add("clicked"); // Ajoute la classe "clicked" au clic sur un élément de la navbar
            animateBackground(); // Déclenche l'effet gyroscopique au clic sur un élément de la navbar
            setTimeout(() => {
                this.classList.remove("clicked"); // Retire la classe "clicked" après l'animation
                window.location = event.target.href;
            }, 2000);
        });
    }); */

</script> 

<?php    
/* TODO: Can you auto generate those section based on the categorie table
* I dont like the multiple selects
* UD: well, it will require a change on the database about categories and sub categories
    because we want to list unique continent at top level, and countries as sub level
* I tried: GROUP BY, didn't work
*/

    // $AllCategories = $NewConnection->select("categorie", "*", '(`nom` <> "Brouillon" AND `nom` <> "Pratique")');
    
    // foreach ($AllCategories as $Each) {
    //     $HasDropDownContent = !($Each['continent'] == '' || $Each['continent'] == 'NA' || $Each['continent'] == 'Pratique');

    //     echo '<div class="dropdown">';
    //     if ($HasDropDownContent)
    //     {
    //         echo '<a href="#">' . $Each['continent'] . '</a>';
    //         echo '<div class="dropdown-content">';
    //         echo '<a href="categorie.php?id_categorie='. $Each['id_categorie'] . '">' .$Each['nom'] . '</a>';
    //         echo '</div>';
    //     }
    //     else
    //     {
    //         echo '<a href="categorie.php?id_categorie='. $Each['id_categorie'] . '">' .$Each['nom'] . '</a>';
    //     }
    //     echo '</div>';
    // }
?>
