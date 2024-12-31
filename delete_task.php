<?php
//...delete_task.php
//...script para eliminar una tarea

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //...obtener y limpiar el id de la tarea
    $id = intval($_POST['id']);

    if ($id > 0) {
        try {
            //...preparar y ejecutar la eliminación de la tarea
            $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            //...redirigir con mensaje de éxito
            header('Location: index.php?message=Tarea eliminada exitosamente.&status=success');
            exit();
        } catch (PDOException $e) {
            //...manejar errores de eliminación
            header('Location: index.php?message=Error al eliminar la tarea.&status=error');
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
