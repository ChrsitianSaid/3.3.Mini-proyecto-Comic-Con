<?php
// Mostrar todos los errores durante el desarrollo
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$total = 0;
$error = "";
$resultado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recoger los datos del formulario
        $nombre = trim($_POST['nombre'] ?? "");
        $edad = $_POST['edad'] ?? "";
        $entrada = $_POST['entrada'] ?? "";

        $taller = $_POST["taller"] ??"";

       /* $tvideojuegos = $_POST['tvideojuegos'] ?? "";
        *$tcosplay = $_POST['tcosplay'] ?? "";
        *$rol = $_POST['rol'] ?? "";
        */
        // Validar el nombre
        if ($nombre === "") {
            throw new Exception("El nombre es obligatorio.");
        }

        // Validar la edad
        if (filter_var($edad, FILTER_VALIDATE_INT) === false) {
            throw new Exception("La edad debe ser un número entero.");
        }

        $edad = (int) $edad;

        if ($edad < 0 || $edad > 120) {
            throw new Exception("Introduzca una edad válida.");
        }

        // Validar el tipo de entrada
        $entradasPermitidas = ['general', 'vip', 'infantil'];

        if (!in_array($entrada, $entradasPermitidas, true)) {
            throw new Exception("Tipo de entrada no válido.");
        }

        // Restricción VIP para menores de 18 años
        if ($edad < 18 && $entrada === 'vip') {
            throw new Exception(
                "Los menores de 18 años no pueden comprar entradas VIP."
            );
        }

        // Calcular el precio de la entrada mediante switch
        switch ($entrada) {
            case 'general':
                $total = 20;
                break;

            case 'vip':
                $total = 50;
                break;

            case 'infantil':
                $total = 10;
                break;

            default:
                throw new Exception("Tipo de entrada no válido.");
        }

        
        
        $taller = ['general','vip','infantil'];

        // Sumar los talleres seleccionados
       /* if ($tvideojuegos === "si") {
            $total += 10;
        }

        if ($tcosplay === "si") {
            $total += 15;
        }

        if ($rol === "si") {
            $total += 5;
        }*/

        $resultado = true;

    } catch (Exception $e) {
        $error = $e->getMessage();

    } finally {
        // Registrar el procesamiento del formulario
        error_log(
            "[" . date('Y-m-d H:i:s') .
            "] Procesamiento del formulario Comic-Con."
        );
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ilerna Comic-Con</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Ilerna Comic-Con</h1>

    <?php if ($error !== ""): ?>
        <p>
            Error: <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($resultado): ?>
        <h2>Resumen de la inscripción</h2>

        <p>
            Nombre:
            <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>Edad: <?= $edad ?></p>

        <p>
            Entrada:
            <?= htmlspecialchars(ucfirst($entrada), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            Total a pagar:
            <strong><?= $total ?> €</strong>
        </p>
    <?php endif; ?>

    <form action="comicon.php" method="POST">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad"
               min="0" max="120" step="1" required>
        <br>

        <label for="entrada">Entrada:</label>
        <select id="entrada" name="entrada" required>
            <option value="">-- Selecciona una opción --</option>
            <option value="general">General (20 €)</option>
            <option value="vip">VIP (50 €)</option>
            <option value="infantil">Infantil (10 €)</option>
        </select>
        <br>

        <input type="checkbox" id="tvideojuegos"
               name="tvideojuegos" value="si">
        <label for="tvideojuegos">Taller de videojuegos (10 €)</label>
        <br>

        <input type="checkbox" id="tcosplay"
               name="tcosplay" value="si">
        <label for="tcosplay">Taller de cosplay (15 €)</label>
        <br>

        <input type="checkbox" id="rol" name="rol" value="si">
        <label for="rol">Zona de rol en vivo (5 €)</label>
        <br>

        <button type="submit">Enviar</button>

    </form>

</body>
</html>