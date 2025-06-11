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

// Productos de panadería (simulando base de datos)
$productos = [
    [
        'id' => 1,
        'nombre' => 'Pan Dulce Tradicional',
        'precio' => 2.50,
        'descripcion' => 'Pan dulce artesanal con ingredientes tradicionales',
        'imagen' => 'pan.jpg',
        'categoria' => 'Pan Dulce'
    ],
    [
        'id' => 2,
        'nombre' => 'Bolillos Caseros',
        'precio' => 0.50,
        'descripcion' => 'Bolillos frescos horneados diariamente',
        'imagen' => 'bollos02.jpg',
        'categoria' => 'Pan Salado'
    ],
    [
        'id' => 3,
        'nombre' => 'Empanadas de Pollo',
        'precio' => 3.00,
        'descripcion' => 'Empanadas rellenas de pollo con especias',
        'imagen' => 'empanada.jpg',
        'categoria' => 'Empanadas'
    ],
    [
        'id' => 4,
        'nombre' => 'Conchas de Vainilla',
        'precio' => 1.50,
        'descripcion' => 'Tradicionales conchas mexicanas de vainilla',
        'imagen' => 'concha.jpg',
        'categoria' => 'Pan Dulce'
    ],
    [
        'id' => 5,
        'nombre' => 'Pastel Especial',
        'precio' => 25.00,
        'descripcion' => 'Pastel decorado para ocasiones especiales',
        'imagen' => 'pastel especial.jpg',
        'categoria' => 'Pasteles'
    ],
    [
        'id' => 6,
        'nombre' => 'Pan Integral',
        'precio' => 4.00,
        'descripcion' => 'Pan integral con semillas y cereales',
        'imagen' => 'pan.jpg',
        'categoria' => 'Pan Integral'
    ]
];

// Obtener categoría seleccionada
$categoria_filtro = $_GET['categoria'] ?? 'todos';
$productos_filtrados = $categoria_filtro === 'todos' ? $productos : 
    array_filter($productos, function($p) use ($categoria_filtro) {
        return strtolower($p['categoria']) === strtolower($categoria_filtro);
    });

$categorias = array_unique(array_column($productos, 'categoria'));

