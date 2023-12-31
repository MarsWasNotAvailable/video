<?php
    class MaConnexion {
        private $DatabaseName;
        private $User = "root";
        private $Password ="";
        private $Host;
        private $Connection;

        public function __construct($NewDatabaseName, $NewUser, $NewPassword = "", $NewHost = "localhost")
        {
            $this->DatabaseName = $NewDatabaseName;
            $this->User = $NewUser;
            $this->Password = $NewPassword;
            $this->Host = $NewHost;

            try {
                $DataSourceName = "mysql:host=$this->Host;dbname=$this->DatabaseName;charset=utf8mb4";
                $this->Connection = new PDO($DataSourceName, $this->User, $this->Password);
                $this->Connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();
                var_dump($this);
            }
        }

        /**
         * The ConditionField is a filter to isolate a specific result
         * Returns an associative array of the results, or false on error */
        public function select($Table, $Column, $ConditionField = 1)
        {
            // $SQLQueryString = 'SELECT * FROM `users` WHERE (`mail` = "superuser@local" AND `password` = "pass")';
            // $SQLQueryString = "SELECT $Column FROM $Table WHERE 1";
            try {
                // NOTE: we cannot wrap Column in `` because it could be a regex like '*'
                // $SQLQueryString = "SELECT `$Column` FROM `$Table` WHERE $ConditionField";
                $SQLQueryString = "SELECT $Column FROM `$Table` WHERE $ConditionField";

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        public function select_video_with_extras($VideoID)
        {
            try {
                $SQLQueryString = "SELECT *
                FROM `videos`
                INNER JOIN `users` ON `users`.`id_user` = `videos`.`fk_user`
                WHERE `videos`.`id_video` = '$VideoID' ;
                ";

                // var_dump($SQLQueryString);

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        public function select_recent_videos()
        {
            // $SQLQueryString = 'SELECT * FROM `users` WHERE (`mail` = "superuser@local" AND `password` = "pass")';
            // $SQLQueryString = "SELECT $Column FROM $Table WHERE 1";
            try {
                // NOTE: we cannot wrap Column in `` because it could be a regex like '*'
                // $SQLQueryString = "SELECT `$Column` FROM `$Table` WHERE $ConditionField";
                $SQLQueryString = "SELECT * FROM `video`.`videos` WHERE `is_visible` = 1 ORDER BY `date` LIMIT 9";

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        /**Returns an associative array of the results, or false on error */
        public function select_comments($ConditionField)
        {
            try {
                $SQLQueryString = "SELECT `commentaire`.`id_commentaire`, `commentaire`.`date`, `commentaire`.`contenu`, `users`.`name`
                FROM `commentaire`
                INNER JOIN `videos` ON `videos`.`id_video` = `commentaire`.`fk_video`
                INNER JOIN `users` ON `users`.`id_user` = `commentaire`.`fk_user`
                WHERE `commentaire`.`fk_video` = $ConditionField ;
                ";

                // var_dump($SQLQueryString);

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        /**Returns the id of inserted row on sucessful insert, false on failure */
        public function insert($Table, $Values)
        {
            try {
                $ValueAsString = "";
                $KeyAsString = "";

                foreach ($Values as $EachColumn => $EachValue) {
                    // echo "$EachColumn => $EachValue";
                    $KeyAsString .= "`$EachColumn`, ";
                    // var_dump($EachValue);
                    $ValueAsString .= ($this->Connection->quote($EachValue) . ", ");
                }
                $KeyAsString = rtrim($KeyAsString, ', ');
                $ValueAsString = rtrim($ValueAsString, ', ');

                /* $SQLQueryString = "INSERT IGNORE INTO $Table (<?>) VALUES (<!>)"; */
                $SQLQueryString = "INSERT INTO $Table (<?>) VALUES (<!>)";
                $SQLQueryString = str_replace("<!>", $ValueAsString, str_replace("<?>", $KeyAsString, $SQLQueryString));

                $Result = $this->Connection->query($SQLQueryString);
                // return true;

                return $this->Connection->lastInsertId();

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        /** INSERT ON DUPLICATE UPDATE version */
        public function insert_update($Table, $Values, $UpdateField)
        {
            try {
                $UpdateKey = $UpdateField['Key'];
                $UpdateValue = $UpdateField['Value'];


                $ValueAsString = "";
                $KeyAsString = "";

                foreach ($Values as $EachColumn => $EachValue) {
                    // echo "$EachColumn => $EachValue";
                    $KeyAsString .= "`$EachColumn`, ";
                    $ValueAsString .= ($this->Connection->quote($EachValue) . ", ");
                }
                $KeyAsString = rtrim($KeyAsString, ', ');
                $ValueAsString = rtrim($ValueAsString, ', ');

                /* $SQLQueryString = "INSERT IGNORE INTO $Table (<?>) VALUES (<!>)"; */
                $SQLQueryString = "INSERT INTO $Table (<?>) VALUES (<!>) ON DUPLICATE KEY UPDATE `id_user` = LAST_INSERT_ID(`id_user`), `$UpdateKey` = '$UpdateValue'";
                $SQLQueryString = str_replace("<!>", $ValueAsString, str_replace("<?>", $KeyAsString, $SQLQueryString));

                var_dump($SQLQueryString);

                $Result = $this->Connection->query($SQLQueryString);
                // var_dump($Result);
                // return true;

                // TODO: We need to find a way to return the id in case of a failed update:
                return $this->Connection->lastInsertId();

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        /**Returns true on sucessful update */                
        public function update($Table, $ConditionField, $Values)
        {
            try {
                if (count($ConditionField) != 1)
                {
                    return false;
                }

                $ValueAsString = "";
                $KeyAsString = "";

                foreach ($Values as $EachColumn => $EachValue) {
                    $ValueAsString .= ("`$EachColumn` = " . $this->Connection->quote($EachValue) . ", ");
                }
                $ValueAsString = rtrim($ValueAsString, ', ');

                foreach ($ConditionField as $EachColumn => $EachValue) {
                    $KeyAsString .= ("`$EachColumn` = " . $this->Connection->quote($EachValue));
                }

                $SQLQueryString = "UPDATE $Table SET <?> WHERE <!>";
                $SQLQueryString = str_replace("<!>", $KeyAsString, str_replace("<?>", $ValueAsString, $SQLQueryString));

                $query = $this->Connection->prepare($SQLQueryString);
                $query->execute();

                return true;

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        /**Returns true on sucessful delete */
        public function delete($Table, $ConditionField)
        {
            try {
                //DELETE FROM `user` WHERE `user`.`idUser` = 36
                // $SQLQueryString = "DELETE FROM `$Table` WHERE `$Table`.`idUser` = 36";
                $SQLQueryString = "DELETE FROM `$Table` WHERE <?>";

                $ConditionAsString = "";
                foreach ($ConditionField as $EachColumn => $EachValue) {
                    $ConditionAsString .= ("`$EachColumn` = " . $this->Connection->quote($EachValue));
                }

                $SQLQueryString = str_replace("<?>", $ConditionAsString, $SQLQueryString);

                echo $SQLQueryString;

                $query = $this->Connection->prepare($SQLQueryString);
                $query->execute();

                return true;

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        public function execute_file($FilePath)
        {
            try {
                $ScriptCreateDatabase = file_get_contents($FilePath);
                $Statement = $this->Connection->prepare($ScriptCreateDatabase);

                return $Statement->execute();

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

    }

    // $NewConnection = new MaConnexion("liste_utilisateurs", "root", "", "localhost");
    // $NewConnection = new MaConnexion("products", "root", "", "localhost");
    // echo var_dump($NewConnection);

    // $Result = $NewConnection->select("utilisateur", "email");
    // $Result = $NewConnection->select("produit", "*");
    // echo var_dump($Result);

    // $Result = $NewConnection->__deprecated_insert("utilisateur", array("Doe", "Jane", rand(0, 10000) . "@domain", "20230101", null, "path/to/image.jpg"));
    // $Result = $NewConnection->insert("utilisateur", array(
    //     "NameLast" => "Doe", 
    //     "NameFirst" => "Jane",
    //     "Email" => (rand(0, 10000) . "@domain"),
    //     "Birthday" => "20230101",
    //     "idUser" => "NULL",
    //     "Image" => "path/to/image.jpg")
    // );


    // $UpdateFieldCondition = array( "Email" => "1070@domain" );

    // $UpdateValues = array(
    //     "NameLast" => "Yoka",
    //     "NameFirst" => "dahl",
    //     "Email" => "1070@domain",
    //     "Birthday" => "20230121",
    //     "Image" => "image13.jpg"
    // );

    // $Result = $NewConnection->update("utilisateur", $UpdateFieldCondition, $UpdateValues);

    // $UpdateFieldCondition = array( "Email" => "ol@dsfdsfsf" );

    // $Result = $NewConnection->delete("utilisateur", $UpdateFieldCondition);

?>
