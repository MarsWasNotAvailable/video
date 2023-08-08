<?php
    session_start();
    // var_dump($_SESSION);

    if (!isset($_SESSION['crsf_token']))
    {
        header("Location: " . 'login.php');
        die();
    }

    require_once("./components/commons.php");
    require_once("./components/connexion.php");

    $CurrentArticleID = isset($_GET['id_article']) ? $_GET['id_article'] : 0;

    $IsLoggedIn = isset($_SESSION['UserRole']);
    $IsEditingArticle = isset($_GET['edit']) ? $_GET['edit'] && $IsLoggedIn && CanEditArticles($_SESSION['UserRole']) : false;
    $IsEditingComment = isset($_GET['edit']) ? $_GET['edit'] && $IsLoggedIn && CanEditComments($_SESSION['UserRole']) : false;

    $DatabaseName = "viaje";
    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");


    $SelectedCategories = $NewConnection->select("categorie", "nom, id_categorie");
    // var_dump($SelectedCategories);
    $SelectedArticle = null;
    $CurrentArticleCategorie = "none";
    $CurrentArticleCategorieSub = "none";

    // Create new article
    if ($CurrentArticleID < 1 || $CurrentArticleID == 'new')
    {
        $SelectedArticle = array();
    }
    else {
        $SelectedArticle = $NewConnection->select("article", "*", "id_article = $CurrentArticleID");
        foreach ($SelectedArticle as $Key => $Value)
        {
            $CurrentArticleCategorie = strtolower( $SelectedCategories[$Value['categorie'] - 1]['nom'] );
            $CurrentArticleCategorieSub = strtolower( $Value['sous_categorie'] );
            break;
        }

        // $SelectedArticle = $NewConnection->select_full_article($CurrentArticleID);
        // // var_dump($SelectedArticle);
        // foreach ($SelectedArticle as $Key => $Value)
        // {
        //     $CurrentArticleCategorie = strtolower( $Value['nom'] );
        //     $CurrentArticleCategorieSub = strtolower( $Value['sous_categorie'] );
        //     break;
        // }
    }

    if (!$IsEditingArticle && ($CurrentArticleCategorie == "brouillon" || $CurrentArticleCategorieSub == "brouillon" ))
    {
        header("Location: " . 'index.php');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaje:
        <?php
            $ArticlesName = "Article";
            foreach ($SelectedArticle as $Key => $Value) {
                $ArticlesName .= (' - ' . $Value['titre']);
            }
            echo $ArticlesName;
        ?>
    </title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <header>
        <?php include("./components/navbar.php"); ?>
    </header>

    <main>
        <article class="chunks">
            <?php
                function GenerateSection($IsEditingArticle, $SelectedArticle, $SectionNumber, $CurrentArticleCategorieSub)
                {
                    foreach ($SelectedArticle as $Key => $Value)
                    { 
                        echo '<h4 name="' . "sous_titre_$SectionNumber" . '" contenteditable="' . boolalpha($IsEditingArticle) . '">' . $Value["sous_titre_$SectionNumber"] . '</h4>';
                        echo '<p name="' . "contenu_$SectionNumber" . '" contenteditable="' . boolalpha($IsEditingArticle) . '">' . $Value["contenu_$SectionNumber"] . '</p>';

                        $ImageSource = $Value["photo_$SectionNumber"] != '' ?
                            GetImagePath( $Value["photo_$SectionNumber"], $CurrentArticleCategorieSub )
                            : './images/icons_plus.png'
                        ;

                        if ($IsEditingArticle){
                            echo '<label for="' . "photo_$SectionNumber" . '">Selectionner une image:</label>';
                            echo '<input name="' . "photo_$SectionNumber" . '" class="image-selector" type="file" accept="image/*"> ';

                            echo '<img width="256" class="image-preview" src="' . $ImageSource . '" alt="Image Preview">';
                            // echo '<img width="256" class="image-preview" src="' . GetImagePath($Value["photo_$SectionNumber"], 'Australie') . '" alt="Image Preview">';
                        }
                        else {
                            echo '<img src="' . $ImageSource . '" alt="Fancy contextual image for this section" >';
                        }
                    }
                }

                function GenerateCategorieSelector($Categories, $Name, $SelectedId)
                {
                    $SelectedId--; //zero-based vs one-based

                    $Options = "";
                    foreach ($Categories as $Key => $Value)
                    {
                        $SelectState = ($Key == ($SelectedId)) ? 'selected="true' : '';

                        $Options .= '<option ' . $SelectState . ' value="' . $Value['id_categorie'] . '">' . $Value['nom'] . '</option>';
                    }

                    echo '
                        <label for="Categorie">Choose a Categorie:</label>
                        <select name="' . $Name . '" id="Categorie">'
                        . $Options .
                        '</select>
                    ';
                }
            ?>
            <section id="Tete" class="container">

                <?php
                    foreach ($SelectedArticle as $Key => $Value)
                    {
                        if ($IsEditingArticle){
                            
                            GenerateCategorieSelector($SelectedCategories, 'Categorie', $Value['categorie']);

                            echo '<label for="sous_categorie">Insérer une sous catégorie ici:</label>';
                            echo '<h4 name="sous_categorie" contenteditable="true">' . $Value['sous_categorie'] . '</h4>';
        
                            echo '<h2 name="titre" contenteditable="true">' . $Value['titre'] . '</h2>';
                            echo '<h6>' . date('Y-d-m') . '</h6>';

                            $ImageSource = $Value['photo_principale'] != '' ?
                                GetImagePath( $Value['photo_principale'], $CurrentArticleCategorieSub )
                                : './images/icons_plus.png'
                            ;
                            
                            echo '<label for="photo_principale">Selectionner une image:</label>';
                            echo '<input name="photo_principale" class="image-selector" type="file" accept="image/*"> ';

                            echo '<img width="256" class="image-preview" src="' . $ImageSource . '" alt="Image 1">';
                        }
                        else {
                            echo '<h1 name="categorie">Categorie: ' . $SelectedCategories[$Value['categorie'] - 1]['nom'] . '</h1>';
                            echo '<h2 name="titre">' . $Value['titre'] . '</h2>';
                            echo '<h6>' . $Value['date'] . '</h6>';
                            echo '<img src="' . GetImagePath( $Value['photo_principale'], $CurrentArticleCategorieSub ) . '" alt="Image 1">';
                        }
                        
                        echo '<p name="resume" contenteditable="' . boolalpha($IsEditingArticle) . '">' . $Value['resume'] . '</p>';
                        echo '<div >';
                        echo '<fieldset class="Sommaire">';
                        echo '  <legend >Sommaire:</legend>';
                        echo '    <ul>';
                        echo '        <li><a href="#Section1">' . $Value['sous_titre_1'] . '</a></li>';
                        echo '        <li><a href="#Section2">' . $Value['sous_titre_2'] . '</a></li>';
                        echo '        <li><a href="#Section3">' . $Value['sous_titre_3'] . '</a></li>';
                        echo '    </ul>';
                        echo '</fieldset>';
                    }
                ?>
            </section>

            <section id="Section1" class="container">
                <?php
                    GenerateSection($IsEditingArticle, $SelectedArticle, 1, $CurrentArticleCategorieSub);
                ?>
            </section>

            <section id="Section2" class="container">
                <?php
                    GenerateSection($IsEditingArticle, $SelectedArticle, 2, $CurrentArticleCategorieSub);
                ?>
            </section>

            <section id="Section3" class="container">
                <?php
                    GenerateSection($IsEditingArticle, $SelectedArticle, 3, $CurrentArticleCategorieSub);
                ?>
            </section>
        </article>
    </main>

    <section id="Commentaires" class="container chunks">
        <?php
            $AllComments = $NewConnection->select_comments($CurrentArticleID);
            // var_dump($AllComments);

            foreach ($AllComments as $Key => $Value)
            {
                if ($IsEditingComment)
                {
                    echo '<form action="controller.php" method="post" class="article-editor">';
                    echo '<input type="hidden" name="id_article" value="' . $CurrentArticleID . '">';
                    echo '<input type="hidden" name="id_commentaire" value="' . $Value['id_commentaire'] . '">';
                }

                echo '<fieldset class="comments">';
                echo '<legend>' . $Value['nom'] .'</legend>';
                echo '<h6>' . $Value['date'] . '</h6>';

                if ($IsEditingComment)
                {
                    echo '<textarea name="contenu" rows="5">' . $Value['contenu'] . '</textarea>';
                }
                else {
                    echo '<p>' . $Value['contenu'] . '</p>';
                }

                echo '</fieldset>';

                if ($IsEditingComment)
                {
                    echo '<button name="Intention" value="UpdateComment" type="submit">Mettre à jour</button>';
                    echo '<button name="Intention" value="DeleteComment" type="submit">Supprimer</button>';
                    echo '</form>';
                }
            }
        ?>

        <?php if (!$IsEditingComment || $IsEditingArticle): ?>
            <form id="CommentaireForm" action="controller.php" method="POST" >
                <h3>Laisser un commentaires</h3>
                <?php
                    echo '<input type="hidden" name="id_article" value="' . $CurrentArticleID . '">';
                ?>
                
                <input type="text" name="nom" required placeholder="Insérer votre nom ici"
                    <?php
                        if (isset($_SESSION['CurrentUserName']))
                        {
                            echo ' value=' . $_SESSION['CurrentUserName'] . ' readonly';
                        }
                    ?>
                >
                <input type="email" name="email" required placeholder="Insérer votre email ici"
                    <?php
                        if (isset($_SESSION['CurrentUser']))
                        {
                            echo ' value=' . $_SESSION['CurrentUser'] . ' readonly';
                        }
                    ?>
                >
                <textarea type="text" name="contenu" required placeholder="Insérer votre commentaire ici"></textarea>
                <button name="Intention" value="AddComment" type="submit">Publier le commentaire</button>
            </form>
        <?php endif; ?>
    </section>

    <footer>
        <?php include("./components/footer.php"); ?>
    </footer>

    <script>
        /* Variables */

        // We're using the same button for all ajax submit
        let UpdateButton = document.createElement('button');
            UpdateButton.innerHTML = "Update";
            UpdateButton.className = 'update-edit';
            UpdateButton.type = 'button';
        ;

        /* Transmitting informations in between PHP and JS */

        function GetCurrentArticleID()
        {
            return Number( <?php echo $CurrentArticleID; ?> );
        }

        function GetCurrentArticleCategory()
        {
            return <?php echo '"' . $CurrentArticleCategorie . '"'; ?> ;
        }

        function GetCurrentArticleCategorySub()
        {
            return <?php echo '"' . $CurrentArticleCategorieSub . '"'; ?> ;
        }

        /* Image previewing: aesthetic */
        [...document.getElementsByClassName('image-selector')].forEach(Each => {
            Each.addEventListener('change', (Event) => {
                let Section = Event.target.parentNode;

                let src = URL.createObjectURL(Event.target.files[0]);
                let ImagePreviewPlaceholder = Section.getElementsByClassName('image-preview');
                if (ImagePreviewPlaceholder)
                {
                    ImagePreviewPlaceholder[0].src = src;
                }
            });
        });

        /** Updating the article fields:
         * The point is to hook all those elements to be able to send genericly their data to databases
         * */
        [...document.querySelectorAll('*[contenteditable="true"]')]
        .concat([...document.querySelectorAll('.image-selector')])
        .concat(document.querySelector("#Categorie"))
        .forEach(Each => {

            if (!Each) return;

            async function SendUpdateArticleField (Event) {

                let url = "./controller.php";

                let form_data = new FormData();
                form_data.append('Intention', 'UpdateArticleField');
                form_data.append('id_article', GetCurrentArticleID());
                form_data.append('Category', GetCurrentArticleCategory());
                form_data.append('CategorySub', GetCurrentArticleCategorySub());
                form_data.append('Column', Each.getAttribute('name'));

                // We either send a file (images), the content of the form value (#Categorie), or the actual editable text content
                const File = Each.files ? Each.files[0] : null;
                form_data.append(Each.getAttribute('name'), File || Each.value || Each.innerHTML );

                const Request = await fetch(url, {
                    method: "POST",
                    mode: "cors",
                    cache: "no-cache",
                    credentials: "same-origin",
                    // It doesnt work with Content-Type, the WebBrowser will assess the content-type
                    // headers: { 'Content-Type': 'multipart/form-data' },
                    redirect: "follow",
                    referrerPolicy: "no-referrer",
                    body: form_data
                })
                .then(function (Response) { 
                    
                    UpdateButton.remove();
                    UpdateButton.removeEventListener('click', SendUpdateArticleField, true);

                    return Response.text();
                })
                // .then(function (ResponseText) {
                //     console.log(ResponseText);
                // })
                ;

                return true;
            }

            //Hooking up the button to appear below the edited field
            Each.addEventListener('focus', (Event) => {

                Event.target.insertAdjacentElement('afterend', UpdateButton);

                UpdateButton.addEventListener('click', SendUpdateArticleField);

                UpdateButton.style.display = 'block';
            });

            // Hiding the button on blur, but see notes below
            Each.addEventListener('blur', (Event) => {
                //NOTE: originally thought about removing the button when clicking elsewhere
                //BUT because the button click causes a blur event on the editable element,
                //we cannot remove the button here: otherwise we cripple the async fetch
                //We could simply hide it, but the button would still be there existing,
                // and could be clicked by a (malicious?) script
                // setTimeout(()=>{
                //     UpdateButton.style.display = 'none';
                // }, 3600);
            });
        });
    </script>
</body>
</html>
