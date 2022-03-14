<?php

	session_start();

	require_once('src/option.php');


	if(isset($_SESSION['connect'])){
		header('location: index.php');
		exit();
	}

	if(!empty($_POST['email']) && !empty($_POST['mdp']) && !empty($_POST['mdp_two'])){
		require_once('src/connection.php');

		$email = htmlspecialchars($_POST['email']);
		$mdp = htmlspecialchars($_POST['mdp']);
		$mdpTwo = htmlspecialchars($_POST['mdp_two']);
		
		if($mdp != $mdpTwo){
			header('location: inscription.php?error=1&message=Les mots de passes ne correspondent pas.');
			exit();
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header('location: inscription.php?error=1&message=Votre adresse email est invalide');
			exit();
		}

		$requete = $bdd->prepare('SELECT COUNT(*) AS numberEmail FROM user WHERE email = ?');
		$requete->execute([$email]);
		while($resultat = $requete->fetch()){
			if($resultat['numberEmail'] != 0){
				header('location: inscription.php?error=1&message=Cette adresse est déjà utilisée.');
				exit();
			}
		}
		
		$mdp = 'mqd'.sha1($mdp.'223').'45fg';

		$secret = sha1($email).time();
		$secret = sha1($secret).time();
		
		$envois = $bdd->prepare('INSERT INTO user(email, mdp, secret) VALUES (?, ?, ?)');
		$envois->execute([$email, $mdp, $secret]);

		header('location: inscription.php?success=1&message=Votre compte à bien été créé.');
		exit();
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php if(isset($_GET['error']) && isset($_GET['message'])) {
				echo '<div class="alert error">'.$_GET['message'].'</div>';
			}else if(isset($_GET['success']) && isset($_GET['message'])) {
				echo'<div class="alert success">'.$_GET['message'].'<a href="index.php"> Se connecter</a></div>';
			}?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="mdp" placeholder="Mot de passe" required />
				<input type="password" name="mdp_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>
			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>