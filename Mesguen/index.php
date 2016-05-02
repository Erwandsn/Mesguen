<?php
session_start ();

require 'utilitaires/connection/connection_mysql.php';

if(isset($_POST['loggin']) AND isset($_POST['mdpL']))
{
	$loggin=trim($_POST['loggin']);
	$mdpL=trim($_POST['mdpL']);
	if($loggin!="" AND $mdpL!="")
	{
		$verifCompte=  "SELECT 
							emplLoggin,
							emplMdp
						FROM
							employe
						WHERE
							emplLoggin='".$loggin."'
							AND
							emplMdp='".$mdpL."'";
		if(compteSQL($connexion, $verifCompte)!=0)
		{
			$compteUtil=   "SELECT 
								emplId,
								emplNom,
								emplPrenom,
								emplCat
							FROM
								employe
							WHERE
								emplLoggin='".$loggin."'
								AND
								emplMdp='".$mdpL."'";
			$compteUtil=tableSQL($connexion, $compteUtil);

			$_SESSION['emplId']=$compteUtil[0]['emplId'];
			$_SESSION['emplNom']=$compteUtil[0]['emplNom'];
			$_SESSION['emplPrenom']=$compteUtil[0]['emplPrenom'];
			$_SESSION['emplCat']=$compteUtil[0]['emplCat'];

			header("location:accueil.php");
		}

		echo "<meta http-equiv='refresh' content='0;url=index.php?message=<font color=red>Identifiant ou mot de passe incorrects</font>'>";
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Mesguen</title>
		<link rel='stylesheet' type='text/css' href='style/style.css'/>
	</head>
	<body>
		<form class='connexion' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		
		<fieldset class='connexion'>
			<legend>Connexion</legend>
				<label class='identifiant'>Identifiant</label>
					<input name="loggin" id="loggin" type="text" class='identifiant' required/>
			<br />
				<label class='mdp'>Mot de passe</label>
					<input name="mdpL" id="mdpL" type="password" class='mdp' required/>
			<br />
			<input name="valid" class='bouton5' id="valid" value="Se connecter" type="submit"/>
			<br />
			<?php
			if(isset($_GET['message']))
			{
				echo $_GET['message'];
			}
			?>
		</fieldset>
		</form>
		<img class='mesguen' src='images/Mesguen.jpg'/>
	</body>
</html>