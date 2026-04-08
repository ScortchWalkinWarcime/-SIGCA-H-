<?php

class Database {
    private static ?PDO $instance = null;
    
    private function __construct(array $config) {
        
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        
        self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            
        ]);
    }
    
    public static function getInstance(?array $config = null): PDO {
        if (!self::$instance) {
            if (!$config) {
                $config = require __DIR__ . '/config.php';  
            }
            new self($config);
        }
        return self::$instance;
    }
    
    private static function prepareAndExecute(string $sql, array $params): PDOStatement {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    //consulta
    public static function query(string $sql, array $params = []): array {
        return self::prepareAndExecute($sql, $params)->fetchAll();
    }
    //modificación, eliminar
    public static function execute(string $sql, array $params = []): int {
        return self::prepareAndExecute($sql, $params)->rowCount();
    }
    //insertar
    public static function insert(string $sql, array $params = []): string|false {
        self::prepareAndExecute($sql, $params);
        return self::getInstance()->lastInsertId();
    }
    
    
    public static function beginTransaction(): bool {
        return self::getInstance()->beginTransaction();
    }
    
    public static function commit(): bool {
        return self::getInstance()->commit();
    }
    
    public static function rollback(): bool {
        return self::getInstance()->rollBack();
    }

    private function __clone() {}
    public function __wakeup() {
        throw new Exception("No se puede deserializar");
    } 
}