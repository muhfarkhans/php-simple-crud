<?php
use PDO;
use PDOException;

class DB
{
    private $host = "localhost";
    private $database_name = "db_name";
    private $username = "db_username";
    private $password = "db_password";
    public $conn;
    protected $table;
    protected $columns;
    protected $where;
    protected $whereData = [];
    protected $orderBy;
    protected $limit;
    protected $join;

    function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Database could not be connected: " . $exception->getMessage();
        }
    }

    public function where(string $column, $value, $operator = '=')
    {
        $this->whereData[$column] = $value;
        if (empty($this->where)) {
            $this->where .= $column . " " . $operator . " :" . $column;
        } else {
            $this->where .= " AND " . $column . " " . $operator . " :" . $column;
        }
        return $this;
    }

    public function orderBy(string $column, $sort = 'DESC')
    {
        if (empty($this->orderBy)) {
            $this->orderBy .= $column . " " . $sort;
        } else {
            $this->orderBy .= ", " . $column . " " . $sort;
        }
        return $this;
    }

    public function limit(int $size, int $offset = null)
    {
        if ($offset) {
            $this->limit .= " LIMIT " . $size . " OFFSET " . $offset;
        } else {
            $this->limit .= " LIMIT " . $size;
        }
        return $this;
    }

    public function join(string $destination, string $destinationColumn, string $operator, string $sourceColumn, string $joinType = " INNER ")
    {
        $this->join .= $joinType . " JOIN " . $destination . " ON " . $destinationColumn . " " . $operator . " " . $sourceColumn . " ";
        return $this;
    }

    public function get($select = "*")
    {
        $sqlQuery = "SELECT " . $select . " 
                    FROM " . $this->table . " ";

        if (!empty($this->join)) {
            $sqlQuery .= $this->join;
        }

        if (!empty($this->where)) {
            $sqlQuery .= " WHERE ";
            $sqlQuery .= $this->where;
        }

        if (!empty($this->orderBy)) {
            $sqlQuery .= " ORDER BY ";
            $sqlQuery .= $this->orderBy;
        }

        if (!empty($this->limit)) {
            $sqlQuery .= $this->limit;
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($this->whereData);
        return $stmt->fetchAll();
    }

    public function first($select = "*")
    {
        $sqlQuery = "SELECT " . $select . " 
                    FROM " . $this->table . " ";

        if (!empty($this->join)) {
            $sqlQuery .= $this->join;
        }

        if (!empty($this->where)) {
            $sqlQuery .= " WHERE ";
            $sqlQuery .= $this->where;
        }

        if (!empty($this->orderBy)) {
            $sqlQuery .= " ORDER BY ";
            $sqlQuery .= $this->orderBy;
        }

        if (!empty($this->limit)) {
            $sqlQuery .= $this->limit;
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($this->whereData);
        $all = $stmt->fetchAll();

        $first = (object) [];

        if (isset($all[0])) {
            $first = (object) $all[0];
        }
        return $first;
    }

    public function create(array $input)
    {
        $sqlQuery = "INSERT INTO " . $this->table . " (" . implode(", ", array_keys($input)) . ") " .
            " VALUES " . "(:" . implode(", :", array_keys($input)) . ")";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($input);
        return $stmt;
    }

    public function update(array $input)
    {
        $sqlQuery = "UPDATE " . $this->table . " SET " . implode(', ', array_map(function ($k) {
            return $k . '=:' . $k;
        }, array_keys($input)));

        if (!empty($this->where)) {
            $sqlQuery .= " WHERE " . $this->where;
        }

        $data = array_merge($input, $this->whereData);
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($data);
        return $stmt;
    }

    public function delete()
    {
        $sqlQuery = "DELETE FROM " . $this->table;

        if (!empty($this->where)) {
            $sqlQuery .= " WHERE " . $this->where;
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($this->whereData);
        return $stmt;
    }
}
?>