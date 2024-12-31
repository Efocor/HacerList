<?php
//...toggle_task.php
//...script para alternar el estado de una tarea

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //...obtener y limpiar el id de la tarea
    $id = intval($_POST['id']);

    if ($id > 0) {
        try {
            //...obtener el estado actual de la tarea
            $stmt = $db->prepare("SELECT status FROM tasks WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($task) {
                //...alternar el estado de la tarea
                $newStatus = $task['status'] ? 0 : 1;

                //...actualizar el estado de la tarea
                $updateStmt = $db->prepare("UPDATE tasks SET status = :status WHERE id = :id");
                $updateStmt->bindParam(':status', $newStatus);
                $updateStmt->bindParam(':id', $id);
                $updateStmt->execute();

                //...redirigir con mensaje de éxito
                header('Location: index.php?message=Estado de la tarea actualizado.&status=success');
                exit();
            } else {
                //...redirigir con mensaje de error si la tarea no existe
                header('Location: index.php?message=La tarea no existe.&status=error');
                exit();
            }
        } catch (PDOException $e) {
            //...manejar errores de actualización
            header('Location: index.php?message=Error al actualizar el estado de la tarea.&status=error');
            exit();
        }
    } else {
        //...redirigir con mensaje de error si el id no es válido
        header('Location: index.php?message=ID de tarea inválido.&status=error');
        exit();
    }
} else {
    //...redirigir a la página principal si no es una petición POST
    header('Location: index.php');
    exit();
}
?>
