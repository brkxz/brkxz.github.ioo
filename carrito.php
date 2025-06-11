<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
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

// Procesar acciones del carrito
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $producto_id = (int)($_POST['producto_id'] ?? 0);
    
    switch ($action) {
        case 'actualizar':
            $cantidad = (int)($_POST['cantidad'] ?? 0);
            if ($cantidad > 0 && isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
            }
            break;
            
        case 'eliminar':
            if (isset($_SESSION['carrito'][$producto_id])) {
                unset($_SESSION['carrito'][$producto_id]);
            }
            break;
            
        case 'vaciar':
            $_SESSION['carrito'] = [];
            break;
    }
    
    // Redireccionar para evitar reenvío del formulario
    header("Location: carrito.php");
    exit();
}

// Calcular totales
$total_productos = 0;
$total_precio = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_productos += $item['cantidad'];
    $total_precio += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Panadería La Tradición</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f1eb 0%, #e8ddd4 100%);
            color: #4b2e05;
            min-height: 100vh;
        }
        
        .header {
            background: rgba(255,255,255,0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header h1 {
            color: #d2691e;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .header-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .nav-link {
            background: #f8f5f0;
            color: #8B4513;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background: #d2691e;
            color: white;
        }
        
        .logout-btn {
            background: #d2691e;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #b65c1a;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .carrito-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .carrito-header h2 {
            color: #8B4513;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .carrito-stats {
            color: #d2691e;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .carrito-empty {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .carrito-empty h3 {
            color: #8B4513;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .carrito-items {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .carrito-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s;
        }
        
        .carrito-item:hover {
            background: #fafafa;
        }
        
        .carrito-item:last-child {
            border-bottom: none;
        }
        
        .item-imagen {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 1.5rem;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-nombre {
            font-size: 1.3rem;
            font-weight: 700;
            color: #8B4513;
            margin-bottom: 0.5rem;
        }
        
        .item-precio {
            font-size: 1.1rem;
            color: #d2691e;
            font-weight: 600;
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .cantidad-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .cantidad-input {
            width: 60px;
            padding: 5px;
            border: 2px solid #d2691e;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #d2691e;
            color: white;
        }
        
        .btn-primary:hover {
            background: #b65c1a;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .carrito-total {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .total-precio {
            font-size: 2.5rem;
            font-weight: 700;
            color: #d2691e;
            margin-bottom: 1rem;
        }
        
        .carrito-acciones {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-large {
            padding: 12px 30px;
            font-size: 1.1rem;
        }

        /* Estilos para el modal de WhatsApp */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 500px;
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #d2691e, #b65c1a);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .modal-header h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .whatsapp-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #8B4513;
        }
        
        .form-group input {
            padding: 12px;
            border: 2px solid #d2691e;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #b65c1a;
            box-shadow: 0 0 0 3px rgba(210, 105, 30, 0.1);
        }
        
        .pedido-resumen {
            background: #f8f5f0;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .pedido-resumen h4 {
            color: #8B4513;
            margin-bottom: 1rem;
        }
        
        .pedido-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            color: #6b4a17;
        }
        
        .pedido-total {
            border-top: 2px solid #d2691e;
            padding-top: 0.5rem;
            margin-top: 1rem;
            font-weight: 700;
            color: #d2691e;
            font-size: 1.2rem;
        }
        
        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .close-btn {
            background: #6c757d;
            color: white;
        }
        
        .close-btn:hover {
            background: #5a6268;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: #28a745;
            color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateX(400px);
            transition: transform 0.3s;
            z-index: 1100;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.error {
            background: #dc3545;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .carrito-item {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .item-imagen {
                margin-right: 0;
            }
            
            .carrito-acciones {
                flex-direction: column;
            }
            
            .modal-content {
                margin: 10% auto;
                width: 95%;
            }
            
            .modal-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛒 Mi Carrito de Compras</h1>
        <div class="header-nav">
            <a href="dashboard.php" class="nav-link">← Seguir Comprando</a>
            <span style="color: #8B4513; font-weight: 600;">
                Bienvenido, <?php echo htmlspecialchars($_SESSION['user']); ?>
            </span>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="carrito-header">
            <h2>🥖 Panadería La Tradición</h2>
            <div class="carrito-stats">
                <?php echo $total_productos; ?> productos • Total: S/ <?php echo number_format($total_precio, 2); ?>
            </div>
        </div>

        <?php if (empty($_SESSION['carrito'])): ?>
            <div class="carrito-empty">
                <h3>Tu carrito está vacío</h3>
                <p style="color: #8B4513; margin-bottom: 2rem;">¡Agrega algunos de nuestros deliciosos productos!
                     ¡DELIVIRI SOLO SANTA ANA!</p>
                <a href="dashboard.php" class="btn btn-primary btn-large">Ver Productos</a>
            </div>
        <?php else: ?>
            <div class="carrito-items">
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <?php $producto = $productos[$item['id']]; ?>
                    <div class="carrito-item">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                             class="item-imagen"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlbjwvdGV4dD48L3N2Zz4='">
                        
                        <div class="item-info">
                            <div class="item-nombre"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                            <div class="item-precio">
                                S/ <?php echo number_format($producto['precio'], 2); ?> c/u
                                • Subtotal: S/ <?php echo number_format($producto['precio'] * $item['cantidad'], 2); ?>
                            </div>
                        </div>
                        
                        <div class="item-controls">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="actualizar">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <div class="cantidad-control">
                                    <label>Cantidad:</label>
                                    <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" 
                                           min="1" max="99" class="cantidad-input" 
                                           onchange="this.form.submit()">
                                </div>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('¿Eliminar este producto del carrito?')">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="carrito-total">
                <div class="total-precio">
                    Total: S/ <?php echo number_format($total_precio, 2); ?>
                </div>
                
                <div class="carrito-acciones">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="vaciar">
                        <button type="submit" class="btn btn-danger btn-large" 
                                onclick="return confirm('¿Vaciar todo el carrito?')">
                            🗑️ Vaciar Carrito
                        </button>
                    </form>
                    
                    <a href="dashboard.php" class="btn btn-primary btn-large">
                        ← Seguir Comprando
                    </a>
                    
                    <button class="btn btn-success btn-large" onclick="abrirModalWhatsApp()">
                        📱 Finalizar Compra
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de WhatsApp -->
    <div id="whatsappModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📱 Finalizar Compra por WhatsApp</h3>
                <p>Ingresa tu número de WhatsApp para enviar tu pedido</p>
            </div>
            <div class="modal-body">
                <form class="whatsapp-form" onsubmit="enviarPedidoWhatsApp(event)">
                    <div class="form-group">
                        <label for="nombreCliente">Nombre Completo:</label>
                        <input type="text" id="nombreCliente" required 
                               placeholder="Ej: lujan karrion" value="<?php echo htmlspecialchars($_SESSION['user']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="whatsappNumber">Número de WhatsApp:</label>
                        <input type="tel" id="whatsappNumber" required 
                               placeholder="Ej: 987654321" pattern="[0-9]{9,15}">
                        <small style="color: #6b4a17;">Sin código de país, solo tu número</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="direccion">Dirección de Entrega (Opcional):</label>
                        <input type="text" id="direccion" 
                               placeholder="Ej: Av. Principal 123, Lima">
                    </div>
                    
                    <div class="pedido-resumen">
                        <h4>📋 Resumen del Pedido:</h4>
                        <?php foreach ($_SESSION['carrito'] as $item): ?>
                            <?php $producto = $productos[$item['id']]; ?>
                            <div class="pedido-item">
                                <span><?php echo htmlspecialchars($producto['nombre']); ?> x<?php echo $item['cantidad']; ?></span>
                                <span>S/ <?php echo number_format($producto['precio'] * $item['cantidad'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="pedido-total">
                            <div class="pedido-item">
                                <span>TOTAL:</span>
                                <span>S/ <?php echo number_format($total_precio, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-buttons">
                        <button type="button" class="btn btn-danger close-btn" onclick="cerrarModalWhatsApp()">
                            ❌ Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            📱 Enviar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notificación -->
    <div class="notification" id="notification"></div>

    <script>
        // Datos del carrito para JavaScript
        const carritoData = <?php echo json_encode($_SESSION['carrito']); ?>;
        const productosData = <?php echo json_encode($productos); ?>;
        const totalPrecio = <?php echo $total_precio; ?>;
        
        function abrirModalWhatsApp() {
            document.getElementById('whatsappModal').style.display = 'block';
        }
        
        function cerrarModalWhatsApp() {
            document.getElementById('whatsappModal').style.display = 'none';
        }
        
        // Cerrar modal si se hace clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('whatsappModal');
            if (event.target == modal) {
                cerrarModalWhatsApp();
            }
        }
        
        function enviarPedidoWhatsApp(event) {
            event.preventDefault();
            
            const nombre = document.getElementById('nombreCliente').value.trim();
            const whatsapp = document.getElementById('whatsappNumber').value.trim();
            const direccion = document.getElementById('direccion').value.trim();
            
            if (!nombre || !whatsapp) {
                mostrarNotificacion('Por favor completa todos los campos obligatorios', 'error');
                return;
            }
            
            // Validar número de WhatsApp
            if (!/^[0-9]{9,15}$/.test(whatsapp)) {
                mostrarNotificacion('Número de WhatsApp inválido', 'error');
                return;
            }
            
            // Generar mensaje de WhatsApp
            let mensaje = `*NUEVO PEDIDO - PANADERÍA LA TRADICIÓN*\n\n`;
            mensaje += `*Cliente:* ${nombre}\n`;
            mensaje += `*WhatsApp:* ${whatsapp}\n`;
            if (direccion) {
                mensaje += `*Dirección:* ${direccion}\n`;
            }
            mensaje += `*Fecha:* ${new Date().toLocaleDateString('es-PE')} ${new Date().toLocaleTimeString('es-PE')}\n\n`;
            mensaje += `*PRODUCTOS PEDIDOS:*\n`;
            
            // Agregar productos al mensaje
            for (const [id, item] of Object.entries(carritoData)) {
                const producto = productosData[id];
                const subtotal = producto.precio * item.cantidad;
                mensaje += `• ${producto.nombre} x${item.cantidad} = S/ ${subtotal.toFixed(2)}\n`;
            }
            
            mensaje += `\n*TOTAL: S/ ${totalPrecio.toFixed(2)}*\n\n`;
            mensaje += `*Estado:* Pendiente de confirmación\n`;
            mensaje += `¡Gracias por elegir Panadería La Tradición! `;
            
            // Número de WhatsApp de la panadería (TU NÚMERO)
            const numeroTuWhatsApp = "51982362999"; // Cambia este número por el tuyo
            
            // Crear URL de WhatsApp
            const whatsappUrl = `https://wa.me/${numeroTuWhatsApp}?text=${encodeURIComponent(mensaje)}`;
            
            // Enviar datos a la API para guardar el pedido
            fetch('carrito_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'finalizar_compra',
                    total: totalPrecio,
                    productos: Object.keys(carritoData).length,
                    cliente_whatsapp: whatsapp,
                    cliente_nombre: nombre,
                    direccion: direccion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Abrir WhatsApp
                    window.open(whatsappUrl, '_blank');
                    
                    // Mostrar mensaje de éxito
                    mostrarNotificacion('¡Pedido enviado! Se abrió WhatsApp para confirmar tu pedido.', 'success');
                    
                    // Cerrar modal
                    cerrarModalWhatsApp();
                    
                    // Redirigir después de 3 segundos
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 3000);
                } else {
                    mostrarNotificacion(data.message || 'Error al procesar el pedido', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
        }
        
        function mostrarNotificacion(mensaje, tipo = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = mensaje;
            notification.className = `notification ${tipo}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }
        
        // Validar entrada del número de WhatsApp
        document.getElementById('whatsappNumber').addEventListener('input', function(e) {
            // Solo permitir números
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>