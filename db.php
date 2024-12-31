<?php
//...db.php
//...archivo para manejar la conexion a la base de datos

//...ruta de la base de datos
define('DB_PATH', __DIR__ . '/todo.db');

try {
    //...crear una instancia de PDO para SQLite
    $db = new PDO('sqlite:' . DB_PATH);
    //...configurar el modo de errores de PDO a excepciones
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //...crear la tabla de tareas si no existe
    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        description TEXT NOT NULL,
        status INTEGER NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        due_date DATE,
        priority INTEGER NOT NULL DEFAULT 1
    )");
} catch (PDOException $e) {
    //...manejar errores de conexión
    die("error al conectar con la base de datos: " . $e->getMessage());
}
?>
