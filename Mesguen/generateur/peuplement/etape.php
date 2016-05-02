<?php
function ajoutEtape()
{
	require('divers/connectionPDOSelect.php');

	$requete=$bdd->query(  "SELECT 
								trnNum,
								trnDepChf
							FROM
								tournee");

	$tournees=$requete->fetchAll();

	$requete->closeCursor();
	$requete=$bdd->query(  "SELECT 
								lieuId
							FROM
								lieu");

	$lieux=$requete->fetchAll();

	$requete->closeCursor();

	$requete=$bdd->prepare("SELECT 
								etpId
							FROM
								etape
							WHERE
								trnNum=:tournee
								AND
								lieuId=:lieu
								AND
								etpRDV=:rdv");

	$requeteSeconde=$bdd->prepare( "INSERT INTO etape
										(trnNum,
										lieuId,
										etpRDV,
										etpRDVMin,
										etpRDVMax,
										etpNbPalLiv,
										etpNbPalLivEur,
										etpNbPalChg,
										etpNbPalChgEur,
										etpEtat)
									VALUES
										(:tournee,
										:lieu,
										:rdv,
										:rdvMin,
										:rdvMax,
										:liv,
										:livEur,
										:chg,
										:chgEur,
										:etat)");

	$etat=0;

	for($i=0; $i<750; $i++)
	{
		$ligneTournee=rand(0, sizeof($tournees)-1);
		echo $tournee=$tournees[$ligneTournee]['trnNum'];

		echo $lieu=$lieux[rand(0, sizeof($lieux)-1)]['lieuId'];

		$rdv=$tournees[$ligneTournee]['trnDepChf'];
		$rdv=new DateTime($rdv);
		$rdv->modify("+".rand(0,400000)." seconde");
		echo $rdv=date_format($rdv, 'Y-m-d H:i:s');

		$rdvMin=new DateTime($rdv);
		$rdvMax=new DateTime($rdv);
		$rdvMin->modify("-".rand(0,10800)." seconde");
		$rdvMax->modify("+".rand(0,10800)." seconde");
		echo $rdvMin=date_format($rdvMin,'Y-m-d H:i:s');
		echo $rdvMax=date_format($rdvMax,'Y-m-d H:i:s');

		echo $palLiv=rand(0, 32);
		echo $palLivEur=rand(0, $palLiv);
		echo $palChg=rand(0, $palLiv);
		echo $palChgEur=rand(0, $palChg);

		echo $etat;
		
		$requete->execute(array("tournee"=>$tournee, "lieu"=>$lieu, "rdv"=>$rdv));

		if($requete->fetch()==false)
		{
			$requeteSeconde->execute(array("tournee"=>$tournee, "lieu"=>$lieu, "rdv"=>$rdv, "rdvMin"=>$rdvMin, "rdvMax"=>$rdvMax, "liv"=>$palLiv, "livEur"=>$palLivEur, "chg"=>$palChg, "chgEur"=>$palChgEur, "etat"=>$etat));
		}
		else
		{
			$i--;
		}

		echo "<br />";
	}

	$requete->closeCursor();
	$requeteSeconde->closeCursor();
}
?>