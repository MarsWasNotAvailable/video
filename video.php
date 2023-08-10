<?php
    session_start();

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
            <section class="editor-video-box">
                <?php if (true): ?>
                    <form id="FormForVideoUpload" action="controller.php" method="post" enctype="multipart/form-data" >
                        <label for="">Remplacer la video ?</label>
                        <input type="file" name="video" class="video-selector" accept="video/mp4">
                        <input type="hidden" name="Intention" value="UploadVideo">
                        <input type="hidden" name="id_video" value="<?php echo $CurrentArticleID; ?>">
                    </form>
                <?php endif; ?>
                <video id="vid" width="320" height="240" id="video" controls>
                    <source src="./videos/<?php echo $SelectedArticle['path']; ?>" type=video/mp4>
                    <source src="./videos/404.mp4" type=video/mp4>
                    Your browser does not support the video tag.
                </video>

                <fieldset>
                    <legend>Details de publication:</legend>
                    <div>
                        <label for="is_visible">Visible To Other Users</label>
                        <input class="editable-ajax" type="checkbox" id="is_visible" name="is_visible" <?php echo ($SelectedArticle['is_visible']) ? 'checked' : ''; ?> />
                    </div>

                    <div>
                        <label for="titre">Titre</label>
                        <input class="editable-ajax" type="text" id="titre" name="titre" value="<?php echo ($SelectedArticle['titre']); ?>" readonly />
                    </div>
                </fieldset>
            </section>

            <?php if ($IsLoggedIn): ?>
                <section class="editor-box">


                    <div class="editor">
                        <p><?php echo $SelectedArticle['resume']; ?></p>
                    </div>

                    <script src="./components/quill.js"></script>
                    <script >
                        var quill = new Quill('.editor', {
                            theme: 'snow'
                        });

                        quill.on('selection-change', function(range, oldRange, source) {
                            console.log('range is ', range);
                            if (range) {
                                // https://quilljs.com/docs/api/#selection-change
                                // focus
                            } else {
                                // blur
                                // let about = JSON.stringify(quill.getContents());
                                // let about = quill.getContents();
                                // console.log(about);
                                // console.log(quill.getContents().ops[0].insert);
                                // console.log(quill.root.innerHTML);

                                let url = "./controller.php";

                                let form_data = new FormData();
                                form_data.append('Intention', 'UpdateVideoDescription');
                                form_data.append('id_video', <?php echo $CurrentArticleID; ?>);
                                form_data.append('resume', quill.root.innerHTML);

                                const Request = fetch(url, {
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
                                    return Response.text();
                                })
                                // .then(function (ResponseText) {
                                //     console.log(ResponseText);
                                // })
                                ;
                            }
                        });



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
                    // echo '<input type="hidden" name="id_video" value="' . $CurrentArticleID . '">';
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
                    echo '<input type="hidden" name="id_video" value="' . $CurrentArticleID . '">';
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

        /* Updating the video */
        // This could be inlined in the HTML
        [...document.getElementsByClassName('video-selector')].forEach(Each => {
            Each.addEventListener('change', (Event) => {
                Event.target.parentNode.submit();
            });
        });

        /** Updating the description :
         * The point is to hook all those elements to be able to send genericly their data to databases
         * */
        [...document.querySelectorAll('.editable-ajax')]
        // .concat([...document.querySelectorAll('.image-selector')])
        // .concat(document.querySelector("#Categorie"))
        .forEach(Each => {

            if (!Each) return;

            Each.addEventListener('change', (Event) => {
                console.log(Event);
                // TODO: use the script use for quilljs, as a function so we can reuse it
            });

            // Each.addEventListener('focus', (Event) => {
            // });

            // Each.addEventListener('blur', (Event) => {
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
