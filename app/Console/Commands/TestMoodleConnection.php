<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestMoodleConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la conexión y configuración con Moodle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Probando conexión con Moodle...\n');

        $moodleUrl = env('MOODLE_URL');
        $moodleToken = env('MOODLE_WS_TOKEN');
        $moodleCourseId = env('MOODLE_COUSE_ID', 2);

        $this->info('📋 Configuración:');
        $this->table(
            ['Variable', 'Valor'],
            [
                ['MOODLE_URL', $moodleUrl],
                ['MOODLE_WS_TOKEN', $moodleToken ? '***' . substr($moodleToken, -8) : 'NO CONFIGURADO'],
                ['MOODLE_COUSE_ID', $moodleCourseId],
                ['MOODLE_TIMEOUT', env('MOODLE_TIMEOUT', 30)],
            ]
        );

        // Test 1: Validar configuración
        if (!$moodleUrl || !$moodleToken) {
            $this->error("\n❌ Configuración incompleta en .env");
            return Command::FAILURE;
        }

        // Test 2: Conectar a Moodle
        $this->info("\n📡 Conectando a Moodle...");

        try {
            $response = Http::timeout(30)
                ->get($moodleUrl, [
                    'wstoken' => $moodleToken,
                    'wsfunction' => 'core_webservice_get_site_info',
                    'moodlewsrestformat' => 'json',
                ]);

            if ($response->failed()) {
                $this->error("❌ Error HTTP: " . $response->status());
                $this->error("Respuesta: " . $response->body());
                return Command::FAILURE;
            }

            $result = $response->json();

            if (isset($result['exception'])) {
                $this->error("❌ Error de Moodle:");
                $this->error("   Código: " . ($result['errorcode'] ?? 'unknown'));
                $this->error("   Mensaje: " . ($result['message'] ?? 'Sin mensaje'));
                return Command::FAILURE;
            }

            $this->info("✅ Conexión exitosa a Moodle\n");

            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Site Name', $result['sitename'] ?? 'N/A'],
                    ['Release', $result['release'] ?? 'N/A'],
                    ['User ID', $result['userid'] ?? 'N/A'],
                    ['Username', $result['username'] ?? 'N/A'],
                ]
            );

        } catch (\Exception $e) {
            $this->error("\n❌ Exception: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Test 3: Verificar curso
        $this->info("\n📚 Verificando Curso ID {$moodleCourseId}...");

        try {
            $response = Http::timeout(30)
                ->get($moodleUrl, [
                    'wstoken' => $moodleToken,
                    'wsfunction' => 'core_course_get_courses',
                    'moodlewsrestformat' => 'json',
                    'options[ids][0]' => $moodleCourseId,
                ]);

            $result = $response->json();

            if (isset($result['exception'])) {
                $this->error("❌ Curso no encontrado");
                $this->error("   Mensaje: " . ($result['message'] ?? 'Error desconocido'));
                return Command::FAILURE;
            }

            if (empty($result)) {
                $this->error("❌ Curso ID {$moodleCourseId} no existe");
                return Command::FAILURE;
            }

            $course = $result[0];
            $this->info("✅ Curso encontrado\n");

            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $course['id']],
                    ['Nombre', $course['fullname']],
                    ['Shortname', $course['shortname']],
                    ['Status', $course['visible'] ? 'Visible' : 'Oculto'],
                ]
            );

        } catch (\Exception $e) {
            $this->error("❌ Error verificando curso: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Test 4: Verificar métodos de enrollment
        $this->info("\n🔓 Verificando métodos de enrollment...");

        try {
            $response = Http::timeout(30)
                ->get($moodleUrl, [
                    'wstoken' => $moodleToken,
                    'wsfunction' => 'core_enrol_get_course_enrolment_methods',
                    'moodlewsrestformat' => 'json',
                    'courseid' => $moodleCourseId,
                ]);

            $result = $response->json();

            if (isset($result['exception'])) {
                $this->warn("⚠️  Error obteniendo métodos de enrollment");
            } elseif (empty($result)) {
                $this->warn("⚠️  No hay métodos de enrollment configurados");
                $this->info("   SOLUCIÓN: Ve a Moodle → Curso → Enrollment methods → Add 'Manual enrollment'");
            } else {
                $this->info("✅ Métodos de enrollment encontrados\n");

                $methods = [];
                foreach ($result as $method) {
                    $methods[] = [
                        $method['name'] ?? $method['type'],
                        $method['status'] ?? 'active',
                    ];
                }

                $this->table(['Método', 'Estado'], $methods);

                // Buscar manual enrollment
                $hasManual = collect($result)->some(fn($m) => $m['type'] === 'manual');
                if (!$hasManual) {
                    $this->warn("\n⚠️  'Manual enrollment' no está habilitado");
                    $this->info("   Esto es REQUERIDO para enrollar usuarios automáticamente");
                }
            }

        } catch (\Exception $e) {
            $this->warn("⚠️  No se pudo verificar métodos: " . $e->getMessage());
        }

        // Test 5: Probar crear usuario con contraseña segura
        $this->info("\n👤 Probando crear un usuario de prueba...");

        $testEmail = 'moodletest' . time() . '@test.local';
        $testUsername = 'testuser' . time();
        $testPassword = $this->generateSecurePassword(); // Contraseña segura

        try {
            $response = Http::timeout(30)
                ->asForm()
                ->post($moodleUrl . '?wstoken=' . $moodleToken . '&wsfunction=core_user_create_users&moodlewsrestformat=json', [
                    'users' => [
                        [
                            'username' => $testUsername,
                            'password' => $testPassword,
                            'firstname' => 'Test',
                            'lastname' => 'Usuario',
                            'email' => $testEmail,
                            'lang' => 'es',
                        ]
                    ]
                ]);

            $result = $response->json();

            if (isset($result['exception'])) {
                $this->error("❌ Error creando usuario:");
                $this->error("   Código: " . ($result['errorcode'] ?? 'unknown'));
                $this->error("   Mensaje: " . ($result['message'] ?? 'Sin mensaje'));
                return Command::FAILURE;
            }

            if (!empty($result[0]['id'])) {
                $moodleUserId = $result[0]['id'];
                $this->info("✅ Usuario de prueba creado exitosamente");
                $this->table(
                    ['Campo', 'Valor'],
                    [
                        ['Moodle User ID', $moodleUserId],
                        ['Username', $testUsername],
                        ['Email', $testEmail],
                        ['Contraseña', $testPassword],
                    ]
                );

                // Step 6: Enrollar usuario en el curso
                $this->info("\n📝 Enrollando usuario en el curso ID {$moodleCourseId}...");

                try {
                    $enrollResponse = Http::timeout(30)
                        ->asForm()
                        ->post($moodleUrl . '?wstoken=' . $moodleToken . '&wsfunction=enrol_manual_enrol_users&moodlewsrestformat=json', [
                            'enrolments' => [
                                [
                                    'roleid' => 5, // Student role
                                    'userid' => $moodleUserId,
                                    'courseid' => $moodleCourseId,
                                ]
                            ]
                        ]);

                    $enrollResult = $enrollResponse->json();

                    if (isset($enrollResult['exception'])) {
                        $this->warn("⚠️  Error enrollando usuario:");
                        $this->warn("   Código: " . ($enrollResult['errorcode'] ?? 'unknown'));
                        $this->warn("   Mensaje: " . ($enrollResult['message'] ?? 'Sin mensaje'));
                    } else {
                        $this->info("✅ Usuario enrollado en el curso exitosamente");
                    }

                } catch (\Exception $e) {
                    $this->warn("⚠️  Error en enrollment: " . $e->getMessage());
                }

                // Mostrar credenciales que se enviarían al suscriptor
                $this->info("\n📧 CREDENCIALES A ENVIAR AL SUSCRIPTOR:");
                $this->line(str_repeat('=', 60));
                $this->info("Campus: https://campus.asociados.acoficum.org");
                $this->info("Usuario: {$testUsername}");
                $this->info("Contraseña: {$testPassword}");
                $this->info("Email: {$testEmail}");
                $this->info("Curso: ASOCIADO ACOFICUM");
                $this->line(str_repeat('=', 60));

                // Limpiar: Borrar el usuario de prueba
                $this->info("\n🧹 Limpiando usuario de prueba...");
                Http::timeout(30)
                    ->post($moodleUrl . '?wstoken=' . $moodleToken . '&wsfunction=core_user_delete_users&moodlewsrestformat=json', [
                        'userids' => [$moodleUserId]
                    ]);
                $this->info("✅ Usuario de prueba eliminado");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error en prueba de usuario: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("\n" . str_repeat('=', 60));
        $this->info("✅ TODAS LAS PRUEBAS PASARON EXITOSAMENTE");
        $this->info(str_repeat('=', 60));

        return Command::SUCCESS;
    }

    /**
     * Generar una contraseña segura según los requisitos de Moodle
     * Requisitos: 8+ caracteres, mayúsculas, minúsculas, números, símbolos
     *
     * @return string
     */
    private function generateSecurePassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';

        // Asegurar que cada grupo esté representado
        $password = '';
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $symbols[rand(0, strlen($symbols) - 1)];

        // Llenar el resto de caracteres aleatoriamente
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 0; $i < 8; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }

        // Mezclar la contraseña
        $passwordArray = str_split($password);
        shuffle($passwordArray);

        return implode('', $passwordArray);
    }
}
