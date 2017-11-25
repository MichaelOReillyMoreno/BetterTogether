<?php

include 'BBDDFunctions.php';
include 'VarsBBDDFunctions.php'; 
include 'inputs.php';
include 'logGlobalVars.php';

if (session_status() == PHP_SESSION_NONE)
{
		session_start();
}

$access = $_SESSION['access'];

if(!isset($access))
{
		$access = 0;
}

if($access != 0)
{ 
//...
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<title>Gincana admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Cache-control" content="no-cache">
<link rel="stylesheet" type="text/css" href="css/style.css?v=1.1">
<?php

    $gincanaIsOn;
	$allowChangeTeam;
	
	$con=mysqli_connect($server, $user, $password, $dataBaseUsers);
	$gincanaIsOn = GetGameIsEnable($con);
	$allowChangeTeam = GetchangeTeamIsEnable($con);
?>

<script>

var bttGincanaIsOn;
var bttAllowChangeTeam;

var gincanaIsON;
var allowChangeTeam;

function LoadDataBaseVars()
{
	bttGincanaIsOn = document.getElementById("bttGincanaIsOn");
	bttAllowChangeTeam = document.getElementById("bttAllowChangeTeam");
	
	gincanaIsON = "<?php echo $gincanaIsOn; ?>";
	
    allowChangeTeam = "<?php echo $allowChangeTeam; ?>";
    ChangeStatesColor();
}
function ChangeStatesColor()
{
	if(gincanaIsON == 1)
	{
		bttGincanaIsOn.style.backgroundColor = "green";
	}
	else
	{
		bttGincanaIsOn.style.backgroundColor = "red";
	}
	
	if(allowChangeTeam == 1)
	{
		bttAllowChangeTeam.style.backgroundColor = "green";
	}
	else
	{
		bttAllowChangeTeam.style.backgroundColor = "red";
	}
}

function GoTeamsAdmin(){window.location.href= '<?php echo $teamsURL; ?>';}

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.71/jquery.csv-0.71.min.js"></script>
<script type="text/javascript">  
    $(document).ready(function() {

    // The event listener for the file upload
    document.getElementById('txtFileUpload').addEventListener('change', upload, false);

    // Method that checks that the browser supports the HTML5 File API
    function browserSupportFileUpload() {
        var isCompatible = false;
        if (window.File && window.FileReader && window.FileList && window.Blob) {
        isCompatible = true;
        }
        return isCompatible;
    }

    // Method that reads and processes the selected file
    function upload(evt) {
        if (!browserSupportFileUpload()) {
            alert('The File APIs are not fully supported in this browser!');
            } else {
                var data = null;
                var file = evt.target.files[0];
                var reader = new FileReader();
                reader.readAsText(file);
                reader.onload = function(event) {
                    var csvData = event.target.result;
                    data = $.csv.toArrays(csvData);
                    if (data && data.length > 0) {
                    alert((data.length - 1) + '- usuarios importados!');
					window.location.href = "csvImportData.php?dataStrg=" + JSON.stringify(data);
                    } else {
                        alert('No data to import!');
                    }
                };
                reader.onerror = function() {
                    alert('Unable to read ' + file.fileName);
                };
				
				document.getElementById("txtFileUpload").value = "";
            }
        }
    });
</script>
</head>
<body onload="LoadDataBaseVars()">

	<div class="DataBaseContent">
		<?php
			ShowUsers($con);
			mysqli_close($con);
		?>
	<div class="DataBaseOptions">
		<div class="DataBaseOp DataBaseOpDark">
		<div class="DataBaseTitle">Añadir participante</div>
			<form method="post" action="" class="Form FormTwoLines">
				Nombre:
				<input type="text" name="firstname" class="InputText">
				1ºApellido:
				<input type="text" name="surname" class="InputText">
				<br>
				2ºApellido:
				<input type="text" name="secondSurname" class="InputText">
				Campo abierto:
				<input type="text" name="openField" class="InputText">
				<input type="submit" name="submitInsert" value="Añadir" class="FormInput FormInputTwoLines">
				<br>
				Equipo:
				<select name="chooseTeam" class="InputList">
				  <option value= "0">Ninguno</option>
				  <option value= "1">Alpha</option>
				  <option value= "2">Beta</option>
				  <option value= "3">Gamma</option>
				  <option value= "4">Delta</option>
				  <option value= "5">Epsilon</option>
				  <option value= "6">Zeta</option>
				  <option value= "7">Eta</option>
				  <option value= "8">Theta</option>
				  <option value= "9">Iota</option>
				  <option value= "10">Kappa</option>
				  <option value= "11">Lambda</option>
				  <option value= "12">Mu</option>
				  <option value= "13">Nu</option>
				  <option value= "14">Xi</option>
				  <option value= "15">Omicron</option>
				  <option value= "16">Pi</option>
				  <option value= "17">Rho</option>
				  <option value= "18">Sigma</option>
				  <option value= "19">Tau</option>
				  <option value= "20">Upsilon</option>
				  <option value= "21">Phi</option>
				  <option value= "22">Chi</option>
				  <option value= "23">Psi</option>
				  <option value= "24">Omega</option>
				</select>
				
			</form>
		</div>
		<div class="DataBaseOp">
			<div class="DataBaseTitle">Eliminar participante</div>
			<form method="post" action="" class="Form">
				ID:
				<input type="number_format" name="IdDelete" class="InputText">
				<input type="submit" name="submitDelete" value="Borrar" class="FormInput">
			</form>
		</div>

		<div class="DataBaseOp DataBaseOpDark">
			<div class="DataBaseTitle">Cambiar de equipo a participante</div>
			<form method="post" action="" class="Form">
				ID:
				<input type="number_format" name="IdChangeTeam" class="InputText">

				<select name="Teams" class="InputList">
				  <option value= "0">Ninguno</option>
				  <option value= "1">Alpha</option>
				  <option value= "2">Beta</option>
				  <option value= "3">Gamma</option>
				  <option value= "4">Delta</option>
				  <option value= "5">Epsilon</option>
				  <option value= "6">Zeta</option>
				  <option value= "7">Eta</option>
				  <option value= "8">Theta</option>
				  <option value= "9">Iota</option>
				  <option value= "10">Kappa</option>
				  <option value= "11">Lambda</option>
				  <option value= "12">Mu</option>
				  <option value= "13">Nu</option>
				  <option value= "14">Xi</option>
				  <option value= "15">Omicron</option>
				  <option value= "16">Pi</option>
				  <option value= "17">Rho</option>
				  <option value= "18">Sigma</option>
				  <option value= "19">Tau</option>
				  <option value= "20">Upsilon</option>
				  <option value= "21">Phi</option>
				  <option value= "22">Chi</option>
				  <option value= "23">Psi</option>
				  <option value= "24">Omega</option>
				</select>

				<input type="submit" name="changeTeam" value="Cambiar" class="FormInput">
			</form>
		</div>

		<div class="ControlerGincana">
		<div class="DataBaseTitle">Permitir comenzar el juego</div>
			<form method="post" action="" class="GincanaOpp">

				<input type="submit" name="StartGincana" value="Iniciar" class="FormInput">

			</form>

			<form method="post" action=""  class="GincanaOpp">

				<input type="submit" name="StopGincana" value="Parar" class="FormInput">

			</form>

			<div class="StateBorder"><div class="State" id="bttGincanaIsOn"></div></div>

		</div>
		
		<div class="ControlerGincana DataBaseOpDark">
		<div class="DataBaseTitle">Permitir cambios de equipo</div>
			<form method="post" action="" class="GincanaOpp">

				<input type="submit" name="AllowChangeTeam" value="Si" class="FormInput">

			</form>

			<form method="post" action=""  class="GincanaOpp">

				<input type="submit" name="NotAllowChangeTeam" value="No" class="FormInput">

			</form>

			<div class="StateBorder"><div class="State" id="bttAllowChangeTeam"></div></div>

		</div>
		
		<div class="ControlerGincana">
		<div class="DataBaseTitle">Borrar a todos los jugadores</div>
			<form method="post" action="" class="GincanaOpp" onsubmit="return confirm('Estás seguro de querer borrar a todos los jugadores? AVISO : ESTA ACCIÓN NO SE PUEDE DESHACER');">

				<input type="submit" name="DeleteAllPlayers" value="Borrar" class="FormInput">

			</form>
		</div>
		
		<div class="DataBaseOp fileupload DataBaseOpDark" id="dvImportSegments" >
			<div class="DataBaseTitle">Sube un archivo .csv de usuarios</div>
			
			<div class="fileUpload">
				<span>Subir</span>
				<input type="file" name="File Upload" id="txtFileUpload" accept=".csv" class="upload"/>
			</div>		
		</div>
		
		<div class="ControlerGincana link">			
			<button onclick="GoTeamsAdmin()"  class="FormInput linkBtt">Ir al administrador de equipos</button>
		</div>
		
		
	
	</div>


	</div>

</body>
</html>
<?php
}
 else
	{
		header($logURL);
	}
?>