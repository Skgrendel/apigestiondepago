<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a tu Membresía ACOFICUM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 480px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: #ffffff;
            padding: 30px 20px 20px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .logo {
            max-height: 50px;
            margin-bottom: 15px;
            display: block;
        }

        .content {
            padding: 30px 20px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #003366;
            margin-bottom: 15px;
        }

        .message {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .credentials {
            background: #fafbfc;
            border: 1px solid #e8e8e8;
            border-radius: 4px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .credential-row {
            margin-bottom: 12px;
        }

        .credential-row:last-child {
            margin-bottom: 0;
        }

        .credential-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }

        .credential-value {
            display: block;
            font-size: 15px;
            color: #003366;
            font-family: 'Menlo', 'Monaco', 'Courier', monospace;
            font-weight: 600;
            word-break: break-all;
        }

        .alert {
            background: #fff5f5;
            border-left: 3px solid #d32f2f;
            padding: 12px 14px;
            border-radius: 3px;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .alert strong {
            color: #d32f2f;
        }

        .button {
            display: block;
            background: #d32f2f;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .button:hover {
            background: #b71c1c;
        }

        .support {
            font-size: 13px;
            color: #999;
            padding: 12px;
            background: #fafbfc;
            border-radius: 4px;
        }

        .support a {
            color: #d32f2f;
            text-decoration: none;
        }

        .support a:hover {
            text-decoration: underline;
        }

        .footer {
            background: #fafbfc;
            border-top: 1px solid #f0f0f0;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #999;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .container {
                border-radius: 0;
            }

            .content {
                padding: 20px;
            }

            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="https://acoficum.org/wp-content/uploads/2025/03/cropped-LOGO-SITIO-WEB-ACOFICUM-340x71.png" alt="ACOFICUM" class="logo">
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Hola {{ $firstname }} {{ $lastname }},</p>

            <p class="message">
                ¡Tu membresía ha sido activada exitosamente! A continuación encontrarás tus datos de acceso.
            </p>

            <!-- Credentials Section -->
            <div class="section">
                <div class="section-title">Tus Credenciales</div>
                <div class="credentials">
                    <div class="credential-row">
                        <span class="credential-label">Email</span>
                        <span class="credential-value">{{ $email }}</span>
                    </div>

                    <div class="credential-row">
                        <span class="credential-label">Usuario</span>
                        <span class="credential-value">{{ $username }}</span>
                    </div>

                    <div class="credential-row">
                        <span class="credential-label">Contraseña</span>
                        <span class="credential-value">{{ $password }}</span>
                    </div>
                </div>
            </div>

            <!-- Alert -->
            <div class="alert">
                <strong>Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña en tu primer acceso.
            </div>

            <!-- Membership Info -->
            <div class="section">
                <div class="section-title">Tu Acceso</div>
                <p class="message">
                    Membresía: <strong>{{ $membershipName }}</strong>
                </p>
                <p class="message">
                    Tienes acceso inmediato a todos los contenidos y beneficios de tu membresía.
                </p>
            </div>

            <!-- CTA Button -->
            <a href="{{ $campusUrl }}" class="button">Acceder a tu Membresía</a>

            <!-- Support -->
            <div class="support">
                ¿Necesitas ayuda? Contáctanos en <a href="mailto:contacto@acoficum.org">contacto@acoficum.org</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ now()->year }} ACOFICUM - Asociación Colombiana de Oficiales de Cumplimiento</p>
            <p style="margin-top: 8px;">Este es un correo automático. Por favor no responda a esta dirección.</p>
        </div>
    </div>
</body>
</html>
