<?php

	session_start();

	require_once('src/option.php');

	if(!empty($_POST['email']) && !empty($_POST['mdp'])){
		require_once('src/connection.php');

		$email = htmlspecialchars($_POST['email']);
		$mdp = htmlspecialchars($_POST['mdp']);

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header('location: index.php?error=1&message=Votre adresse email est invalide');
			exit();
		}

		$mdp = 'mqd'.sha1($mdp.'223').'45fg';

		$requete = $bdd->prepare('SELECT COUNT(*) AS numberEmail FROM user WHERE email = ?');
		$requete->execute([$email]);
		while($resultat = $requete->fetch()){
			if($resultat['numberEmail'] != 1){
				header('location: index.php?error=1&message=Immpossible de se connecter.');
				exit();
			}
		}

		$envoi = $bdd->prepare('SELECT * FROM user WHERE email = ?');
		$envoi->execute([$email]);
		while($user = $envoi->fetch()){
			if($user['mdp'] == $mdp) {
				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];

				if(isset($_POST['auto'])){
					setcookie('auth', $user['secret'], time() + 365*24*3600, '/', null, false, true );
				}
				
				header('location: index.php?success=1');
				exit();

			}
			else {
				header('location: index.php?error=1&message=Immpossible de se connecter.');
				exit();
			}
		}
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

				<?php if(isset($_SESSION['connect'])) { ?>

					<h1>Bonjour !</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
					} ?>
					<p>Qu'allez-vous regarder aujourd'hui ?</p>
					<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>
					<h1>S'identifier</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="mdp" placeholder="Mot de passe" required />
						<button type="submit">S'identifier</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
				

					<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>