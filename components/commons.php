<?php 
    function CanEditArticles($UserRole) {
        switch (strtolower($UserRole))
        {
            case 'admin':
            case 'redactor':
                return true;
            // case 'moderator':
            // case 'guest':
            default:
                break;
        }
        return false;
    }

    function CanEditComments($UserRole) {
        switch (strtolower($UserRole))
        {
            case 'admin':
            case 'moderator':
                return true;
            // case 'guest':
            default:
                break;
        }
        return false;
    }

    function boolalpha($Boolean)
    {
        // return $Boolean ? 'true' : 'false';
        return var_export($Boolean, true);
    }

    function GetImagePath($Filename, $CategorySub)
    {
        return './images/' . strtolower($CategorySub) . '/' . $Filename;
    }

    // recursive
    function CopyFolder( $Source, $Destination )
    {
        $SourceDirectory = opendir($Source); 
        if (( file_exists( $Destination ) && is_dir( $Destination )) || mkdir($Destination))
        {
            // Loop through the files in source directory
            while( $EachFile = readdir($SourceDirectory) ) {
                $EachPath = $Source . '/' . $EachFile;
                $NewDestination = $Destination . '/' . $EachFile;
            
                switch ($EachFile)
                {
                    case '.':
                    case '..':
                        break;
                    default:
                        if ( is_dir($EachPath) )
                        {
                            CopyFolder($EachPath, $NewDestination);
                        }
                        else {
                            copy($EachPath, $NewDestination); 
                        }
                        break;
                }
            }
        }
        
        closedir($SourceDirectory);
    }

    function DeleteFolder ($Source)
    {
        array_map('unlink', glob("$Source/*"));
        rmdir($Source);
    }
?>
