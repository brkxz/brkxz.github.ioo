<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Productos disponibles (mismo array que en dashboard.php)
$productos = [
    1 => [
        'id' => 1,
        'nombre' => 'Pan Dulce Tradicional',
        'precio' => 2.50,
        'descripcion' => 'Pan dulce artesanal con ingredientes tradicionales',
        'imagen' => 'pan.jpg',
        'categoria' => 'Pan Dulce'
    ],
    2 => [
        'id' => 2,
        'nombre' => 'Bolillos Caseros',
        'precio' => 0.50,
        'descripcion' => 'Bolillos frescos horneados diariamente',
        'imagen' => 'bollos02.jpg',
        'categoria' => 'Pan Salado'
    ],
    3 => [
        'id' => 3,
        'nombre' => 'Empanadas de Pollo',
        'precio' => 3.00,
        'descripcion' => 'Empanadas rellenas de pollo con especias',
        'imagen' => 'empanada.jpg',
        'categoria' => 'Empanadas'
    ],
    4 => [
        'id' => 4,
        'nombre' => 'Conchas de Vainilla',
        'precio' => 1.50,
        'descripcion' => 'Tradicionales conchas mexicanas de vainilla',
        'imagen' => 'concha.jpg',
        'categoria' => 'Pan Dulce'
    ],
    5 => [
        'id' => 5,
        'nombre' => 'Pastel Especial',
        'precio' => 25.00,
        'descripcion' => 'Pastel decorado para ocasiones especiales',
        'imagen' => 'pastel especial.jpg',
        'categoria' => 'Pasteles'
    ],
    6 => [
        'id' => 6,
        'nombre' => 'Pan Integral',
        'precio' => 4.00,
        'descripcion' => 'Pan integral con semillas y cereales',
        'imagen' => 'pan.jpg',
        'categoria' => 'Pan Integral'
    ]
];

// Función para generar mensaje de WhatsApp
function generarMensajeWhatsApp($carrito, $total, $usuario, $pedido_id) {
    $mensaje = "🥖 *NUEVO PEDIDO #" . $pedido_id . "*\n";
    $mensaje .= "Cliente: " . $usuario . "\n";
    $mensaje .= "Fecha: " . date('d/m/Y H:i') . "\n\n";
    $mensaje .= "*PRODUCTOS:*\n";
    
    foreach ($carrito as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $mensaje .= "• " . $item['nombre'] . " x" . $item['cantidad'] . " = S/ " . number_format($subtotal, 2) . "\n";
    }
    
    $mensaje .= "\n*TOTAL: S/ " . number_format($total, 2) . "*\n";
    $mensaje .= "Estado: Pendiente\n";
    $mensaje .= "¡Gracias por su pedido! 🙏";
    
    return $mensaje;
}

