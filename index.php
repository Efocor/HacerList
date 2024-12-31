<?php
/* 
    CÓDIGO PHP PARA UN SISTEMA DE LISTA DE TAREAS CON PDO, OCUPA BASE DE DATOS SQLITE
    AUTOR: Felipe Correa Rodríguez

    Su funcionamiento es simple, se pueden agregar tareas, marcarlas como completadas y eliminarlas.
    Las tareas se muestran en orden de creación, con las más recientes primero.
    Se incluyen mensajes de estado para mostrar si una tarea se ha agregado, completado o eliminado correctamente.
    Se incluyen estilos CSS y scripts JS para mejorar la apariencia y la interactividad de la lista de tareas.

    Estando en la carpeta del proyecto, se puede ejecutar con el comando:
    php -S localhost:8000
    Y luego abrir el navegador en la dirección http://localhost:8000
*/

//...index.php
//...pagina principal de la lista de tareas

require 'db.php';

//.....definir las opciones de ordenamiento
$sort_options = [
    'created_desc' => 'Fecha de creación (más recientes)',
    'created_asc' => 'Fecha de creación (más antiguos)',
    'due_asc' => 'Fecha de vencimiento (más próximas)',
    'due_desc' => 'Fecha de vencimiento (más lejanas)',
    'alphabet_asc' => 'Alfabéticamente (A-Z)',
    'alphabet_desc' => 'Alfabéticamente (Z-A)',
    'priority_asc' => 'Prioridad (baja a alta)',
    'priority_desc' => 'Prioridad (alta a baja)'
];

//.....obtener los parámetros de ordenamiento y filtrado
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_desc';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

//.....construir la consulta base
$query = "SELECT * FROM tasks WHERE 1=1";

//.....aplicar filtro
if ($filter === 'completed') {
    $query .= " AND status = 1";
} elseif ($filter === 'pending') {
    $query .= " AND status = 0";
}

//.....aplicar ordenamiento
switch ($sort) {
    case 'created_asc':
        $query .= " ORDER BY created_at ASC";
        break;
    case 'created_desc':
        $query .= " ORDER BY created_at DESC";
        break;
    case 'due_asc':
        $query .= " ORDER BY due_date ASC";
        break;
    case 'due_desc':
        $query .= " ORDER BY due_date DESC";
        break;
    case 'alphabet_asc':
        $query .= " ORDER BY description ASC";
        break;
    case 'alphabet_desc':
        $query .= " ORDER BY description DESC";
        break;
    case 'priority_asc':
        $query .= " ORDER BY priority ASC";
        break;
    case 'priority_desc':
        $query .= " ORDER BY priority DESC";
        break;
    default:
        $query .= " ORDER BY created_at DESC";
        break;
}

//.....ejecutar la consulta
$stmt = $db->query($query);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas Avanzada</title>
    <!-- incluir el archivo de estilos -->
    <link rel="stylesheet" href="assets/css/estilo0x.css">
    <!-- incluir iconos de Font Awesome para botones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- incluir Google Fonts para una mejor tipografía -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Lista de Tareas Avanzada</h1>
        
        <!-- Formulario para añadir una nueva tarea -->
        <form action="add_task.php" method="POST" class="task-form">
            <input type="text" name="description" placeholder="¿Qué necesitas hacer hoy?" required>
            <input type="date" name="due_date" required>
            <select name="priority" required>
                <option value="1">Baja</option>
                <option value="2">Media</option>
                <option value="3">Alta</option>
            </select>
            <button type="submit" class="btn-add"><i class="fas fa-plus"></i> Agregar</button>
        </form>

        <!-- Filtros y Ordenamientos -->
        <div class="filters">
            <form method="GET" action="index.php" class="filter-form">
                <label for="filter">Filtrar:</label>
                <select name="filter" id="filter" onchange="this.form.submit()">
                    <option value="all" <?php if($filter === 'all') echo 'selected'; ?>>Todas</option>
                    <option value="completed" <?php if($filter === 'completed') echo 'selected'; ?>>Completadas</option>
                    <option value="pending" <?php if($filter === 'pending') echo 'selected'; ?>>Pendientes</option>
                </select>
            </form>

            <form method="GET" action="index.php" class="sort-form">
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                <label for="sort">Ordenar por:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <?php foreach($sort_options as $key => $value): ?>
                        <option value="<?php echo $key; ?>" <?php if($sort === $key) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- Mostrar mensajes de estado (éxito o error) -->
        <?php if (isset($_GET['message'])): ?>
            <div class="message <?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Lista de tareas -->
        <?php if (count($tasks) > 0): ?>
            <ul class="task-list">
                <?php foreach ($tasks as $task): ?>
                    <li class="<?php echo $task['status'] ? 'completed' : ''; ?>">
                        <div class="task-details">
                            <!-- Formulario para alternar el estado de la tarea -->
                            <form action="toggle_task.php" method="POST" class="toggle-form">
                                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn-toggle">
                                    <?php echo $task['status'] ? '<i class="fas fa-check-circle"></i>' : '<i class="far fa-circle"></i>'; ?>
                                </button>
                            </form>
                            <!-- Mostrar descripción y detalles de la tarea -->
                            <div class="task-info">
                                <span class="description"><?php echo htmlspecialchars($task['description']); ?></span>
                                <span class="due-date"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($task['due_date']); ?></span>
                                <span class="priority <?php echo $task['priority'] == 3 ? 'high' : ($task['priority'] == 2 ? 'medium' : 'low'); ?>">
                                    <?php 
                                        if($task['priority'] == 3) echo '<i class="fas fa-exclamation-circle"></i> Alta';
                                        elseif($task['priority'] == 2) echo '<i class="fas fa-exclamation-circle"></i> Media';
                                        else echo '<i class="fas fa-exclamation-circle"></i> Baja'; 
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="task-actions">
                            <!-- Botón para editar la tarea -->
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn-edit" title="Editar Tarea">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Formulario para eliminar la tarea -->
                            <form action="delete_task.php" method="POST" class="delete-form">
                                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarea?');" title="Eliminar Tarea">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="no-tasks">¡No tienes tareas pendientes! Agrega una nueva tarea para comenzar.</p>
        <?php endif; ?>
    </div>

    <!-- incluir el archivo de scripts -->
    <script src="assets/js/script.js"></script>
</body>
</html>

