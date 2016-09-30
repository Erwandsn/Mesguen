<?php
session_start();

if($_SESSION['emplCat']=="Exploitant")
{
	require 'utilitaires/connection/connection_mysql.php';
	//mysql_set_charset("UTF8");
	$connexion->exec("SET CHARACTER SET utf8");

	if(isset($_POST['maj']))
	{
		if($_POST['lieu']!="NAN" AND $_POST['etpRDV']!="")
		{
			$lieu=$_POST['lieu'];
			$etpRDV=$_POST['etpRDV'];
			$etpRDV=date_create_from_format('d/m/Y', $etpRDV);
			$etpRDV=date_format($etpRDV, 'Y-m-d H:i:s');
			$trnNum=$_POST['trnNumInfo'];
			$etpId=$_POST['etpIdInfo'];
			$commentaire=$_POST['commentaire'];

			if($_POST['etpRDVMin']=='')
			{
				$etpRDVMin='NULL';
			}
			else
			{
				$etpRDVMin=$_POST['etpRDVMin'];
				$etpRDVMin=date_create_from_format('d/m/Y', $etpRDVMin);
				$etpRDVMin=date_format($etpRDVMin, 'Y-m-d H:i:s');
				$etpRDVMin="'".$etpRDVMin."'";
			}
			if($_POST['etpRDVMax']=='')
			{
				$etpRDVMax='NULL';
			}
			else
			{
				$etpRDVMax=$_POST['etpRDVMax'];
				$etpRDVMax=date_create_from_format('d/m/Y', $etpRDVMax);
				$etpRDVMax=date_format($etpRDVMax, 'Y-m-d H:i:s');
				$etpRDVMax="'".$etpRDVMax."'";
			}

			$sqlUpdateEtape=   "UPDATE
									etape
								SET
									lieuId=".$lieu.",
									etpRDV='".$etpRDV."',
									etpCommentaire='".$commentaire."',
									etpRDVMin=".$etpRDVMin.",
									etpRDVMax=".$etpRDVMax."
								WHERE
									trnNum=".$trnNum."
									AND
									etpId=".$etpId;

			$_SESSION['trnNumInfo']=$trnNum;
			$_SESSION['etpIdInfo']=$etpId;

			$sqlUpdateEtape=executeSQL($sqlUpdateEtape);

			/*if($sqlUpdateEtape)
			{
				echo "<meta http-equiv='refresh' content='0;url=AC13.php?message=<font color=green>Étape modifiée</font>'>";
			}
			else
			{
				echo "<meta http-equiv='refresh' content='0;url=AC13.php?message=<font color=red>Une erreur est survenue</font>'>";
			}*/
		}
	}

	if(isset($_POST['creer']))
	{
		if($_POST['lieu']!="NAN" AND $_POST['etpRDV']!="")
		{
			$lieu=$_POST['lieu'];
			$etpRDV=$_POST['etpRDV'];
			$etpRDV=date_create_from_format('d/m/Y', $etpRDV);
			$etpRDV=date_format($etpRDV, 'Y-m-d H:i:s');
			$trnNum=$_POST['trnNumInfo'];
			$commentaire=$_POST['commentaire'];

			if($_POST['etpRDVMin']=='')
			{
				$etpRDVMin='NULL';
			}
			else
			{
				$etpRDVMin=$_POST['etpRDVMin'];
				$etpRDVMin=date_create_from_format('d/m/Y', $etpRDVMin);
				$etpRDVMin=date_format($etpRDVMin, 'Y-m-d H:i:s');
				$etpRDVMin="'".$etpRDVMin."'";
			}
			if($_POST['etpRDVMax']=='')
			{
				$etpRDVMax='NULL';
			}
			else
			{
				$etpRDVMax=$_POST['etpRDVMax'];
				$etpRDVMax=date_create_from_format('d/m/Y', $etpRDVMax);
				$etpRDVMax=date_format($etpRDVMax, 'Y-m-d H:i:s');
				$etpRDVMax="'".$etpRDVMax."'";
			}

			$sqlCreateEtape=   "INSERT INTO etape
									(trnNum,
									lieuId,
									etpRDV,
									etpRDVMin,
									etpRDVMax,
									etpCommentaire)
								VALUES
									(".$trnNum.",
									".$lieu.",
									'".$etpRDV."',
									".$etpRDVMin.",
									".$etpRDVMax.",
									'".$commentaire."')";
			$sqlCreateEtape=executeSQL($sqlCreateEtape);
			
			/*if($sqlCreateEtape)
			{
				$sqlEtapeReq=   "SELECT
									etpId
								FROM
									etape
								WHERE
									etpId=( SELECT
												MAX(etpId)
											FROM
												etape
											WHERE
												trnNum=".$trnNum.")";
				$sqlEtapeId=tableSQL($sqlEtapeReq);
				$_SESSION['etpIdInfo']=$sqlEtapeId['etpId'];
				echo "<meta http-equiv='refresh' content='0;url=AC13.php?message=<font color=green>Étape ajoutée</font>'>";
			}
			else
			{
				echo "<meta http-equiv='refresh' content='0;url=AC13.php?message=<font color=green>Une erreur est survenue</font>'>";
			}*/
		}
	}
	?>


	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="ISO-8859-1"/>
			<title>Étape</title>
			<link rel="stylesheet" type="text/css" href="style/styleac13.css"/>
			<script type="text/javascript">
				<?php require 'javascript/calendrier.js';
				require 'javascript/controle.js'; ?>
			</script>
		</head>
		<body>
			<?php
			if((isset($_POST['trnNumInfo']) AND isset($_POST['etpIdInfo'])) OR (isset($_SESSION['trnNumInfo']) AND isset($_SESSION['etpIdInfo'])))
			{
				if(isset($_POST['trnNumInfo']) AND isset($_POST['etpIdInfo']))
				{
					$trnNum=$_POST['trnNumInfo'];
					$etpId=$_POST['etpIdInfo'];
				}
				else
				{
					$trnNum=$_SESSION['trnNumInfo'];
					$etpId=$_SESSION['etpIdInfo'];
				}

				$_SESSION['trnNumInfo']=$trnNum;
				$_SESSION['etpIdInfo']=$etpId;

				$sqlEtape= "SELECT
								lieuId,
								etpRDV,
								etpRDVMax,
								etpRDVMin,
								etpCommentaire
							FROM
								etape
							WHERE
								trnNum=".$trnNum."
								AND
								etpId=".$etpId;
				$sqlEtape=tableSQL($sqlEtape);
				$sqlEtape=$sqlEtape[0];
				?>
				<div id="erreur"></div>
				<form action="AC13.php" method="POST" onSubmit="return isValidFormEtapeUpdate();">
					<div class="ajouter">
						<div class="titre">
							<?php
							?>
							<p>
								AC13-Organiser les tournées - Tournée <?php echo $trnNum." Étape ".$etpId;?>
							</p>
						</div>
						<div class="contenu">
							<label class="lieu" for="lieu">Lieu <sup>(</sup>*<sup>)</sup>:</label>
							<select required id="lieu" class="lieu" name="lieu">
								<?php 
								$sqlLieux= "SELECT
												lieuId,
												lieuNom,
												comNom
											FROM
												commune,
												lieu
											WHERE
												commune.comid=lieu.comid";
								$result=executeSQL($sqlLieux);
								$donnees=tableSQL($sqlLieux);
								foreach($donnees as $donnee)
								{
									if($sqlEtape['lieuId']==$donnees['lieuId'])
									{
										echo "<option selected='true' value='".$donnee['lieuId']."'>".$donnee['lieuNom']." ".$donnee['comNom']."</option>";
									}
									else
									{
										echo "<option value='".$donnee['lieuId']."'>".$donnee['lieuNom']." ".$donnee['comNom']."</option>";
									}
								}
								?>
							</select>
							<label class='rdventre'>Rendez vous entre :</label>
							<input type='text' class='rdventre' id="etpRDVMin" name='etpRDVMin' value="<?php if(isset($sqlEtape['etpRDVMin'])) echo date("d/m/Y", strtotime($sqlEtape['etpRDVMin'])); ?>" onKeyPress="return isDateKey(event);"/>
							<div id="calendrierEtpRDVMin"></div>
							<script type="text/javascript">
								calInit("calendrierEtpRDVMin", "", "etpRDVMin", "jsCalendar", "day", "selectedDay");
							</script>
							<label class='et'>et :</label>
							<input type='text' class='et' name='etpRDVMax' id="etpRDVMax" value="<?php if(isset($sqlEtape['etpRDVMax'])) echo date("d/m/Y", strtotime($sqlEtape['etpRDVMax'])); ?>" onKeyPress="return isDateKey(event);"/>
							<div id="calendrierEtpRDVMax"></div>
							<script type="text/javascript">
								calInit("calendrierEtpRDVMax", "", "etpRDVMax", "jsCalendar", "day", "selectedDay");
							</script>
							<label class='prisenchg'>Pris en charge le <sup>(</sup>*<sup>)</sup>:</label>
							<input type='text' class='prisenchg' name='etpRDV' id="etpRDV" value="<?php echo date("d/m/Y", strtotime($sqlEtape['etpRDV'])); ?>" onKeyPress="return isDateKey(event);"/>
							<div id="calendrierEtpRDV"></div>
							<script type="text/javascript">
								calInit("calendrierEtpRDV", "", "etpRDV", "jsCalendar", "day", "selectedDay");
							</script>
							<label class='commentaire'>Commentaire :</label>
							<textarea class='commentaire' name='commentaire'><?php echo $sqlEtape['etpCommentaire']; ?></textarea>
							<input type="hidden" id="trnNumInfo" name="trnNumInfo" value="<?php echo $trnNum; ?>"/>
							<input type="hidden" id="etpIdInfo" name="etpIdInfo" value="<?php echo $etpId; ?>"/>
						</div>
						<input class="global" id="valider" type="submit" value="Valider" name="maj"/>
						<input class="global" id="retour" type="button" onClick="location='AC12.php'" value="Retour"/>
					</div>
				</form>
				<?php
				if(isset($_GET['message']))
				{
					echo $_GET['message'];
				}
				?>
				<?php
			}
			elseif(isset($_POST['trnNumInfo']) OR isset($_SESSION['trnNumInfo']))
			{
				if(isset($_POST['trnNumInfo']))
				{
					$trnNum=$_POST['trnNumInfo'];
				}
				else
				{
					$trnNum=$_SESSION['trnNumInfo'];
				}

				$sql= "SELECT
								etpId
							FROM
								etape
							WHERE
								etpId=( SELECT
											MAX(etpId)
										FROM
											etape
										WHERE
											trnNum=".$trnNum.")";
				echo $sql;
				$nbLigne=compteSQL($sql);
				if ($nbLigne==0){
					$sqlEtapeId=1;
				}else{
					$sqlEtape=tableSQL($sql);
					$sqlEtapeId=$sqlEtape[0]['etpId'];
					$sqlEtapeId=$sqlEtapeId+1;
				}
				?>
				<div id="erreur"></div>
				<form action="AC13.php" method="POST" onSubmit="return isValidFormEtapeCreate();">
					<div class='ajouter'>
						<div class='titre'>
							<p>
								AC13-Organiser les tournées - Tournée <?php echo $trnNum." Etape ".$sqlEtapeId;?>
							</p>
						</div>
						<div class='contenu'>
							<label class="lieu" for="lieu">Lieu <sup>(</sup>*<sup>)</sup>:</label>
							<select required id="lieu" class="lieu" name="lieu">
								<option selected value="NAN">Aucun lieu</option>
								<?php 
								$sqlLieu=  "SELECT
												lieuid,
												lieuNom,
												comNom
											FROM
												commune,
												lieu
											WHERE
												commune.comid=lieu.comid";
								//$result=executeSQL($sqlLieu);
								$row=tableSQL($sqlLieu);
								foreach ($row as $rows){
									echo "<option value='".$rows[0]."'>".$rows[1]." ".$rows[2]."</option>";
								}
								?>
							</select>
							<label class='rdventre'>Rendez vous entre :</label>
							<input type='text' class='rdventre' name="etpRDVMin" id="etpRDVMin" onKeyPress="return isDateKey(event);"/>
							<div id="calendrierEtpRDVMin"></div>
							<script type="text/javascript">
								calInit("calendrierEtpRDVMin", "", "etpRDVMin", "jsCalendar", "day", "selectedDay");
							</script>
							<label class='et'>et :</label>
							<input type='text' class='et' name="etpRDVMax" id="etpRDVMax" onKeyPress="return isDateKey(event);"/>
							<div id="calendrierEtpRDVMax"></div>
							<script type="text/javascript">
								calInit("calendrierEtpRDVMax", "", "etpRDVMax", "jsCalendar", "day", "selectedDay");
							</script>
							<label class='prisenchg'>Pris en charge le <sup>(</sup>*<sup>)</sup>:</label>
							<input type='text' class='prisenchg' name="etpRDV" id="etpRDV" onKeyPress="return isDateKey(event);" required/>
							<div id="calendrierEtpRDV"></div>
							<script type="text/javascript">
								calInit("calendrierEtpRDV", "", "etpRDV", "jsCalendar", "day", "selectedDay");
							</script>
							<label class='commentaire'>Commentaire :</label>
							<textarea class='commentaire' name='commentaire'></textarea>
							<input type="hidden" id="trnNumInfo" name="trnNumInfo" value="<?php echo $trnNum; ?>"/>
						</div>
						<input class="global" id="valider" type="submit" value="Valider" name="creer"/>
						<input class="global" id="retour" type="button" onClick="location='AC12.php'" value="Retour"/>
					</div>
				</form>
				<?php
				if(isset($_GET['message']))
				{
					echo $_GET['message'];
				}
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