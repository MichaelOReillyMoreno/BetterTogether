<?php
	include 'logGlobalVars.php';
	include 'VarsBBDDFunctions.php';

	$gincanaIsOn;
	$allowChangeTeam;

	$id = 0;
	$clientDesiredTeam = 0;
	$clientCurrentTeam = 0;
	$teamMessage = "_98_";

	if (isset($_GET['id']))
	{
		$id = $_GET['id'];
	}

	if (isset($_GET['desiredTeam']))
	{
		$clientDesiredTeam = $_GET['desiredTeam'];
	}

	if (isset($_GET['currentTeam']))
	{
		$clientCurrentTeam = $_GET['currentTeam'];
	}

	$con=mysqli_connect($server, $user, $password, $dataBaseUsers);

		if (mysqli_connect_errno())
		{
			$teamMessage = "_98_";//error de conexion
		}
		else
		{
			$consult = " SELECT team FROM players WHERE `id`='$id'";

			$resultCons = mysqli_query($con, $consult);
			$row = mysqli_fetch_array($resultCons);

			if ((int)$row['team'] != (int)$clientCurrentTeam)
			{
				if($row['team'] != "")
					$teamMessage = "_" . $row['team'] . "_";//EL server cambio de equipo al cliente
				else//No existe dicha id
					$teamMessage = "_97_";
			}
			else if ((int)$row['team'] != (int)$clientDesiredTeam)
			{
				$sql = "UPDATE `players` SET `team`='$clientDesiredTeam' WHERE `id`='$id'";

				if ($con->query($sql) === FALSE)
				{
					$teamMessage = "_99_";//error de query
				}
				else
				{
					$teamMessage = "_42_";//el cliente cambia de equipo
				}
			}
			else
			{
				$teamMessage = "_0_";//el cliente mantiene su equipo
			}
		}
	
	$gincanaIsOn = GetGameIsEnable($con);
	$allowChangeTeam = GetchangeTeamIsEnable($con);	

	if(!isset($gincanaIsOn))
	{
		$gincanaIsOn = 0;
	}
	if(!isset($allowChangeTeam))
	{
		$allowChangeTeam = 0;
	}

	echo $gincanaIsOn . $teamMessage . $allowChangeTeam;	

	mysqli_close($con);

	function GetServerState()
	{
	    echo $gincanaIsOn;
	}
?>
