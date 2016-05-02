<?php
function ajoutVehicule()
{
	require('divers/connectionPDOSelect.php');

	$camions=file('ressources/camion.txt');

	$requete=$bdd->prepare("SELECT 
								vehMat
							FROM
								vehicule
							WHERE
								vehMat=:vehMat");

	$requeteSeconde=$bdd->prepare( "INSERT INTO vehicule
										(vehMat,
										vehMarque)
									VALUES
										(:vehMat,
										:vehMarque)");

	for($i=0; $i<100; $i++)
	{
		$immatriculation="";

		for($j=0; $j<2; $j++)
		{
			$immatriculation.=chr(rand(65, 90));
		}

		$immatriculation.="-";

		for($j=0; $j<3; $j++)
		{
			$immatriculation.=rand(0, 9);
		}

		$immatriculation.="-";

		for($j=0; $j<2; $j++)
		{
			$immatriculation.=chr(rand(65, 90));
		}

		echo $immatriculation;

		$camion="Camion ";
		echo $camion.=trim($camions[rand(0, sizeof($camions)-1)]);

		echo "<br />";

		$requete->execute(array("vehMat"=>$immatriculation));

		if($requete->fetch()==false)
		{
			$requeteSeconde->execute(array("vehMat"=>$immatriculation, "vehMarque"=>$camion));
		}
		else
		{
			$i--;
		}
	}

	$requete->closeCursor();
	$requeteSeconde->closeCursor();
}
?>