<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Controller</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        // var_dump($_POST);
        
        include("./components/connexion.php");
        include("./components/commons.php");
        // $DatabaseName = "viaje";
        $UsersTableName = "users";
        $CommentsTableName = "commentaire";
        $ArticleTableName = "videos";
        $Redirection = "index.php";
        $ArticlePageRedirection = './video.php';
        $NotAllowedRedirection = './index.php';
        $RedirectionAfterLogin = './profile.php';

        $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");

        if (isset($_POST['Intention']))
        {
            switch ($_POST['Intention']) {

                case 'Signup':

                    // $HashedPassword = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                    $HashedPassword = password_hash($_POST['mot_de_passe'], PASSWORD_ARGON2ID, ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2]);

                    $Values = array(
                        'email' => $_POST['email'],
                        'name' => $_POST['name'],
                        'mot_de_passe' => $HashedPassword,
                        'role' => 'guest'
                    );
                    // var_dump($Values);

                    $Success = $NewConnection->insert($UsersTableName, $Values);

                    if (!$Success)
                    {
                        session_start();

                        $_SESSION['HasFailedSignedUp'] = true;

                        header("Location: " . 'signin.php');
                        die();
                    }

                    // NOTE: we let fall through from signup to login, so it automatically logs in
                    // break;
                    
                case 'Login':
                    $Condition = '(`email` = "' . $_POST['email'] . '")';
                    $UniqueUser = $NewConnection->select($UsersTableName, "*", $Condition);
                    // var_dump($UniqueUser[0]);

                    session_start([
                        'cookie_lifetime' => (30 * 60) //lifetime of session in seconds
                    ]);

                    // I truly don't see the point of that:
                    // 1. we can't react dynamically to it (best we can do it inefficient polling)
                    // 2. we have other field (like UserName or UserRole to secure the app)
                    // 3. as soon as the session expires, everything is meant to adapt to a guest experience
                    $_SESSION['crsf_token'] = bin2hex(random_bytes(32));

                    // TODO: uncomment when going in presentation
                    if ($UniqueUser /* && password_verify($_POST['mot_de_passe'], $UniqueUser[0]['mot_de_passe']) */) {

                        $_SESSION['CurrentUser'] = $UniqueUser[0]['email'];
                        $_SESSION['CurrentUserName'] = $UniqueUser[0]['name'];
                        $_SESSION['UserRole'] = $UniqueUser[0]['role'];
                        $_SESSION['UserID'] = $UniqueUser[0]['id_user'];

                        // if (isset($_SESSION['HasFailedSignedUp']))
                        //     unset($_SESSION['HasFailedSignedUp']);

                        // if (isset($_SESSION['HasFailedLogin']))
                        //     unset($_SESSION['HasFailedLogin']);

                        header('Location: ' . $RedirectionAfterLogin . '?profile=' . $_SESSION['UserID']);
                        die();
                    }
                    else {
                        $_SESSION['HasFailedLogin'] = true;

                        header("Location: " . 'login.php');
                        die();
                    }

                    // var_dump($_SESSION);

                    break;

                case 'Logout':
                    session_start();

                    // session_unset();
                    session_destroy();
                
                    // var_dump($_SESSION);
                
                    header('Location: ' . $Redirection );
                    die();

                    break;

                case 'CreateVideo':
                    session_start();
                    /* EXAMPLE:
                        INSERT INTO `videos` (`id_video`, `fk_user`, `titre`, `date`, `path`, `resume`) VALUES
                        (1, 3, 'rapture', '2023-08-09', 'lorem_life.mp4', 'come...'),
                    */
                    $VideoID = $NewConnection->insert( 'videos', array(
                        'fk_user' => $_SESSION['UserID'],
                        'titre' => 'Sans titre',
                        'resume' => 'Ajouter un résumé ici.'
                    ));
                    
                    var_dump($VideoID);
            
                    if ($VideoID)
                    {
                        header("Location: " . "./video.php?edit=true&id_video=$VideoID");
                        die();
                    }

                    break;

                case 'DeleteVideo':
                    $UpdateFieldCondition = array('id_video' => $_POST['id_video']);

                    $Success = $NewConnection->delete($ArticleTableName, $UpdateFieldCondition);

                    if ($Success) {
                        header("Location: " . 'profile.php?id_profile=' . $_POST['id_video']);
                        die();
                    }
                    break;

                case 'AddComment':

                    session_start();

                    $Values = array(
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'mot_de_passe' => bin2hex(openssl_random_pseudo_bytes(4)),
                        'role' => 'guest'
                    );

                    $UserID = $NewConnection->insert_update($UsersTableName, $Values, array('Key' => 'name', 'Value' => $Values['name']));

                    $Comments = array(
                        'contenu' => $_POST['contenu'],
                        'fk_video' => $_POST['id_video'],
                        'fk_user' => $UserID
                    );

                    $CommentsID = $NewConnection->insert($CommentsTableName, $Comments);
                    // var_dump($CommentsID);

                    if ($UserID && $CommentsID)
                    {
                        require_once('./components/commons.php');

                        $CanEditToken = CanEditComments($_SESSION['UserRole']) ? 'edit=true&' : '&';

                        header("Location: " . $ArticlePageRedirection . '?' . $CanEditToken . 'id_video=' . $_POST['id_video'] . '#Commentaires');
                        die();
                    }

                    break;

                case 'UpdateComment':
                    require_once('./components/commons.php');
                    session_start();

                    if (CanEditComments($_SESSION['UserRole']))
                    {
                        $Values = array();
                    
                        $FieldsToUpdate = array('contenu');
                        foreach ($FieldsToUpdate as $EachKey => $EachValue){
                            if ($_POST[$EachValue]) $Values += array($EachValue => $_POST[$EachValue]);
                        }
                        // var_dump($Values);
    
                        $Condition = array('id_commentaire' => $_POST['id_commentaire']);
    
                        $Success = $NewConnection->update($CommentsTableName, $Condition, $Values);
    
                        if ($Success) {
                            header("Location: " . $ArticlePageRedirection . '?edit=true&id_video=' . $_POST['id_video'] . '#Commentaires');
                            die();
                        }
                    }
                    else
                    {
                        header("Location: " . $NotAllowedRedirection . '?edit=failed&id_video=' . $_POST['id_video'] . '#Commentaires');
                        die();
                    }

                    break;

                case 'DeleteComment':
                    require_once('./components/commons.php');
                    session_start();

                    if (CanEditComments($_SESSION['UserRole']))
                    {
                        $UpdateFieldCondition = array('id_commentaire' => $_POST['id_commentaire']);

                        $Success = $NewConnection->delete($CommentsTableName, $UpdateFieldCondition);
    
                        if ($Success) {
                            header("Location: " . $ArticlePageRedirection . '?edit=true&id_video=' . $_POST['id_video'] . '#Commentaires');
                            die();
                        }
                    }
                    else
                    {
                        header("Location: " . $NotAllowedRedirection . '?edit=failed&id_video=' . $_POST['id_video'] . '#Commentaires');
                        die();
                    }

                    break;

                case 'UploadVideo':

                    if (isset($_FILES) && $_FILES)
                    {
                        // var_dump($_FILES);
                        // var_dump($_POST);

                        $FolderName = './videos/' ;

                        $LocalTempName = $_FILES['video']['tmp_name'];
                        $DestinationName = $FolderName . $_FILES['video']['name'] ;
                        var_dump($LocalTempName);
                        var_dump($DestinationName);

                        if ((file_exists( $FolderName ) && is_dir( $FolderName )) || mkdir($FolderName))
                        {
                            if (!file_exists($DestinationName))
                            {
                                move_uploaded_file($LocalTempName, $DestinationName);
                            }
                        }

                        // We should only store the filename below
                        $_POST['video'] = $_FILES['video']['name'];
                    }

                    $Values = array(
                        'path' => $_POST['video']
                    );

                    $Condition = array('id_video' => $_POST['id_video']);

                    $Success = $NewConnection->update($ArticleTableName, $Condition, $Values);

                    var_dump($Success);

                    header("Location: " . 'video.php?edit=true&id_video=' .  $_POST['id_video']);
                    die();
                    break;

                case 'UpdateVideoDescription':

                    $Values = array(
                        'resume' => $_POST['resume']
                    );

                    $Condition = array('id_video' => $_POST['id_video']);

                    $Success = $NewConnection->update($ArticleTableName, $Condition, $Values);

                    die();

                    break;
                case 'UpdateArticleField':

                    //'id_video' => $_POST['id_video']
                    $Condition = '(`id_video` = "' . $_POST['id_video'] . '")';
                    $CurrentCategorySub = $NewConnection->select('article', 'sous_categorie', $Condition);
                    $CurrentCategorySub = $CurrentCategorySub ? $CurrentCategorySub[0]['sous_categorie'] : '';

                    if (isset($_POST['sous_categorie']) )
                    {
                        $NewCategorySub = strtolower($_POST['sous_categorie']);

                        if ($NewCategorySub != $CurrentCategorySub)
                        {
                            require_once('./components/commons.php');

                            $Source = './images/' . $CurrentCategorySub;
                            $Destination = './images/' . $NewCategorySub;

                            CopyFolder($Source, $Destination);
    
                            //TODO: I'm wondering: could there be two articles storing in same folder (sub-category)
                            //technically no, but there's those articles that have bad subcategory, like China instead of a city Beijing

                            //if the rest of the code uses the sub categorie to point to folder we got nothing else to do,
                            //except delete source folder
                            //TODO: I'm not yet confident to uncomment that, but the function works
                            // DeleteFolder($Source);
                        }
                    }

                    else if (isset($_FILES) && $_FILES)
                    {
                        // var_dump($_FILES);
                        // var_dump($_POST);

                        $CategoryFolderName = './images/' . $CurrentCategorySub;
                        $CurrentCategorySubFolder = $CurrentCategorySub ? $CurrentCategorySub . '/' : '';

                        $LocalTempName = $_FILES[$_POST['Column']]['tmp_name'];

                        $DestinationName = './images/' . $CurrentCategorySubFolder . $_FILES[$_POST['Column']]['name'] ;
                        // var_dump($LocalTempName);
                        // var_dump($DestinationName);

                        if ((file_exists( $CategoryFolderName ) && is_dir( $CategoryFolderName )) || mkdir($CategoryFolderName))
                        {
                            if (!file_exists($DestinationName))
                            {
                                move_uploaded_file($LocalTempName, $DestinationName);
                            }
                        }

                        // We should only store the filename below
                        // $_POST[$_POST['Column']] = $DestinationName;
                        $_POST[$_POST['Column']] = $_FILES[$_POST['Column']]['name'];
                    }

                    $Values = array(
                        $_POST['Column'] => $_POST[$_POST['Column']]
                    );

                    $Condition = array('id_video' => $_POST['id_video']);

                    $Success = $NewConnection->update($ArticleTableName, $Condition, $Values);

                    die();
                    break;

                case 'UpdateProfile':
                    $Values = array(
                        // 'email' => $_POST['email'],
                        'name' => $_POST['name']
                    );

                    $Condition = array('id_user' => $_POST['id_user']);

                    $Success = $NewConnection->update($UsersTableName, $Condition, $Values);

                    if ($Success)
                    {
                        session_start();

                        // $_SESSION['CurrentUser'] = $_POST['email'];
                        $_SESSION['CurrentUserName'] = $_POST['name'];

                        header("Location: " . './profile.php?id_profile=' . $_POST['id_user']);
                        die();
                    }

                    // die();
                    break;
                    
                case 'CheckSession':
                    session_start();

                    $ResponseCode = isset($_SESSION['crsf_token']) ? 200 : 404;

                    header('Location: login.php', true, $ResponseCode);
                    die();
                    break;
    
                default:
                    # code...
                    break;
            }
        }
        else if (isset($_GET['Intention']))
        {
            switch ($_GET['Intention']) {

                default:
                    break;
            }
        }
        else if (isset($_PUT['Intention']))
        {
            switch ($_PUT['Intention']) {
                default:
                    break;
            }
        }
    ?>

</body>
</html>
