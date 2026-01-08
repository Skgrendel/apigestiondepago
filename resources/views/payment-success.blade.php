<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Confirmado - ACOFICUM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f8f9fa;
        }

        /* Header/Logo */
        .top-bar {
            display: none;
        }

        /* Navbar */
        .navbar {
            display: none;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #003366 0%, #004d99 100%);
            color: white;
            padding: 40px 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><pattern id="pattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="1200" height="600" fill="url(%23pattern)"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .status-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 30px;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .checkmark {
            display: none;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .hero h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 0;
            opacity: 0.95;
        }

        /* Main Content */
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .info-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .info-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e8e8e8;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .info-card .icon {
            display: none;
        }

        .info-card h3 {
            color: #003366;
            font-size: 18px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .info-card p {
            color: #555;
            font-size: 14px;
            line-height: 1.8;
        }

        /* Alert Box */
        .alert-box {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .alert-box h3 {
            color: #003366;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            font-size: 16px;
        }

        .alert-box h3::before {
            content: '⚠️';
            margin-right: 10px;
            font-size: 20px;
        }

        .alert-box p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Buttons */
        .button-section {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 50px 0;
        }

        .btn {
            padding: 16px 40px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            min-width: 250px;
            text-align: center;
        }

        .btn-primary {
            background: #d32f2f;
            color: white;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
        }

        .btn-primary:hover {
            background: #b71c1c;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(211, 47, 47, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #003d99;
            border: 2px solid #003d99;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: #f0f4f8;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Footer */
        footer {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
                gap: 15px;
            }

            .navbar a {
                font-size: 11px;
                padding: 10px 0;
            }

            .navbar .btn-campus {
                margin-left: 0;
            }

            .hero {
                padding: 50px 20px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 16px;
            }

            .button-section {
                flex-direction: column;
            }

            .btn {
                min-width: 100%;
            }

            .info-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <div class="status-badge">✓ Pago Confirmado</div>

            <div class="checkmark">✓</div>

            <h1>¡Tu Pago está Siendo Procesado!</h1>

            <p>
                Tu transacción se está procesando en este momento. En los próximos momentos recibirás un correo con toda la información que necesitas para acceder a tu membresía.
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Info Cards -->
        <div class="info-section">
            <div class="info-card">
                <h3>Correo en Camino</h3>
                <p>Recibirás un email con tus datos de acceso al espacio de miembros en los próximos momentos.</p>
            </div>

            <div class="info-card">
                <h3>Credenciales Seguras</h3>
                <p>Tu usuario y contraseña han sido generados automáticamente con máximos estándares de seguridad.</p>
            </div>

            <div class="info-card">
                <h3>Acceso Inmediato</h3>
                <p>Una vez confirmes el correo, tendrás acceso instantáneo a todos los contenidos y beneficios de tu membresía.</p>
            </div>
        </div>

        <!-- Alert -->
        <div class="alert-box">
            <h3>Revisa tu Spam</h3>
            <p>Si no ves el correo en tu bandeja principal, revisa la carpeta de <strong>Spam</strong> o <strong>Promociones</strong>. A veces los correos pueden llegar allí.</p>
        </div>

        <!-- CTA Buttons -->
        <div class="button-section">
            <a href="https://campus.asociados.acoficum.org" class="btn btn-primary">
                → Ir al Campus
            </a>
            <a href="https://acoficum.org" class="btn btn-secondary">
                ← Volver al Inicio
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p><strong>ACOFICUM</strong> - Asociación Colombiana de Oficiales de Cumplimiento</p>
        <p>Para soporte: <a href="mailto:contacto@acoficum.org">contacto@acoficum.org</a></p>
        <p style="margin-top: 15px; color: #666;">© {{ now()->year }} ACOFICUM. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
