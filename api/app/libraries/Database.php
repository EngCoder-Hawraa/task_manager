<?php
declare(strict_types=1);

/**
 * Database Class using PDO
 * - Connects to DB
 * - Prepares & executes queries
 * - Handles binding
 * - Fetches results
 */

class Database {
    protected string $host = DB_HOST;
    protected string $user = DB_USER;
    protected string $pass = DB_PASS;
    protected string $dbname = DB_NAME;

    protected PDO $dbh;
    protected ?PDOStatement $stmt = null;
    protected ?string $error = null;

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
        $options = [
            PDO::ATTR_PERSISTENT         => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database connection error: " . $this->error);
            die("Database connection failed.");
        }
    }

    public function query(string $sql): void {
        $this->stmt = $this->dbh->prepare($sql);
    }

    public function bind(string $param, mixed $value, int $type = null): void {
        if (is_null($type)) {
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(): bool {
        return $this->stmt->execute();
    }

    public function resultSet(): array {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single(): object|false {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount(): int {
        return $this->stmt->rowCount();
    }

    public function lastInsertId(): string {
        return $this->dbh->lastInsertId();
    }

    public function getError(): ?string {
        return $this->error;
    }
}
