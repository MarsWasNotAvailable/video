<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Script: Import</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        /* This is an ebauche : on how to automate the import of the database across users */

        $DatabaseName = "viaje";
        $Redirection = "index.php";
        $NotAllowedRedirection = './index.php';

        include("./components/connexion.php");
        $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");
        $NewConnection->execute_file('./viaje.sql');

        // $ScriptCreateDatabase = file_get_contents('./viaje.sql');
        // $mysqli = new mysqli("localhost", "root", "", $DatabaseName);
        // $mysqli->multi_query($ScriptCreateDatabase);
    ?>

</body>
</html>
