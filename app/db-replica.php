<?php
/**
 * Gestión de conexiones Master-Slave para MySQL
 * Implementa patrón de lectura/escritura distribuida
 */

class DatabaseManager {
    private $masterConnection;
    private $slaveConnections = [];
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->initConnections();
    }
    
    /**
     * Inicializar conexiones maestro y esclavos
     */
    private function initConnections() {
        // Conexión Master (Escritura)
        try {
            $this->masterConnection = new PDO(
                "mysql:host={$this->config['master']['host']};dbname={$this->config['master']['database']};charset=utf8",
                $this->config['master']['username'],
                $this->config['master']['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => true
                ]
            );
            
            echo "✅ Conexión Master establecida\n";
        } catch (PDOException $e) {
            echo "❌ Error conectando al Master: " . $e->getMessage() . "\n";
            throw $e;
        }
        
        // Conexiones Slave (Lectura)
        foreach ($this->config['slaves'] as $index => $slaveConfig) {
            try {
                $this->slaveConnections[] = new PDO(
                    "mysql:host={$slaveConfig['host']};dbname={$slaveConfig['database']};charset=utf8",
                    $slaveConfig['username'],
                    $slaveConfig['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => true
                    ]
                );
                
                echo "✅ Conexión Slave " . ($index + 1) . " establecida\n";
            } catch (PDOException $e) {
                echo "⚠️ Error conectando al Slave " . ($index + 1) . ": " . $e->getMessage() . "\n";
                // Continuar sin este slave
            }
        }
    }
    
    /**
     * Obtener conexión para escritura (Master)
     */
    public function getWriteConnection() {
        return $this->masterConnection;
    }
    
    /**
     * Obtener conexión para lectura (Slave con load balancing)
     */
    public function getReadConnection() {
        if (empty($this->slaveConnections)) {
            // Fallback al master si no hay slaves disponibles
            echo "⚠️ No hay slaves disponibles, usando Master para lectura\n";
            return $this->masterConnection;
        }
        
        // Load balancing simple: selección aleatoria
        $randomIndex = array_rand($this->slaveConnections);
        return $this->slaveConnections[$randomIndex];
    }
    
    /**
     * Ejecutar consulta de escritura (INSERT, UPDATE, DELETE)
     */
    public function write($sql, $params = []) {
        $conn = $this->getWriteConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($params);
        
        // Log de operación de escritura
        error_log("WRITE QUERY: $sql - Params: " . json_encode($params));
        
        return $stmt;
    }
    
    /**
     * Ejecutar consulta de lectura (SELECT)
     */
    public function read($sql, $params = []) {
        $conn = $this->getReadConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        // Log de operación de lectura
        error_log("READ QUERY: $sql - Params: " . json_encode($params));
        
        return $stmt;
    }
    
    /**
     * Verificar estado de replicación
     */
    public function checkReplicationStatus() {
        $status = [];
        
        foreach ($this->slaveConnections as $index => $slave) {
            try {
                $stmt = $slave->query("SHOW SLAVE STATUS");
                $slaveStatus = $stmt->fetch();
                
                $status['slave_' . ($index + 1)] = [
                    'io_running' => $slaveStatus['Slave_IO_Running'] ?? 'No',
                    'sql_running' => $slaveStatus['Slave_SQL_Running'] ?? 'No',
                    'seconds_behind' => $slaveStatus['Seconds_Behind_Master'] ?? 'N/A',
                    'last_error' => $slaveStatus['Last_Error'] ?? 'None'
                ];
            } catch (PDOException $e) {
                $status['slave_' . ($index + 1)] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $status;
    }
    
    /**
     * Obtener estadísticas de conexiones
     */
    public function getConnectionStats() {
        return [
            'master_available' => $this->masterConnection !== null,
            'slaves_count' => count($this->slaveConnections),
            'total_connections' => 1 + count($this->slaveConnections)
        ];
    }
}

// Configuración de conexiones
$dbConfig = [
    'master' => [
        'host' => 'db-master',
        'database' => 'appdb',
        'username' => 'appuser',
        'password' => 'app123'
    ],
    'slaves' => [
        [
            'host' => 'db-slave1',
            'database' => 'appdb',
            'username' => 'appuser',
            'password' => 'app123'
        ],
        [
            'host' => 'db-slave2', 
            'database' => 'appdb',
            'username' => 'appuser',
            'password' => 'app123'
        ]
    ]
];

// Instancia global del gestor de base de datos
$dbManager = new DatabaseManager($dbConfig);
?>