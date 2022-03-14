<?php
    
    if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])){
        require_once('src/connection.php');

        $secret = htmlspecialchars($_COOKIE['auth']);

        $req = $bdd->prepare('SELECT COUNT(*) AS numberSecret FROM user WHERE secret = ? ');
        $req->execute([$secret]);

        while($user = $req->fetch()) {
            if($user['numberSecret'] == 1) {
                $informations = $bdd->prepare('SELECT * FROM user WHERE secret = ? ');
                $informations->execute([$secret]);
                
                while($userInformations = $informations->fetch()) {
                    $_SESSION['connect'] = 1;
                    $_SESSION['email'] = $userInformations['email'];
                }
            } 
        }
    }