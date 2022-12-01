<?php 

   
    function connect()
    {
        require_once 'pdoConfig.php';
        try
        {
            $PDO = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
           // echo "conectou redondin paizãp";
            return $PDO;
        }
        catch (PDOException $pe)
        {
            die ("num foi não kkkkkikk. Erro: " . $pe->getMessage());
        }
    }

?>