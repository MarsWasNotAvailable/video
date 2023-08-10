<?php
    session_start();
    // var_dump($_SESSION);

    // if (!isset($_SESSION['crsf_token']))
    // {
    //     header("Location: " . 'login.php');
    //     die();
    // }

    require_once("./components/commons.php");
    require_once("./components/connexion.php");

    $CurrentArticleID = isset($_GET['id_video']) ? $_GET['id_video'] : 0;

    $IsLoggedIn = isset($_SESSION['UserRole']);
    $IsEditingArticle = isset($_GET['edit']) ? $_GET['edit'] && $IsLoggedIn && CanEditArticles($_SESSION['UserRole']) : false;
    $IsEditingComment = isset($_GET['edit']) ? $_GET['edit'] && $IsLoggedIn && CanEditComments($_SESSION['UserRole']) : false;

    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");

    // TODO: need to inner join with User, so we can use that info below (for forwarding to user profile)
    $SelectedArticle = $NewConnection->select('videos', '*', "`id_video` = $CurrentArticleID");

    if (!$SelectedArticle)
    {
        echo 'an error occured';
        die();
    }

    $SelectedArticle = $SelectedArticle[0];

    $CurrentPageName = 'Videos';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video: <?php echo $CurrentPageName; ?></title>
    </title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./components/quill.snow.css" >
</head>
<body>

    <header>
        <?php include("./components/navbar.php"); ?>
    </header>

    <main>
        <article class="chunks video-box">
            <video id="vid" width="320" height="240" id="video" controls>
                <source src="./videos/<?php echo $SelectedArticle['path']; ?>" type=video/mp4>
                <source src="./videos/404.mp4" type=video/mp4>
                Your browser does not support the video tag.
            </video>

            <?php if ($IsLoggedIn): ?>
                <section class="editor-box">
                    <div class="editor">
                        <p>Write your description here</p>
                    </div>

                    <script src="./components/quill.js"></script>
                    <script >
                        var quill = new Quill('.editor', {
                            theme: 'snow'
                        });

                        // let about = JSON.stringify(quill.getContents());
                        // console.log(about);
                        // console.log(quill.getContents());
                    </script>
                </section>
            <?php else: ?>
                <section class="description-box">
                    <a href="./profile.php?"><h3>Profile Handle</h3></a>
                    <h4><?php echo $SelectedArticle['date']; ?></h4>
                    <div><?php echo $SelectedArticle['resume']; ?></div>
                </section>
            <?php endif; ?>
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
                    // TODO: we're going to use Quill here as well (class=".editor")
                    
                    // echo '<form action="controller.php" method="post" class="article-editor">';
                    // echo '<input type="hidden" name="id_article" value="' . $CurrentArticleID . '">';
                    // echo '<input type="hidden" name="id_commentaire" value="' . $Value['id_commentaire'] . '">';
                    // echo '</form>';

                }
                else
                {
                    echo '<fieldset class="comments">';
                    echo    '<legend>' . $Value['name'] .'</legend>';
                    echo    '<h6>' . $Value['date'] . '</h6>';
                    echo    '<p>' . $Value['contenu'] . '</p>';
                    echo '</fieldset>';
                }
            }

            // $_SESSION['CurrentUserName']
            // $_SESSION['CurrentUser']
        ?>


        <?php if (true): ?>
            <form id="CommentaireForm" action="controller.php" method="POST" >
                <h3>Laisser un commentaire</h3>
                <?php
                    echo '<input type="hidden" name="id_article" value="' . $CurrentArticleID . '">';
                ?>

                <input type="text" name="name" required placeholder="Insérer votre nom ici"
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

    
    <script>
        /* Variables */

        /* Transmitting informations in between PHP and JS */

        function GetCurrentArticleID()
        {
            return Number( <?php echo $CurrentArticleID; ?> );
        }

        /** Updating the description :
         * The point is to hook all those elements to be able to send genericly their data to databases
         * */
        [...document.querySelectorAll('.editor')]
        // .concat([...document.querySelectorAll('.image-selector')])
        // .concat(document.querySelector("#Categorie"))
        .forEach(Each => {

            if (!Each) return;

            // async function SendUpdateArticleField (Event) {

            //     let url = "./controller.php";

            //     let form_data = new FormData();
            //     form_data.append('Intention', 'UpdateArticleField');
            //     form_data.append('id_video', GetCurrentArticleID());
            //     form_data.append('Column', Each.getAttribute('name'));

            //     // We either send a file (images), the content of the form value (#Categorie), or the actual editable text content
            //     const File = Each.files ? Each.files[0] : null;
            //     form_data.append(Each.getAttribute('name'), File || Each.value || Each.innerHTML );

            //     const Request = await fetch(url, {
            //         method: "POST",
            //         mode: "cors",
            //         cache: "no-cache",
            //         credentials: "same-origin",
            //         // It doesnt work with Content-Type, the WebBrowser will assess the content-type
            //         // headers: { 'Content-Type': 'multipart/form-data' },
            //         redirect: "follow",
            //         referrerPolicy: "no-referrer",
            //         body: form_data
            //     })
            //     .then(function (Response) { 
                    
            //         UpdateButton.remove();
            //         UpdateButton.removeEventListener('click', SendUpdateArticleField, true);

            //         return Response.text();
            //     })
            //     // .then(function (ResponseText) {
            //     //     console.log(ResponseText);
            //     // })
            //     ;

            //     return true;
            // }

            // //Hooking up the button to appear below the edited field
            // Each.addEventListener('focus', (Event) => {

            //     Event.target.insertAdjacentElement('afterend', UpdateButton);

            //     UpdateButton.addEventListener('click', SendUpdateArticleField);

            //     UpdateButton.style.display = 'block';
            // });

            // // Hiding the button on blur, but see notes below
            // Each.addEventListener('blur', (Event) => {
            //     //NOTE: originally thought about removing the button when clicking elsewhere
            //     //BUT because the button click causes a blur event on the editable element,
            //     //we cannot remove the button here: otherwise we cripple the async fetch
            //     //We could simply hide it, but the button would still be there existing,
            //     // and could be clicked by a (malicious?) script
            //     // setTimeout(()=>{
            //     //     UpdateButton.style.display = 'none';
            //     // }, 3600);
            // });
        });
    </script>

    <footer>
        <?php include("./components/footer.php"); ?>
    </footer>
</body>
</html>
