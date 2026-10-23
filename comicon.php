
<?php
// Muestra todos los errores durante el desarrollo.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Variables iniciales.
$total = 0;
$error = "";
$resultado = false;
$estado = "fallido";

/**
 * @param string $tipo 
 * @return int Precio base de la entrada.
 * @throws Exception Si el tipo de entrada no es válido.
 */
function calcularPrecioBase(string $tipo): int
{
    switch ($tipo) {
        case 'general':
            return 30;

        case 'vip':
            return 60;

        case 'menores':
            return 10;

        default:
            throw new Exception("Tipo de entrada no válido.");
    }
}

function procesarInscripcion(
    string $entrada,
    array $talleresSeleccionados,
    array $preciosTalleres
): array {
    // Obtener el precio base llamando a la otra función.
    $precioBase = calcularPrecioBase($entrada);
    $total = $precioBase;

    // Sumar el precio de cada taller seleccionado.
    foreach ($talleresSeleccionados as $nombreTaller) {
        $total += $preciosTalleres[$nombreTaller];
    }

    // Devolver los datos que necesita el resguardo.
    return [
        'precioBase' => $precioBase,
        'talleres' => $talleresSeleccionados,
        'total' => $total
    ];
}

// Procesar los datos solamente cuando se envía el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recoger los datos de forma segura.
        $nombre = trim($_POST['nombre'] ?? '');
        $edad = $_POST['edad'] ?? '';
        $entrada = $_POST['entrada'] ?? '';

        // Los checkbox no seleccionados no se reciben en POST.
        $tvideojuegos = $_POST['tvideojuegos'] ?? '';
        $tcosplay = $_POST['tcosplay'] ?? '';
        $rol = $_POST['rol'] ?? '';

        // Validar que el nombre no esté vacío.
        if ($nombre === '') {
            throw new Exception("El nombre es obligatorio.");
        }

        // Validar que la edad sea un entero.
        if (filter_var($edad, FILTER_VALIDATE_INT) === false) {
            throw new Exception("La edad debe ser un número entero.");
        }

        $edad = (int) $edad;

        // Comprobar el rango de edad.
        if ($edad < 1 || $edad > 120) {
            throw new Exception("Introduzca una edad válida.");
        }

        // Comprobar que el tipo de entrada está permitido.
        $entradasPermitidas = ['general', 'vip', 'menores'];

        if (!in_array($entrada, $entradasPermitidas, true)) {
            throw new Exception("Tipo de entrada no válido.");
        }

        // Los menores de 18 años no pueden comprar entradas VIP.
        if ($edad < 18 && $entrada === 'vip') {
            throw new Exception(
                "La entrada VIP está restringida a mayores de edad."
            );
        }

        // La entrada de menores solo es válida para menores de 18.
        if ($edad >= 18 && $entrada === 'menores') {
            throw new Exception(
                "La entrada para menores solo está permitida a menores de 18 años."
            );
        }

        // Array asociativo con el precio de cada taller.
        $valortaller = [
            'videojuegos' => 10,
            'cosplay' => 15,
            'rol' => 5
        ];

        // Crear una lista con los talleres marcados.
        $taller = [];

        if ($tvideojuegos === 'si') {
            $taller[] = 'videojuegos';
        }

        if ($tcosplay === 'si') {
            $taller[] = 'cosplay';
        }

        if ($rol === 'si') {
            $taller[] = 'rol';
        }

        // Calcular el precio utilizando la función correspondiente.
        $inscripcion = procesarInscripcion(
            $entrada,
            $taller,
            $valortaller
        );

        $precioBase = $inscripcion['precioBase'];
        $talleresSeleccionados = $inscripcion['talleres'];
        $total = $inscripcion['total'];

        // Indicar que el procesamiento ha terminado correctamente.
        $resultado = true;
        $estado = 'correcto';

    } catch (Exception $e) {
        // Guardar el mensaje para mostrarlo en la página.
        $error = $e->getMessage();

    } finally {
        // Registrar la fecha y el resultado de la operación.
        error_log(
            '[' . date('Y-m-d H:i:s') . '] ' .
            'Procesamiento de Comic-Con: ' . $estado
        );
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ilerna Comic-Con</title>
</head>
<body>

    <h1>Ilerna Comic-Con</h1>

    <?php if ($error !== ''): ?>
        <p class="error">
            Error: <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($resultado): ?>
        <h2>Resguardo de la inscripción</h2>

        <p>
            Nombre:
            <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>Edad: <?= $edad ?></p>

        <p>
            Entrada:
            <?= htmlspecialchars(ucfirst($entrada), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>Precio base: <?= $precioBase ?> €</p>

        <h3>Talleres seleccionados</h3>

        <?php if (!empty($talleresSeleccionados)): ?>
            <ul>
                <?php foreach ($talleresSeleccionados as $nombreTaller): ?>
                    <li>
                        <?= htmlspecialchars($nombreTaller, ENT_QUOTES, 'UTF-8') ?>:
                        <?= $valortaller[$nombreTaller] ?> €
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No se han seleccionado talleres.</p>
        <?php endif; ?>

        <p>
            Total a pagar:
            <strong><?= $total ?> €</strong>
        </p>
    <?php endif; ?>

    <h2>Formulario de inscripción</h2>

    <form action="comicon.php" method="POST">
        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad"
               min="1" max="120" step="1" required>
        <br>

        <label for="entrada">Tipo de entrada:</label>
        <select id="entrada" name="entrada" required>
            <option value="">-- Selecciona una opción --</option>
            <option value="general">General (30 €)</option>
            <option value="vip">VIP (60 €)</option>
            <option value="menores">Entrada para menores (10 €)</option>
        </select>
        <br>

        <input type="checkbox" id="tvideojuegos"
               name="tvideojuegos" value="si">
        <label for="tvideojuegos">Torneo de videojuegos (10 €)</label>
        <br>

        <input type="checkbox" id="tcosplay"
               name="tcosplay" value="si">
        <label for="tcosplay">Taller de cosplay (15 €)</label>
        <br>

        <input type="checkbox" id="rol" name="rol" value="si">
        <label for="rol">Zona de rol en vivo (5 €)</label>
        <br>

        <button type="submit">Enviar inscripción</button>
    </form>

</body>
</html>
