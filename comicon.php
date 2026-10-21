<?php 
// Mostrar todos los errores durante el desarrollo
error_reporting(E_ALL); //indica que PHP debe informar de todos los niveles de error.
ini_set('display_errors', '1'); //permite mostrar los errores en la salida de la página.
ini_set('display_startup_errors', '1'); // solicita que también se muestren los errores producidos durante el arranque de PHP, cuando sea posible.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total = 0;
    $edad = "";
    $nombre = "";
    $edad = "";

    $entrada = ['general', 'vip', 'infantil']; //Tipo de entrada[General|VIP|Infantil]

    $tvideojuegos = $_POST['videojuegos'] ?? ""; // Taller de videojuegos + 10$
    $tcosplay = $_POST['cosplay'] ?? ""; // Taller de cosplay +15$
    $rol = $_POST["rol"] ?? ""; // Zona de roll en vivo +5$

   
}
//try catch finally
try {
    $edad = htmlspecialchars($edad, ENT_QUOTES). $_POST[''] ??"";
     if ($tvideojuegos){
        $total += 10;
    } elseif ($tcosplay){
        $total += 15;
    } elseif ($rol){
        $total += 5;
    }

} catch (Exception $e) {
    echo " ". $e->getMessage() ."";
}finally {
    // Este bloque se ejecuta SIEMPRE (tanto si funcionó como si falló)
    // echo "Finalizando el script... (Guardando traza en el servidor)<br>";
    // Usamos error_log para guardar un registro interno en el archivo de log del servidor
    error_log("[" . date('Y-m-d H:i:s') . "]");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ilerna Comic-Con</title>
</head>
<body>
    <form action="comicon.php" method="POST" enctype="multipart/form-data">
        <label for="nombre">nombre:</label>
        <input type="text" id="nombre" name="nombre" required ><br>
        
        <label for="nombre">edad:</label>
        <input type="text" id="edad" name="edad" required><br>

        <label for="entrada">entrada</label>
        <select id="entrada" name="entrada" required>
            <option value="">-- Selecciona una opción --</option>
            <option value="general">General</option>
            <option value="vip">VIP</option>
            <option value="infantir">Infantil</option>
        </select> <br>

        <input type="checkbox" id="tvideojuegos" name="tvideojuegos" value="si">
        <label for="tvideojuegos">Taller de videojuegos 10€</label><br>

        <input type="checkbox" id="tcosplay" name="tcosplay" value="si">
        <label for="tcosplay">Taller de cosplay 15€</label><br>

        <input type="checkbox" id="rol" name="rol" value="si">
        <label for="rol">Zona de rol en vivo 5€</label><br>

        <button type="submit">Enviar</button>
    </form>
</body>
</html>