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

        //TODO: working through that, I think 'method chaining' would be a perfect interface, as in:
        //$NewConnection.prepare().delete().fromtable("User").where(array( "email" => "example@local") ).execute();

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

        /** Original select was having a entry point at the WHERE clause
         * I think below can give us more control on the condition field
         * ConditionField expects a multidimentional array containing an operations and a pair of operands
         */
        public function select_safe($Table, $Column, $ConditionField = 1)
        {
            try {
                // $ConditionField = array(
                //     array("=" => array("id_article" => "1")),
                //     array("<>" => array("sous_categorie" => "not_that_one")),
                //     array("LIKE" => array("content" => "%keyword%"))
                // );
                $TypeOfCondition = gettype($ConditionField);
                switch($TypeOfCondition)
                {
                    case 'array':
                    case 'object':
                        // What a terrible language: so much words for such a simple thing
                        $ConditionAsString = "";
                        foreach ($ConditionField as $Index => $EachConditionsPair) {
                            foreach ($EachConditionsPair as $EachOperation => $EachPair) {
                                foreach($EachPair as $EachKey => $EachValue) {
                                    $ConditionAsString .= "(`$Table`.`" . $EachKey . '` ' . $EachOperation . ' "' . $EachValue . '") AND ';
                                    break; //there should only be one pair per operations
                                }
                            }
                        }
                        $ConditionField = rtrim($ConditionAsString, ' AND ');
                        var_dump($ConditionField);
                        break;
                    default:
                    case 'boolean':
                    case 'integer':
                    case 'string':
                    case 'double':
                        break;
                    case 'NULL':
                        $ConditionField = 1;
                        break;
                }

                // NOTE: here, we try to support both valid name, and regex expression
                $Column = (@preg_match($Column, null) === false) ? $Column : ('`' . $Column . '`');
                // var_dump($Column);
                $SQLQueryString = "SELECT $Column FROM `$Table` WHERE $ConditionField";

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        public function select_full_article_all($Condition = 1)
        {
            try {
                $SQLQueryString = "SELECT *
                FROM `article`
                INNER JOIN `categorie` ON `article`.`categorie` = `categorie`.`id_categorie`
                WHERE $Condition ;
                ";

                // var_dump($SQLQueryString);

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        public function select_full_article($ArticleID)
        {
            try {
                $SQLQueryString = "SELECT *
                FROM `article`
                INNER JOIN `categorie` ON `article`.`categorie` = `categorie`.`id_categorie`
                WHERE `article`.`id_article` = '$ArticleID' ;
                ";

                // var_dump($SQLQueryString);

                $Result = $this->Connection->query($SQLQueryString);

                return $Result->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erreur: " . $e->getMessage();

                return false;
            }
        }

        //TODO: update the name on database, so we can include more of categorie's fields without conflict
        /**Returns an associative array of the results, or false on error */
        public function select_article($ConditionField)
        {
            try {
                $SQLQueryString = "SELECT `article`.`id_article`,`article`.`categorie`,`article`.`sous_categorie`, `article`.`date`, `article`.`titre`, `article`.`resume`, `article`.`photo_principale`, `categorie`.`nom`
                FROM `article`
                INNER JOIN `categorie` ON `categorie`.`id_categorie` = `article`.`categorie`
                WHERE `article`.`categorie` = $ConditionField ;
                ";


                // var_dump($SQLQueryString);

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
                $SQLQueryString = "SELECT `commentaire`.`id_commentaire`, `commentaire`.`date`, `commentaire`.`contenu`, `utilisateur`.`nom`
                FROM `commentaire`
                INNER JOIN `article` ON `article`.`id_article` = `commentaire`.`id_article`
                INNER JOIN `utilisateur` ON `utilisateur`.`id_utilisateur` = `commentaire`.`id_utilisateur`
                WHERE `commentaire`.`id_article` = $ConditionField ;
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
                $SQLQueryString = "INSERT INTO $Table (<?>) VALUES (<!>) ON DUPLICATE KEY UPDATE `id_utilisateur` = LAST_INSERT_ID(`id_utilisateur`), `$UpdateKey` = '$UpdateValue'";
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
                //DELETE FROM `utilisateur` WHERE `utilisateur`.`idUser` = 36
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