// Calcular totales del carrito
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
    <title>Catálogo - Panadería La Tradición</title>
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
        
        .carrito-btn {
            background: #d2691e;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            position: relative;
        }
        
        .carrito-btn:hover {
            background: #b65c1a;
            transform: scale(1.05);
        }
        
        .carrito-counter {
            background: #fff;
            color: #d2691e;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
        }
        
        .welcome {
            color: #8B4513;
            font-weight: 600;
        }
        
        .logout-btn {
            background: #8B4513;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #654321;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .filters h3 {
            color: #8B4513;
            margin-bottom: 1rem;
        }
        
        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 8px 16px;
            background: #f8f5f0;
            color: #8B4513;
            border: 2px solid #d2691e;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: #d2691e;
            color: white;
        }
        
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .producto-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }
        
        .producto-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .producto-info {
            padding: 1.5rem;
        }
        
        .producto-categoria {
            background: #d2691e;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .producto-nombre {
            font-size: 1.3rem;
            font-weight: 700;
            color: #8B4513;
            margin-bottom: 0.5rem;
        }
        
        .producto-precio {
            font-size: 1.5rem;
            font-weight: 700;
            color: #d2691e;
            margin-bottom: 0.5rem;
        }
        
        .producto-descripcion {
            color: #6b4a17;
            line-height: 1.4;
            margin-bottom: 1rem;
        }
        
        .producto-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .cantidad-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .cantidad-input {
            width: 50px;
            padding: 5px;
            border: 2px solid #d2691e;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }
        
        .add-cart-btn {
            background: #d2691e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }
        
        .add-cart-btn:hover {
            background: #b65c1a;
            transform: scale(1.02);
        }
        
        .add-cart-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .empty-message {
            text-align: center;
            color: #8B4513;
            font-size: 1.2rem;
            margin-top: 3rem;
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
            z-index: 1000;
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
            
            .header-nav {
                order: -1;
                width: 100%;
                justify-content: center;
            }
            
            .filter-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .productos-grid {
                grid-template-columns: 1fr;
            }
            
            .producto-actions {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🥖 Panadería La Tradición</h1>
        <div class="header-nav">
            <a href="carrito.php" class="carrito-btn" id="carritoBtn">
                🛒 Mi Carrito
                <span class="carrito-counter" id="carritoCounter"><?php echo $total_productos; ?></span>
                <small style="margin-left: 10px;">S/ <?php echo number_format($total_precio, 2); ?></small>
            </a>
            <span class="welcome">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user']); ?>!</span>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="filters">
            <h3>Filtrar por Categoría</h3>
            <div class="filter-buttons">
                <a href="?categoria=todos" class="filter-btn <?php echo $categoria_filtro === 'todos' ? 'active' : ''; ?>">
                    Todos los Productos
                </a>
                <?php foreach ($categorias as $cat): ?>
                    <a href="?categoria=<?php echo urlencode($cat); ?>" 
                       class="filter-btn <?php echo strtolower($categoria_filtro) === strtolower($cat) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($productos_filtrados)): ?>
            <div class="empty-message">
                <p>No hay productos disponibles en esta categoría.</p>
            </div>
        <?php else: ?>
            <div class="productos-grid">
                <?php foreach ($productos_filtrados as $producto): ?>
                    <div class="producto-card">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                             class="producto-img"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlbiBubyBkaXNwb25pYmxlPC90ZXh0Pjwvc3ZnPg=='">
                        <div class="producto-info">
                            <span class="producto-categoria"><?php echo htmlspecialchars($producto['categoria']); ?></span>
                            <h3 class="producto-nombre"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <div class="producto-precio">S/ <?php echo number_format($producto['precio'], 2); ?></div>
                            <p class="producto-descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <div class="producto-actions">
                                <div class="cantidad-selector">
                                    <label>Cant:</label>
                                    <input type="number" min="1" max="99" value="1" 
                                           class="cantidad-input" id="cantidad-<?php echo $producto['id']; ?>">
                                </div>
                                <button class="add-cart-btn" onclick="agregarAlCarrito(<?php echo $producto['id']; ?>)">
                                    🛒 Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Notificación -->
    <div class="notification" id="notification"></div>

    <script>
        function agregarAlCarrito(productoId) {
            const cantidadInput = document.getElementById(`cantidad-${productoId}`);
            const cantidad = parseInt(cantidadInput.value) || 1;
            
            if (cantidad < 1 || cantidad > 99) {
                mostrarNotificacion('Cantidad inválida (1-99)', 'error');
                return;
            }
            
            // Deshabilitar botón temporalmente
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '⏳ Agregando...';
            
            // Enviar petición AJAX
            fetch('carrito_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'agregar',
                    producto_id: productoId,
                    cantidad: cantidad
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador del carrito
                    document.getElementById('carritoCounter').textContent = data.carrito_total;
                    
                    // Actualizar precio total en el botón del carrito
                    const carritoBtn = document.getElementById('carritoBtn');
                    const smallElement = carritoBtn.querySelector('small');
                    if (smallElement) {
                        smallElement.textContent = `S/ ${data.precio_total.toFixed(2)}`;
                    }
                    
                    // Resetear cantidad a 1
                    cantidadInput.value = 1;
                    
                    // Mostrar notificación de éxito
                    mostrarNotificacion('¡Producto agregado al carrito!', 'success');
                    
                    // Efecto visual en el botón del carrito
                    const carritoCounter = document.getElementById('carritoCounter');
                    carritoCounter.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        carritoCounter.style.transform = 'scale(1)';
                    }, 200);
                    
                } else {
                    mostrarNotificacion(data.message || 'Error al agregar producto', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            })
            .finally(() => {
                // Rehabilitar botón
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
        
        function mostrarNotificacion(mensaje, tipo = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = mensaje;
            notification.className = `notification ${tipo}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Animación de entrada para las cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.producto-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s, transform 0.5s';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Actualizar contador del carrito periódicamente
            setInterval(actualizarCarrito, 30000); // cada 30 segundos
        });
        
        function actualizarCarrito() {
            fetch('carrito_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'obtener'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('carritoCounter').textContent = data.total_productos;
                    const smallElement = document.getElementById('carritoBtn').querySelector('small');
                    if (smallElement) {
                        smallElement.textContent = `S/ ${data.total_precio.toFixed(2)}`;
                    }
                }
            })
            .catch(error => {
                console.error('Error actualizando carrito:', error);
            });
        }
        
        // Validar cantidad en inputs
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('cantidad-input')) {
                let value = parseInt(e.target.value);
                if (value < 1) e.target.value = 1;
                if (value > 99) e.target.value = 99;
            }
        });
    </script>
</body>
</html>