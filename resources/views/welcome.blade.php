<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Portal de Pagos - ACOFICUM</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                line-height: 1.6;
                color: #333;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 40px 20px;
            }

            header {
                background: white;
                padding: 20px 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 60px;
                position: relative;
                z-index: 10;
            }

            .header-content {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .logo {
                max-height: 50px;
                display: block;
            }

            .nav {
                display: flex;
                gap: 30px;
                align-items: center;
            }

            .nav a {
                color: #003366;
                text-decoration: none;
                font-weight: 500;
                font-size: 14px;
                transition: color 0.3s;
            }

            .nav a:hover {
                color: #d32f2f;
            }

            .hero {
                background: white;
                border-radius: 12px;
                padding: 80px 40px;
                text-align: center;
                margin-bottom: 40px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .hero h1 {
                font-size: 42px;
                color: #003366;
                margin-bottom: 20px;
                font-weight: 700;
            }

            .hero p {
                font-size: 18px;
                color: #666;
                margin-bottom: 30px;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            .features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                margin-bottom: 40px;
            }

            .feature-card {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                transition: transform 0.3s, box-shadow 0.3s;
                border-top: 4px solid #d32f2f;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            }

            .feature-icon {
                font-size: 40px;
                margin-bottom: 15px;
                display: block;
            }

            .feature-card h3 {
                font-size: 20px;
                color: #003366;
                margin-bottom: 15px;
                font-weight: 600;
            }

            .feature-card p {
                color: #666;
                font-size: 14px;
                line-height: 1.7;
            }

            .info-section {
                background: white;
                padding: 50px 40px;
                border-radius: 12px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                margin-bottom: 40px;
            }

            .info-section h2 {
                font-size: 28px;
                color: #003366;
                margin-bottom: 30px;
                font-weight: 700;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 30px;
            }

            .info-item {
                padding: 20px;
                border-left: 4px solid #d32f2f;
                background: #f9f9f9;
                border-radius: 4px;
            }

            .info-item h4 {
                color: #d32f2f;
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 10px;
            }

            .info-item p {
                color: #666;
                font-size: 14px;
                line-height: 1.6;
            }

            .cta-section {
                background: linear-gradient(135deg, #003366 0%, #004d99 100%);
                padding: 60px 40px;
                border-radius: 12px;
                color: white;
                text-align: center;
                margin-bottom: 40px;
            }

            .cta-section h2 {
                font-size: 32px;
                margin-bottom: 15px;
                font-weight: 700;
            }

            .cta-section p {
                font-size: 16px;
                margin-bottom: 30px;
                opacity: 0.95;
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }

            .btn {
                display: inline-block;
                padding: 14px 40px;
                background: #d32f2f;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
                transition: background 0.3s, transform 0.2s;
                border: none;
                cursor: pointer;
                font-size: 16px;
            }

            .btn:hover {
                background: #b71c1c;
                transform: translateY(-2px);
            }

            .btn-secondary {
                background: white;
                color: #003366;
            }

            .btn-secondary:hover {
                background: #f5f5f5;
            }

            footer {
                background: white;
                padding: 40px 20px;
                border-top: 1px solid #e0e0e0;
                text-align: center;
                color: #999;
                font-size: 13px;
            }

            .benefits {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
                margin: 30px 0;
            }

            .benefit-item {
                display: flex;
                gap: 15px;
                padding: 15px 0;
            }

            .benefit-check {
                color: #d32f2f;
                font-weight: 700;
                flex-shrink: 0;
            }

            .benefit-text {
                color: #666;
                font-size: 14px;
            }

            @media (max-width: 768px) {
                .hero {
                    padding: 40px 20px;
                }

                .hero h1 {
                    font-size: 28px;
                }

                .hero p {
                    font-size: 16px;
                }

                .info-section {
                    padding: 30px 20px;
                }

                .cta-section {
                    padding: 40px 20px;
                }

                .cta-section h2 {
                    font-size: 24px;
                }

                .nav {
                    gap: 15px;
                }

                .header-content {
                    flex-direction: column;
                    gap: 20px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Header -->
        <header>
            <div class="header-content">
                <img src="https://acoficum.org/wp-content/uploads/2025/03/cropped-LOGO-SITIO-WEB-ACOFICUM-340x71.png" alt="ACOFICUM" class="logo">
                <nav class="nav">
                    <a href="https://acoficum.org">Inicio</a>
                    <a href="https://acoficum.org/contacto">Contacto</a>
                    <a href="https://acoficum.org/ayuda">Ayuda</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <div class="container">
            <!-- Hero Section -->
            <div class="hero">
                <h1>Portal de Pagos ACOFICUM</h1>
                <p>Gestiona tus pagos de forma segura y eficiente. Accede a tu membresía de ACOFICUM con solo unos clicks.</p>
            </div>

            <!-- Features Section -->
            <div class="features">
                <div class="feature-card">
                    <span class="feature-icon">🔒</span>
                    <h3>Pago Seguro</h3>
                    <p>Tus transacciones están protegidas con tecnología de encriptación de nivel bancario. Realiza pagos con confianza.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">⚡</span>
                    <h3>Acceso Inmediato</h3>
                    <p>Una vez confirmado tu pago, recibe tus credenciales de acceso al instante y accede a tu membresía sin demoras.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📧</span>
                    <h3>Confirmación por Email</h3>
                    <p>Recibirás un email con todos los detalles de tu transacción y tus datos de acceso de forma automática.</p>
                </div>
            </div>

            <!-- Information Section -->
            <div class="info-section">
                <h2>¿Cómo Funciona?</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <h4>1. Selecciona tu Plan</h4>
                        <p>Elige el plan de membresía que mejor se adapte a tus necesidades.</p>
                    </div>
                    <div class="info-item">
                        <h4>2. Realiza el Pago</h4>
                        <p>Completa tu pago de forma segura a través de nuestro portal de pagos.</p>
                    </div>
                    <div class="info-item">
                        <h4>3. Recibe Credenciales</h4>
                        <p>Obtén tus datos de acceso por correo electrónico inmediatamente después de confirmar el pago.</p>
                    </div>
                    <div class="info-item">
                        <h4>4. Accede a tu Membresía</h4>
                        <p>Inicia sesión en el campus y comienza a disfrutar de todos los beneficios de tu membresía.</p>
                    </div>
                </div>
            </div>

            <!-- Benefits Section -->
            <div class="info-section">
                <h2>Beneficios de tu Membresía</h2>
                <div class="benefits">
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <span class="benefit-text">Acceso completo a todos los contenidos y recursos</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <span class="benefit-text">Capacitaciones y webinars exclusivos</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <span class="benefit-text">Soporte técnico y orientación personalizada</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <span class="benefit-text">Acceso a la comunidad de asociados</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <span class="benefit-text">Actualizaciones continuas de contenido</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <span class="benefit-text">Certificados al completar cursos</span>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h2>¿Listo para Comenzar?</h2>
                <p>Únete a miles de asociados de ACOFICUM que ya disfrutan de los beneficios de su membresía. El acceso es rápido, seguro y completamente en línea.</p>
                <a href="https://acoficum.org/planes" class="btn">Selecciona tu Plan</a>
            </div>

            <!-- Contact Section -->
            <div class="info-section">
                <h2>¿Necesitas Ayuda?</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <h4>Soporte al Cliente</h4>
                        <p>Contáctanos en <strong>contacto@acoficum.org</strong> para cualquier pregunta sobre tu membresía o pago.</p>
                    </div>
                    <div class="info-item">
                        <h4>Preguntas Frecuentes</h4>
                        <p>Visita nuestra sección de <strong>Preguntas Frecuentes</strong> para encontrar respuestas rápidas.</p>
                    </div>
                    <div class="info-item">
                        <h4>Chat en Vivo</h4>
                        <p>Nuestro equipo de soporte está disponible a través del chat para asistirte en tiempo real.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2026 ACOFICUM - Asociación Colombiana de Oficiales de Cumplimiento. Todos los derechos reservados.</p>
            <p>Portal de Pagos Seguro | Términos de Servicio | Política de Privacidad</p>
        </footer>
    </body>
</html>
