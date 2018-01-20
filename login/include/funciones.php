<?php
include_once 'DbConfig.php';

class funciones extends DbConfig
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getData($query)
    {
        $result = $this->connection->query($query);

        if ($result == false) {
            return false;
        }

        $rows = array();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function execute($query)
    {
        $result = $this->connection->query($query);

        if ($result == false) {
            echo 'Error: No se pudo ejecutar';
            return false;
        } else {
            return true;
        }
    }

    public function delete($id, $table)
    {
        $query = "DELETE FROM usuario WHERE id_usu = $id";

        $result = $this->connection->query($query);

        if ($result == false) {
            echo 'Error: no se puede borrar id ' . $id . ' de tabla  ' . $table;
            return false;
        } else {
            return true;
        }
    }
    public function deleteEmpleado($id, $table)
    {
        $query = "DELETE FROM empleado WHERE id_emp = $id";

        $result = $this->connection->query($query);

        if ($result == false) {
            echo 'Error: no se puede borrar id ' . $id . ' de tabla  ' . $table;
            return false;
        } else {
            return true;
        }
    }

    public function escape_string($value)
    {
        return $this->connection->real_escape_string($value);
    }
}
?>
