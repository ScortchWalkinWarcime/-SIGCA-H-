<?php 
session_start();
require_once __DIR__ . '/database/database.php';

class Apimanejador {

    private $response = [];

    public function __construct() {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }

    public function procesa($parametros) {
        
        $accion = $parametros["accion"] ?? '';
        switch ($accion) {
            case 'login':
                return $this->login($parametros);
            default:
                return ["status" => "error", "message" => "Acción no válida"];
        }
    }

    private function login($parametros) {
        $usuario = $parametros["usuario"] ?? '';
        $contrasenia = $parametros["contrasenia"] ?? '';
        $this->response["status"] = "no";

        try {
            
                require_once 'conexion.php';

                $stm = $conn->prepare("SELECT * FROM usuario WHERE usuario = ? AND contrasenia = ?");
                $stm->bind_param("ss", $usuario, $contrasenia);
                $stm->execute();
                $result = $stm->get_result();

                if ($result->num_rows > 0){
                    $user = $result->fetch_assoc();

                    $this->response["status"] = "ok";

                    $_SESSION['n_usuario'] = $user['nombre'] ?? "";
                } else {
                    $this->response["message"] = "Usuario o contraseña incorrectos";
                }

                $stm->close();
        } catch (Exception $e) {
            $this->response["status"] = "error";
            $this->response["message"] = "Error en la base de datos";
            error_log("DB Error: " . $e->getMessage());
        }

        return $this->response;
    }
}

$input = file_get_contents('php://input');
$parametros = json_decode($input, true) ?: [];

$api = new Apimanejador();
$response = $api->procesa($parametros);

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>