// Procesar solicitud
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'agregar':
        $producto_id = (int)($input['producto_id'] ?? $_POST['producto_id'] ?? 0);
        $cantidad = (int)($input['cantidad'] ?? $_POST['cantidad'] ?? 1);
        
        if (!isset($productos[$producto_id])) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            exit;
        }
        
        if ($cantidad < 1 || $cantidad > 99) {
            echo json_encode(['success' => false, 'message' => 'Cantidad inválida']);
            exit;
        }
        
        // Si el producto ya está en el carrito, sumar la cantidad
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
            // Limitar cantidad máxima total a 99
            if ($_SESSION['carrito'][$producto_id]['cantidad'] > 99) {
                $_SESSION['carrito'][$producto_id]['cantidad'] = 99;
            }
        } else {
            $_SESSION['carrito'][$producto_id] = [
                'id' => $producto_id,
                'nombre' => $productos[$producto_id]['nombre'],
                'precio' => $productos[$producto_id]['precio'],
                'cantidad' => $cantidad,
                'imagen' => $productos[$producto_id]['imagen']
            ];
        }
        
        // Calcular totales
        $total_productos = 0;
        $total_precio = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total_productos += $item['cantidad'];
            $total_precio += $item['precio'] * $item['cantidad'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito_total' => $total_productos,
            'precio_total' => $total_precio
        ]);
        break;
        
    case 'obtener':
        $total_productos = 0;
        $total_precio = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total_productos += $item['cantidad'];
            $total_precio += $item['precio'] * $item['cantidad'];
        }
        
        echo json_encode([
            'success' => true,
            'carrito' => $_SESSION['carrito'],
            'total_productos' => $total_productos,
            'total_precio' => $total_precio
        ]);
        break;
        
    case 'actualizar':
        $producto_id = (int)($input['producto_id'] ?? $_POST['producto_id'] ?? 0);
        $cantidad = (int)($input['cantidad'] ?? $_POST['cantidad'] ?? 0);
        
        if ($cantidad <= 0) {
            if (isset($_SESSION['carrito'][$producto_id])) {
                unset($_SESSION['carrito'][$producto_id]);
            }
        } elseif (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] = min($cantidad, 99);
        }
        
        // Recalcular totales
        $total_productos = 0;
        $total_precio = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total_productos += $item['cantidad'];
            $total_precio += $item['precio'] * $item['cantidad'];
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Carrito actualizado',
            'total_productos' => $total_productos,
            'total_precio' => $total_precio
        ]);
        break;
        
    case 'eliminar':
        $producto_id = (int)($input['producto_id'] ?? $_POST['producto_id'] ?? 0);
        
        if (isset($_SESSION['carrito'][$producto_id])) {
            unset($_SESSION['carrito'][$producto_id]);
            
            // Recalcular totales
            $total_productos = 0;
            $total_precio = 0;
            foreach ($_SESSION['carrito'] as $item) {
                $total_productos += $item['cantidad'];
                $total_precio += $item['precio'] * $item['cantidad'];
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Producto eliminado del carrito',
                'total_productos' => $total_productos,
                'total_precio' => $total_precio
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
        }
        break;
        
    case 'vaciar':
        $_SESSION['carrito'] = [];
        echo json_encode([
            'success' => true, 
            'message' => 'Carrito vaciado',
            'total_productos' => 0,
            'total_precio' => 0
        ]);
        break;
        
    case 'finalizar_compra':
        $total = floatval($input['total'] ?? 0);
        $productos_count = intval($input['productos'] ?? 0);
        
        if (empty($_SESSION['carrito'])) {
            echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
            exit;
        }
        
        // Validar que el total enviado coincida con el calculado
        $total_calculado = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total_calculado += $item['precio'] * $item['cantidad'];
        }
        
        if (abs($total - $total_calculado) > 0.01) {
            echo json_encode(['success' => false, 'message' => 'Error en el cálculo del total']);
            exit;
        }
        
        try {
            $host = 'localhost';
            $db   = 'bd_22';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // Crear tabla de pedidos si no existe
            $pdo->exec("CREATE TABLE IF NOT EXISTS pedidos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT,
                usuario_nombre VARCHAR(255),
                productos_json TEXT,
                total DECIMAL(10,2),
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                estado ENUM('pendiente', 'confirmado', 'preparando', 'listo', 'entregado', 'cancelado') DEFAULT 'pendiente'
            )");
            
            // Insertar pedido
            $stmt = $pdo->prepare("
                INSERT INTO pedidos (usuario_id, usuario_nombre, productos_json, total) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $_SESSION['user_id'] ?? 0,
                $_SESSION['user'],
                json_encode($_SESSION['carrito'], JSON_UNESCAPED_UNICODE),
                $total
            ]);
            
            $pedido_id = $pdo->lastInsertId();
            
            // Generar mensaje para WhatsApp
            $whatsapp_message = generarMensajeWhatsApp($_SESSION['carrito'], $total, $_SESSION['user'], $pedido_id);
            
            // Generar URL de WhatsApp
            $numero_whatsapp = "51982362999"; // Número de la panadería
            $whatsapp_url = "https://wa.me/" . $numero_whatsapp . "?text=" . urlencode($whatsapp_message);
            
            // Guardar datos del pedido para confirmación
            $pedido_info = [
                'id' => $pedido_id,
                'usuario' => $_SESSION['user'],
                'productos' => $_SESSION['carrito'],
                'total' => $total,
                'fecha' => date('Y-m-d H:i:s')
            ];
            
            // Vaciar carrito después de la compra exitosa
            $_SESSION['carrito'] = [];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Pedido realizado con éxito',
                'pedido_id' => $pedido_id,
                'whatsapp_url' => $whatsapp_url,
                'whatsapp_message' => $whatsapp_message, // Para debug
                'pedido_info' => $pedido_info
            ]);
            
        } catch (PDOException $e) {
            // Log del error (en producción no mostrar detalles del error)
            error_log("Error en base de datos: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error al procesar el pedido. Por favor intente nuevamente.'
            ]);
        } catch (Exception $e) {
            error_log("Error general: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error inesperado. Por favor intente nuevamente.'
            ]);
        }
        break;
        
    case 'obtener_historial':
        try {
            $host = 'localhost';
            $db   = 'bd_22';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // Obtener pedidos del usuario actual
            $stmt = $pdo->prepare("
                SELECT id, productos_json, total, fecha, estado 
                FROM pedidos 
                WHERE usuario_nombre = ? 
                ORDER BY fecha DESC 
                LIMIT 10
            ");
            
            $stmt->execute([$_SESSION['user']]);
            $pedidos = $stmt->fetchAll();
            
            // Decodificar JSON de productos
            foreach ($pedidos as &$pedido) {
                $pedido['productos'] = json_decode($pedido['productos_json'], true);
                unset($pedido['productos_json']);
            }
            
            echo json_encode([
                'success' => true,
                'pedidos' => $pedidos
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener historial de pedidos'
            ]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
?>