<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Campus ACOFICUM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .welcome-text {
            margin-bottom: 30px;
            color: #555;
            line-height: 1.8;
        }

        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }

        .credentials-box h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .credentials-box h3::before {
            content: "🔐";
            margin-right: 10px;
        }

        .credential-item {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .credential-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .credential-label {
            display: block;
            font-weight: 600;
            color: #667eea;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .credential-value {
            display: block;
            font-size: 16px;
            color: #333;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            background-color: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .course-info {
            background-color: #e8f4f8;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }

        .course-info h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .course-info h3::before {
            content: "📚";
            margin-right: 10px;
        }

        .course-info p {
            color: #555;
            margin: 8px 0;
        }

        .instructions {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }

        .instructions h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .instructions h3::before {
            content: "📝";
            margin-right: 10px;
        }

        .instructions ol {
            margin-left: 20px;
            color: #555;
        }

        .instructions li {
            margin-bottom: 8px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 14px 40px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .cta-button-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }

        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .footer p {
            margin: 5px 0;
        }

        .important-note {
            background-color: #f1f3ff;
            border: 1px solid #d4d9f7;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }

        .important-note strong {
            color: #667eea;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .greeting {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎓 ¡Bienvenido!</h1>
            <p>Campus de Asociados ACOFICUM</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hola {{ $firstname }} {{ $lastname }},
            </div>

            <div class="welcome-text">
                <p>¡Estamos muy emocionados de recibirte en nuestro campus virtual!</p>
                <p>Tu cuenta ha sido creada exitosamente y ya estás listo para acceder a todos nuestros cursos y recursos de formación continua.</p>
            </div>

            <!-- Credentials -->
            <div class="credentials-box">
                <h3>Tus Credenciales de Acceso</h3>

                <div class="credential-item">
                    <span class="credential-label">Email</span>
                    <span class="credential-value">{{ $email }}</span>
                </div>

                <div class="credential-item">
                    <span class="credential-label">Usuario (Login)</span>
                    <span class="credential-value">{{ $username }}</span>
                </div>

                <div class="credential-item">
                    <span class="credential-label">Contraseña</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            <!-- Important Note -->
            <div class="important-note">
                <strong>⚠️ Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña en tu primer acceso. Guarda estos datos en un lugar seguro.
            </div>

            <!-- Course Info -->
            <div class="course-info">
                <h3>Curso Inscrito</h3>
                <p><strong>{{ $courseName }}</strong></p>
                <p>Ya estás inscrito en este curso y puedes comenzar a acceder al contenido de inmediato.</p>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>Cómo Acceder</h3>
                <ol>
                    <li>Haz clic en el botón "Ir al Campus" a continuación</li>
                    <li>Inicia sesión con tu usuario y contraseña</li>
                    <li>Navega a tu curso inscrito</li>
                    <li>¡Comienza tu experiencia de aprendizaje!</li>
                </ol>
            </div>

            <!-- CTA Button -->
            <div class="cta-button-wrapper">
                <a href="{{ $campusUrl }}" class="cta-button">→ Ir al Campus</a>
            </div>

            <!-- Additional Info -->
            <div style="margin-bottom: 30px; color: #555; line-height: 1.8;">
                <p><strong>¿Tienes problemas para acceder?</strong></p>
                <p>Si experimentas dificultades para iniciar sesión o necesitas ayuda, no dudes en contactarnos a través de nuestro correo de soporte.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>ACOFICUM - Asociación Colombiana de Oficiales de Cumplimiento</strong></p>
            <p>Este es un correo automático, por favor no responda a esta dirección.</p>
            <p style="margin-top: 10px; color: #999;">© {{ now()->year }} ACOFICUM. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
