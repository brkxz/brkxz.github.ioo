<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>La Tradición - Panadería</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
  <style>
    /* Estilos generales */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }
    body, html {
      height: 100%;
      background: #f9f5f0;
      color: #4b2e05;
      overflow-x: hidden;
    }
    video#videoFondo {
      position: fixed;
      right: 0;
      bottom: 0;
      min-width: 100%;
      min-height: 100%;
      object-fit: cover;
      z-index: -1;
      filter: brightness(0.6);
    }
    header {
      background: rgba(255,255,255,0.85);
      padding: 1rem 2rem;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
      position: relative;
      z-index: 20;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    header h1 {
      font-size: 3rem;
      font-weight: 700;
      color: #d2691e;
      letter-spacing: 2px;
      font-family: 'Georgia', serif;
      margin-bottom: 0;
    }
    nav {
      text-align: right;
    }
    nav a {
      margin-left: 20px;
      text-decoration: none;
      color: #d2691e;
      font-weight: 600;
      font-size: 1rem;
      transition: color 0.3s ease;
    }
    nav a:hover {
      color: #b65c1a;
    }
    .btn-login {
      padding: 8px 25px;
      font-size: 1rem;
      font-weight: 700;
      color: white;
      background-color: #d2691e;
      border-radius: 40px;
      text-decoration: none;
      box-shadow: 0 5px 15px rgba(210,105,30,0.5);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      margin-left: 20px;
    }
    .btn-login:hover {
      background-color: #b65c1a;
      box-shadow: 0 8px 25px rgba(182,92,26,0.7);
    }

    /* Slider */
    .slider {
      position: relative;
      max-width: 900px;
      margin: 1rem auto 2rem;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.3);
      overflow: hidden;
      background: #fff;
      z-index: 10;
    }
    .slides {
      display: flex;
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
      width: 100%;
    }
    .slides img {
      width: 100%;
      height: 450px;
      object-fit: cover;
      flex-shrink: 0;
      border-radius: 20px;
      user-select: none;
    }
    .slider-nav button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(210, 105, 30, 0.8);
      border: none;
      color: white;
      font-size: 2.8rem;
      padding: 0 18px;
      border-radius: 50%;
      cursor: pointer;
      user-select: none;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 15px rgba(182,92,26,0.7);
      z-index: 15;
    }
    .slider-nav button:hover {
      background-color: #b65c1a;
    }
    .slider-nav #prev {
      left: 15px;
    }
    .slider-nav #next {
      right: 15px;
    }
    .slider-dots {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 12px;
      user-select: none;
      z-index: 15;
    }
    .slider-dots button {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      border: none;
      background-color: rgba(210,105,30,0.6);
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .slider-dots button.active,
    .slider-dots button:hover {
      background-color: #d2691e;
      box-shadow: 0 0 8px rgba(210,105,30,0.8);
    }

    main {
      max-width: 1000px;
      margin: 0 auto 3rem;
      background: rgba(255,255,255,0.95);
      padding: 2.5rem 3rem;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      text-align: center;
      z-index: 10;
      position: relative;
    }
    main h2 {
      font-size: 2.4rem;
      margin-bottom: 1rem;
      color: #8B4513;
      font-weight: 600;
    }
    main p {
      font-size: 1.15rem;
      line-height: 1.9;
      color: #5a3b0e;
      margin-bottom: 2rem;
      text-align: justify;
    }
    .galeria {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-bottom: 2rem;
    }
    .galeria img {
      width: 220px;
      border-radius: 15px;
      box-shadow: 0 5px 18px rgba(0,0,0,0.25);
      transition: transform 0.3s ease;
      cursor: pointer;
    }
    .galeria img:hover {
      transform: scale(1.05);
    }
    .testimonios {
      font-style: italic;
      font-size: 1.1rem;
      color: #6b4a17;
      margin-bottom: 2rem;
    }
    .contacto {
      font-size: 1.1rem;
      color: #6b4a17;
    }
    .contacto a {
      color: #d2691e;
      font-weight: 600;
      text-decoration: none;
    }
    .contacto a:hover {
      color: #b65c1a;
      text-decoration: underline;
    }

    footer {
      text-align: center;
      padding: 1.2rem;
      background-color: rgba(255, 255, 255, 0.85);
      color: #8b5a1a;
      font-weight: 500;
      letter-spacing: 1.2px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
      user-select: none;
    }

    @media (max-width: 800px) {
      header {
        flex-direction: column;
        align-items: flex-start;
      }
      nav {
        margin-top: 10px;
        width: 100%;
        text-align: center;
      }
      nav a, .btn-login {
        margin-left: 12px;
        font-size: 0.9rem;
      }
      .slides img {
        height: 280px;
      }
      main {
        padding: 2rem 1.5rem;
      }
      header h1 {
        font-size: 2rem;
        margin-bottom: 10px;
      }
      .btn-login {
        margin-left: 10px;
        padding: 10px 20px;
      }
    }
  </style>
</head>
<body>

  <video autoplay muted loop id="videoFondo">
    <source src="fondoo.mp4" type="video/mp4" />
  </video>

  <header>
    <h1>La Tradición</h1>
    <nav aria-label="Menú principal">
      <a href="#nosotros">Nosotros</a>
      <a href="#productos">Productos</a>
      <a href="#galeria">Galería</a>
      <a href="#contacto">Contacto</a>
      <a href="login.php" class="btn-login" style="margin-left: 30px;">Iniciar Sesión</a>
      <a href="registro.php" class="btn-login">Registrarse</a>
    </nav>
  </header>

  <div class="slider" role="region" aria-label="Galería de productos destacados">
    <div class="slides">
      <img src="pan.jpg" alt="Pan dulce" />
      <img src="bollos02.jpg" alt="Bollo casero" />
      <img src="pastel especial.jpg" alt="Pastel especial" />
      <img src="empanada.jpg" alt="Empanada" />
      <img src="concha.jpg" alt="Concha" />
    </div>
    <div class="slider-nav">
      <button id="prev" aria-label="Slide anterior">&#10094;</button>
      <button id="next" aria-label="Slide siguiente">&#10095;</button>
    </div>
    <div class="slider-dots" aria-label="Navegación del slider"></div>
  </div>

  <main>
    <section id="nosotros">
      <h2>Bienvenidos a Panadería La Tradición</h2>
      <p>
        Desde 2018, Panadería La Tradición ha sido el corazón de nuestro barrio. Fundada por Don Manuel, esta panadería familiar ha pasado de generación en generación, manteniendo recetas artesanales, ingredientes de calidad y mucho amor por el pan.<br /><br />
        Nos especializamos en conchas, bolillos, empanadas, pasteles y mucho más, siempre frescos y listos para compartir. Cada día, nuestra pasión es llevar el mejor sabor a tu mesa, conservando la tradición que nos caracteriza.
      </p>
    </section>

    <section id="productos">
      <h2>Nuestros Productos Destacados</h2>
      <p>Consulta la galería de imágenes y nuestros productos más populares.</p>
    </section>

    <section id="galeria">
      <h2>Galería de Sabores</h2>
      <div class="galeria">
        <img src="pan.jpg" alt="Pan dulce" />
        <img src="01.jpg" alt="Bollos caseros" />
        <img src="pasteles.jpg" alt="Pasteles" />
      </div>
    </section>

    <section id="contacto" class="contacto">
      <h2>Contáctanos</h2>
      <p>📱 WhatsApp: <a href="https://wa.me/51982362999" target="_blank" rel="noopener noreferrer">+51 982362999</a></p>
      <p>📞 Teléfono: <a href="tel:+51982362999">+51 982362999</a></p>
      <p>📧 Email: <a href="mailto:contacto@panaderialatradicion.com">contacto@panaderialatradicion.com</a></p>
      <p>📸 Instagram: <a href="https://instagram.com/panaderialatradicion" target="_blank" rel="noopener noreferrer">@panaderialatradicion</a></p>
      <p>📘 Facebook: <a href="https://facebook.com/panaderialatradicion" target="_blank" rel="noopener noreferrer">Panadería La Tradición</a></p>
    </section>

    <section class="testimonios">
      <h2>Lo que dicen nuestros clientes</h2>
      <p>“El mejor pan del pueblo, ¡como el de mi abuela!” – Carla M.</p>
      <p>“Siempre calientito, siempre delicioso.” – Javier R.</p>
    </section>
  </main>

  <footer>
    &copy; 2025 Panadería La Tradición · Todos los derechos reservados
  </footer>

  <script>
    const slides = document.querySelector('.slides');
    const images = document.querySelectorAll('.slides img');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const dotsContainer = document.querySelector('.slider-dots');

    let index = 0;
    const total = images.length;

    // Crear puntos indicadores
    for(let i = 0; i < total; i++) {
      const dot = document.createElement('button');
      dot.setAttribute('aria-label', `Slide ${i + 1}`);
      if(i === 0) dot.classList.add('active');
      dot.addEventListener('click', () => {
        showSlide(i);
      });
      dotsContainer.appendChild(dot);
    }
    const dots = dotsContainer.querySelectorAll('button');

    function showSlide(i) {
      if (i < 0) index = total - 1;
      else if (i >= total) index = 0;
      else index = i;
      slides.style.transform = 'translateX(' + (-index * 100) + '%)';

      dots.forEach(dot => dot.classList.remove('active'));
      dots[index].classList.add('active');
    }

    prevBtn.addEventListener('click', () => {
      showSlide(index - 1);
    });

    nextBtn.addEventListener('click', () => {
      showSlide(index + 1);
    });

    // Avance automático
    setInterval(() => {
      showSlide(index + 1);
    }, 5000);
  </script>
</body>
</html>
