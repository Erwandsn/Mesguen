<?php
session_start();

if($_SESSION['emplCat']=="Exploitant" OR $_SESSION['emplCat']=="Chauffeur")
{
	require 'utilitaires/connection/connection_mysql.php';
	mysql_set_charset("UTF8");

	if(isset($_POST['etpIdSup']) AND isset($_POST['trnNumSup']))
	{
		$etpId=$_POST['etpIdSup'];
		$trnNum=$_POST['trnNumSup'];

		$sqlSupprimerEtape="DELETE FROM 
								etape
							WHERE
								etpId=".$etpId."
								AND
								trnNum=".$trnNum;
		mysql_query($sqlSupprimerEtape);

		$_SESSION['trnNumInfo']=$trnNum;

		echo "<meta http-equiv='refresh' content='0;url=AC12.php?message=<font color=green>Etape supprimée</font>'>";
	}

	if(isset($_POST['maj']))
	{
		$trnDepChf=$_POST['trnDepChf'];
		$trnDepChf=date_create_from_format('d/m/Y', $trnDepChf);
		$trnDepChf=date_format($trnDepChf, 'Y-m-d H:i:s');
		$chauffeur=$_POST['chauffeur'];
		$vehicule=$_POST['vehicule'];
		$commentaire=$_POST['commentaire'];
		$trnNum=$_POST['trnNumInfo'];

		$sqlUpdate="UPDATE
						tournee
					SET
						trnDepChf='".$trnDepChf."',
						chfId=".$chauffeur.",
						vehMat='".$vehicule."',
						trnCommentaire='".$commentaire."'
					WHERE
						trnNum=".$trnNum;
		mysql_query($sqlUpdate);
	}

	if(isset($_POST['creer']))
	{
		if($_POST['chauffeur']!="NAN" AND $_POST['vehicule']!="NAN" AND $_POST['trnDepChf']!="")
		{
			$trnDepChf=$_POST['trnDepChf'];
			$trnDepChf=date_create_from_format('d/m/Y', $trnDepChf);
			$trnDepChf=date_format($trnDepChf, 'Y-m-d H:i:s');
			$chauffeur=$_POST['chauffeur'];
			$vehicule=$_POST['vehicule'];
			$sqlControl=   "SELECT
								trnNum
							FROM
								tournee
							WHERE
								trndepchf='".$trnDepChf."'
								AND
								chfId='".$chauffeur."'
								AND
								vehMat='".$vehicule."'";
			if(compteSQL($connexion, $sqlControl)==0)
			{
				if(isset($_POST['commentaire']))
				{
					$commentaire=$_POST['commentaire'];
					$sqlInsert="INSERT INTO tournee
									(trndepchf,
									chfId,
									vehMat,
									trnCommentaire)
								VALUES
									('".$trnDepChf."',
									'".$chauffeur."',
									'".$vehicule."',
									'".$commentaire."')";
				}
				else
				{
					$sqlInsert="INSERT INTO tournee
									(trndepchf,
									chfId,
									vehMat)
								VALUES
									('".$trnDepChf."',
									'".$chauffeur."',
									'".$vehicule."')";
				}
				$result=mysql_query($sqlInsert);
				$result=tableSQL($connexion, $sqlControl);

				$_SESSION['trnNumInfo']=$result[0]['trnNum'];

				header("location:AC12.php");
			}
			else
			{
				echo "<meta http-equiv='refresh' content='0;url=AC12.php?message=<font color=red>Tournée déjà éxistante</font>'>";
			}
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0;url=AC12.php?message=<font color=red>Données manquantes</font>'>";
		}
	}
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8" />
	        <title>Tournée</title>
	        <link rel="stylesheet" type="text/css" href="style/styleorgat.css"/>
	        <script type="text/javascript" src="javascript/javascript.js"></script>
		</head>
		<body>
			<?php
			if(isset($_POST['trnNumInfo']) OR isset($_SESSION['trnNumInfo']))
			{
				if(isset($_POST['trnNumInfo']))
				{
					$trnNum=$_POST['trnNumInfo'];
					$_SESSION['trnNumInfo']=$trnNum;
				}
				else
				{
					$trnNum=$_SESSION['trnNumInfo'];
					if(isset($_SESSION['etpIdInfo']))
					{
						unset($_SESSION['etpIdInfo']);
					}
				}
				$sqlTournee=   "SELECT
									chfId,
									emplNom,
									emplPrenom,
									vehicule.vehMat,
									trnDepChf,
									trnCommentaire
								FROM
									employe,
									vehicule,
									tournee
								WHERE
									emplId=chfId
									AND
									vehicule.vehMat=tournee.vehMat
									AND
									trnNum=".$trnNum;
				$sqlTournee=tableSQL($connexion, $sqlTournee);
				?>
				<h3 id="header_Organiser_tournee">AC12 - Organiser les tournées - Liste des étapes de la tournée n° <?php echo $trnNum; ?></h3>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="form_tournee">
					<div id="label_float">
						<label for="trnDepChf">Date :</label>
						<br/>
						<br/>
						<label class='chauffeur'>Chauffeur :</label>
						<br/>
						<br/>
						<label for="vehicule">Vehicule :</label>
						<br/>
						<br/>
						<label for="trnDepChfHor">Pris en charge le :</label>
						<br/>
						<br/>
						<label for="commentaire">Commentaire :</label>
					</div>
					<div id="input_float">
						<input required name="trnDepChf" id="trnDepChf" type="text" value="<?php echo date("d/m/Y", strtotime($sqlTournee[0]['trnDepChf'])); ?>"/>
						<div id="calendrierTrnDepChf"></div>
						<script type="text/javascript">
							calInit("calendrierTrnDepChf", "", "trnDepChf", "jsCalendar", "day", "selectedDay");
						</script>
						<br/>
						<div class="styled_select">
							<select required name="chauffeur">
								<?php
								$sqlChauffeurs="SELECT
													emplId,
													emplNom,
													emplPrenom
												FROM
													employe
												ORDER BY
													emplNom,
													emplPrenom
												ASC";
								$sqlChauffeurs=mysql_query($sqlChauffeurs);

								while($donnees=mysql_fetch_array($sqlChauffeurs, MYSQL_BOTH))
								{
									if($sqlTournee[0]['chfId']==$donnees['emplId'])
									{
										?>
										<option value="<?php echo $donnees['emplId']; ?>" selected="true"><?php echo $donnees['emplNom'].' '.$donnees['emplPrenom']; ?></option>
										<?php
									}
									else
									{
										?>
										<option value="<?php echo $donnees['emplId']; ?>"><?php echo $donnees['emplNom'].' '.$donnees['emplPrenom']; ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<br />
						<div class="styled_select">
							<select required name="vehicule">
								<?php
								$sqlVehicules= "SELECT
													vehMat
												FROM
													vehicule
												ORDER BY
													vehMat
												ASC";
								$sqlVehicules=mysql_query($sqlVehicules);

								while($donnees=mysql_fetch_array($sqlVehicules, MYSQL_BOTH))
								{
									if($sqlTournee[0]['vehMat']==$donnees['vehMat'])
									{
										?>
										<option value="<?php echo $donnees['vehMat']; ?>" selected="true"><?php echo $donnees['vehMat']; ?></option>
										<?php
									}
									else
									{
										?>
										<option value="<?php echo $donnees['vehMat']; ?>"><?php echo $donnees['vehMat']; ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<br />
						<input name="trnDepChfHor" type="text" value="<?php echo date("d/m/y H:i", strtotime($sqlTournee[0]['trnDepChf'])); ?>" disabled="disabled"/>
						<br />
						<br />
						<textarea name="commentaire" type="text"><?php echo $sqlTournee[0]['trnCommentaire']; ?></textarea>
					</div>
					<div id="valid_float">
						<input name="trnNumInfo" type="hidden" value="<?php echo $trnNum; ?>"/>
						<input class="bouton_global" id="valider" name="maj" type="submit" value="Valider"/>
						<input class="bouton_global" id="retour" type="button" onClick="location='AC11.php'" value="Retour"/>
					</div>
				</form>
				<div class="separateur_vertical">
				</div>
				<div id="etapes_tournee">
					<?php
					$sqlEtapes="SELECT
									etpId,
									comNom,
									lieuNom,
									trnNum
								FROM
									commune,
									lieu,
									etape
								WHERE
									commune.comId=lieu.comId
									AND
									lieu.lieuId=etape.lieuId
									AND
									trnNum=".$trnNum."
								ORDER BY
									etpRDV
								ASC";
					$sqlEtapes=mysql_query($sqlEtapes);
					?>
					<table id="etapes_tournee">
						<tr>
							<th>
								Ordre
							</th>
							<th>
								Etapes
							</th>
						</tr>
						<?php
						$compteur=1;
						while($donnees=mysql_fetch_array($sqlEtapes, MYSQL_BOTH))
						{
							?>
							<tr>
								<td>
									<?php
									echo $compteur++;
									?>
								</td>
								<td>
									<?php
									echo $donnees['lieuNom']." ".$donnees['comNom'];
									?>
								</td>
								<td>
									<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
										<input name="etpIdSup" type="hidden" value="<?php echo $donnees['etpId']; ?>"/>
										<input name="trnNumSup" type="hidden" value="<?php echo $donnees['trnNum']; ?>"/>
										<input name="suppr_bout_<?php echo $donnees['etpId']; ?>" id="suppr_bout_<?php echo $donnees['etpId']; ?>" class="suppr_form" type="submit"/>
									</form>
								</td>
								<td>
									<form action="AC13.php" method="POST">
										<input name="etpIdInfo" type="hidden" value="<?php echo $donnees['etpId']; ?>"/>
										<input name="trnNumInfo" type="hidden" value="<?php echo $donnees['trnNum']; ?>"/>
										<input name="modif_bout_<?php echo $donnees['etpId']; ?>" id="modif_bout_<?php echo $donnees['etpId']; ?>" class="modif_form" type="submit"/>
									</form>
								</td>
							</tr>
							<?php
						}

						if(isset($_GET['message']))
						{
							echo utf8_decode($_GET['message']);
						}
						?>
						<tr>
							<td>
								<form action="AC13.php" method="POST">
									<input type="hidden" name="trnNumInfo" value="<?php echo $trnNum; ?>"/>
									<input class="bouton_global" id="ajouter" type="submit" value="Ajouter"/>
								</form>
							</td>
						</tr>
					</table>
				</div>
				<?php
				}
				else
				{
					?>
					<div>
					<h3>AC12 - Organiser les tournées - Ajouter une tournée</h3>
					</div>
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
						<br/>
						<div id="label_float">
							<label for='chauffeur'>Chauffeur :</label>
							<br/>
							<br/>
							<label for="vehicule">Vehicule :</label>
							<br/>
							<br/>
							<label for="trnDepChf">Pris en charge le :</label>
							<br/>
							<br/>
							<label for="commentaire">Commentaire :</label>
						</div>
						<div id="input_float">
							<select name='chauffeur'>
								<option selected value="NAN">Aucun chauffeur</option>
								<?php
								$sqlChauffeur= "SELECT
													emplId,
													emplNom,
													emplPrenom
												FROM
													employe
												WHERE
													emplcat='chauffeur'";
								$result=mysql_query($sqlChauffeur);
								while($row=mysql_fetch_array($result, MYSQL_NUM))
								{
									echo "<option value='$row[0]'>".$row[1]." ".$row[2]."</option>";
								}
								?>
							</select>
							<br/>
							<br/>
							<select name="vehicule">
								<option selected value="NAN">Aucun véhicule</option>
								<?php
								$sqlPlaque="SELECT
												vehMat
											FROM
												vehicule";
								$result=mysql_query($sqlPlaque);
								while($row=mysql_fetch_array($result, MYSQL_NUM))
								{
									echo "<option value='".$row[0]."'>".$row[0]."</option>";
								}
								?>
							</select>
							<br/>
							<br/>
							<input type="text" name="trnDepChf" id="trnDepChf" placeholder="JJ/MM/AAAA HH:MM:SS"/>
							<div id="calendrierTrnDepChf"></div>
							<script type="text/javascript">
								calInit("calendrierTrnDepChf", "", "trnDepChf", "jsCalendar", "day", "selectedDay");
							</script>
							<br/>
							<textarea name="commentaire"></textarea>
						</div>
						<div id="valid_float">
							<input class="bouton_global" id="valider" type="submit" name="creer" value="Valider"/>
							<input class="bouton_global" id="retour" type="button" onClick="location='AC11.php'" value="Retour"/>
						</div>
						<?php
						if(isset($_GET['message']))
						{
							echo utf8_decode($_GET['message']);
						}
						?>
					</form>
				</div>
				<?php
			}
			?>
		</body>
	</html>
	<?php
}
else
{
	header("location:accueil.php");
}
?>