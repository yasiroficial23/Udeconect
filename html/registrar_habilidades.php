<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'estudiante') {
    header('Location: ../html/feed.php');
    exit;
}

// Obtener habilidades agrupadas por categoría
$stmt = $pdo->query("SELECT * FROM habilidades ORDER BY categoria, nombre");
$habilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

$agrupadas = [];
foreach ($habilidades as $hab) {
    $agrupadas[$hab['categoria']][] = $hab;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Habilidades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        const habilidadesPorCategoria = <?php echo json_encode($agrupadas); ?>;
    </script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Registrar Habilidades</h2>
    
    <!-- Formulario dinámico -->
    <form id="formHabilidades" method="POST" action="../api/guardar_habilidades.php">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="categoria" class="form-label">Categoría</label>
                <select id="categoria" class="form-select" required>
                    <option value="">Seleccione categoría</option>
                    <?php foreach (array_keys($agrupadas) as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria) ?>"><?= ucfirst($categoria) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="habilidad" class="form-label">Habilidad</label>
                <select id="habilidad" class="form-select" required>
                    <option value="">Seleccione habilidad</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="nivel" class="form-label">Nivel</label>
                <select id="nivel" class="form-select">
                    <option value="básico">Básico</option>
                    <option value="intermedio">Intermedio</option>
                    <option value="avanzado">Avanzado</option>
                    <option value="experto">Experto</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" onclick="agregarHabilidad()">Añadir</button>
            </div>
        </div>

        <!-- Lista de habilidades añadidas -->
        <div id="listaHabilidades" class="mt-4"></div>

        <button type="submit" class="btn btn-success mt-3">Guardar Habilidades</button>
    </form>
</div>

<script>
    const categoriaSelect = document.getElementById('categoria');
    const habilidadSelect = document.getElementById('habilidad');
    const nivelSelect = document.getElementById('nivel');
    const listaDiv = document.getElementById('listaHabilidades');

    // Cambiar las habilidades cuando se selecciona una categoría
    categoriaSelect.addEventListener('change', function () {
        const categoria = this.value;
        habilidadSelect.innerHTML = '<option value="">Seleccione habilidad</option>';

        if (categoria && habilidadesPorCategoria[categoria]) {
            habilidadesPorCategoria[categoria].forEach(hab => {
                const option = document.createElement('option');
                option.value = hab.id;
                option.textContent = hab.nombre;
                habilidadSelect.appendChild(option);
            });
        }
    });

    function agregarHabilidad() {
        const habilidadId = habilidadSelect.value;
        const habilidadText = habilidadSelect.options[habilidadSelect.selectedIndex]?.text;
        const nivel = nivelSelect.value;

        if (!habilidadId || !nivel) {
            alert("Selecciona una habilidad y un nivel.");
            return;
        }

        // Verificar si ya fue añadida
        if (document.getElementById('hab-' + habilidadId)) {
            alert("Ya agregaste esta habilidad.");
            return;
        }

        const div = document.createElement('div');
        div.className = 'alert alert-secondary d-flex justify-content-between align-items-center';
        div.id = 'hab-' + habilidadId;

        div.innerHTML = `
            <div>
                <strong>${habilidadText}</strong> - Nivel: ${nivel}
                <input type="hidden" name="habilidades[]" value="${habilidadId}">
                <input type="hidden" name="niveles[]" value="${nivel}">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Eliminar</button>
        `;

        listaDiv.appendChild(div);
    }
</script>
</body>
</html>
