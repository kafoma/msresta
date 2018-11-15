<!DOCTYPE html>
<html>
<head>
	<title>Probando microservicios</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>

<form id="frmOperaciones">
	Número 1:<input type="text" id="numero1"><br/>
	Número 2:<input type="text" id="numero2"><br/>
	<select id="operacion">
		<option>Seleccione una operación</option>
		<option value="suma">Suma</option>
		<option value="resta">Resta</option>
		<option value="multiplica">Multiplicación</option>
		<option value="divide">División</option>
		<option value="raiz">Raiz Cuadrada</option>
		<option value="exponencial">Exponencial</option>
	</select>
	<input type="button" name="Enviar" value="Enviar" onclick="enviar()">
</form>
<br>
<h2>Respuesta:</h2>
<div id="resultado"></div>
</body>

<script type="text/javascript">
	
	function enviar() {
		var num1 = $("#numero1").val();
		var num2 = $("#numero2").val();
		var operacion = $("#operacion").val();

		$.post("clientems.php", {"num1": num1, "num2": num2, "operacion": operacion} ,function( data ) {
			//var datos = jQuery.parseJSON(data);
		  	$( "#resultado" ).html( data );
		});
	}
</script>
</html>