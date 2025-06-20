<?php
// Turn off error display to prevent HTML in JSON response
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Include the JWT library
    require_once 'SimpleJWT.php';
    
    // Secret key 
    $secret_key = 'dosxdos2025*';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // NEW: Handle token decode requests (for returning from NextJS to old app)
        if (isset($input['action']) && $input['action'] === 'decode_token') {
            if (!isset($input['token'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No token provided for decoding']);
                exit;
            }
            
            $jwt = new SimpleJWT($secret_key);
            $decoded = $jwt->decode($input['token']);
            
            if ($decoded === false || $decoded === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'user_data' => $decoded['user_data'] ?? null
            ]);
            exit;
        }
        
        // EXISTING: Original token generation for going to NextJS
        if (!isset($input['user_data'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'User data required']);
            exit;
        }
        
        $userData = $input['user_data'];
        
        // Validate required fields
        if (!isset($userData['id']) || !isset($userData['usuario'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid user data']);
            exit;
        }
        
        // Create JWT payload
        $payload = [
            'iss' => 'dosxdos.app.iidos.com', // Issuer
            'aud' => 'https://nextjs.dosxdos.app', // Audience
            'iat' => time(), // Issued at
            'exp' => time() + 300, // Expires in 5 minutes
            'user_data' => [
                'id' => $userData['id'] ?? '',
                'usuario' => $userData['usuario'] ?? '',
                'nombre' => $userData['nombre'] ?? '',
                'apellido' => $userData['apellido'] ?? '',
                'clase' => $userData['clase'] ?? '',
                'correo' => $userData['correo'] ?? '',
                'cod' => $userData['cod'] ?? '',
                'activo' => $userData['activo'] ?? '',
                'eliminado' => $userData['eliminado'] ?? '',
                'imagen' => $userData['imagen'] ?? '',
                'movil' => $userData['movil'] ?? ''
            ]
        ];
        
        $jwt = new SimpleJWT($secret_key);
        $token = $jwt->encode($payload);
        
        echo json_encode([
            'success' => true,
            'token' => $token,
            'expires_in' => 300
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Test endpoint
        echo json_encode([
            'success' => true,
            'message' => 'Bridge token generator is working',
            'timestamp' => time()
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage()
    ]);
}
?>