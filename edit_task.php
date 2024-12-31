<?php
//...edit_task.php
//...script para editar una tarea existente

require 'db.php';

//...verificar si se ha proporcionado un ID válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    //...obtener la tarea correspondiente
    $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        //...redirigir con mensaje de error si la tarea no existe
        header('Location: index.php?message=Tarea no encontrada.&status=error');
        exit();
    }
} else {
    //...redirigir con mensaje de error si el ID no es válido
    header('Location: index.php?message=ID de tarea inválido.&status=error');
    exit();
}

//...manejar la actualización de la tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = intval($_POST['priority']);

    //...validar los datos
    if (!empty($description) && !empty($due_date) && in_array($priority, [1,2,3])) {
        try {
            //...preparar y ejecutar la actualización de la tarea
            $stmt = $db->prepare("UPDATE tasks SET description = :description, due_date = :due_date, priority = :priority WHERE id = :id");
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            //...redirigir con mensaje de éxito
            header('Location: index.php?message=Tarea actualizada exitosamente.&status=success');
            exit();
        } catch (PDOException $e) {
            //...manejar errores de actualización
            header('Location: index.php?message=Error al actualizar la tarea.&status=error');
            exit();
        }
    } else {
        //...redirigir con mensaje de error si los campos son inválidos
        header('Location: edit_task.php?id=' . $id . '&message=Por favor, completa todos los campos correctamente.&status=error');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <!-- incluir el archivo de estilos -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- incluir iconos de Font Awesome para botones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- incluir Google Fonts para una mejor tipografía -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Editar Tarea</h1>

        <!-- Mostrar mensajes de estado (éxito o error) si existen -->
        <?php if (isset($_GET['message'])): ?>
            <div class="message <?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para editar la tarea -->
        <form action="edit_task.php?id=<?php echo $id; ?>" method="POST" class="task-form">
            <input type="text" name="description" placeholder="Descripción de la tarea" value="<?php echo htmlspecialchars($task['description']); ?>" required>
            <input type="date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
            <select name="priority" required>
                <option value="1" <?php if($task['priority'] == 1) echo 'selected'; ?>>Baja</option>
                <option value="2" <?php if($task['priority'] == 2) echo 'selected'; ?>>Media</option>
                <option value="3" <?php if($task['priority'] == 3) echo 'selected'; ?>>Alta</option>
            </select>
            <button type="submit" class="btn-update"><i class="fas fa-save"></i> Actualizar</button>
            <a href="index.php" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
        </form>
    </div>

    <!-- incluir el archivo de scripts -->
    <script src="assets/js/script.js"></script>
</body>
</html>
