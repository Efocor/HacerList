<?php
//...add_task.php
//...script para agregar una nueva tarea con fecha y prioridad

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //...obtener y limpiar los datos de la tarea
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = intval($_POST['priority']);

    //...validar los datos
    if (!empty($description) && !empty($due_date) && in_array($priority, [1,2,3])) {
        try {
            //...preparar y ejecutar la inserción de la tarea
            $stmt = $db->prepare("INSERT INTO tasks (description, due_date, priority) VALUES (:description, :due_date, :priority)");
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->bindParam(':priority', $priority);
            $stmt->execute();

            //...redirigir con mensaje de éxito
            header('Location: index.php?message=Tarea agregada exitosamente.&status=success');
            exit();
        } catch (PDOException $e) {
            //...manejar errores de inserción
            header('Location: index.php?message=Error al agregar la tarea.&status=error');
            exit();
        }
    } else {
        //...redirigir con mensaje de error si los campos son inválidos
        header('Location: index.php?message=Por favor, completa todos los campos correctamente.&status=error');
        exit();
    }
} else {
    //...redirigir a la página principal si no es una petición POST
    header('Location: index.php');
    exit();
}
?>
