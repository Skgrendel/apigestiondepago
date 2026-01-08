<?php

namespace App\Services;

use App\Models\User;
use App\Models\EpaycoTransaction;
use App\Mail\WelcomeNewMoodleUser;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MoodleEnrollmentService
{
    private string $moodleUrl;
    private string $wsToken;
    private int $timeout;

    public function __construct()
    {
        $this->moodleUrl = env('MOODLE_URL');
        $this->wsToken = env('MOODLE_WS_TOKEN');
        $this->timeout = env('MOODLE_TIMEOUT', 30);
    }

    /**
     * Process complete enrollment flow when payment is approved
     *
     * @param EpaycoTransaction $order
     * @return array Status information about the enrollment process
     */
    public function processPaymentEnrollment(EpaycoTransaction $order): array
    {
        try {
            // Validate order has required data
            if (!$order->user || !$order->course) {
                Log::error('Moodle enrollment failed: Missing user or course', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'course_id' => $order->course_id
                ]);

                return [
                    'success' => false,
                    'error' => 'Missing user or course data',
                    'step' => 'validation'
                ];
            }

            // Check if course has Moodle ID
            if (!$order->course->id_course) {
                Log::warning('Course not linked to Moodle', [
                    'order_id' => $order->id,
                    'course_id' => $order->course_id,
                    'course_title' => $order->course->title
                ]);

                return [
                    'success' => false,
                    'error' => 'Course not linked to Moodle',
                    'step' => 'validation'
                ];
            }

            $user = $order->user;
            $course = $order->course;

            // Step 1: Check if user exists in Moodle
            $moodleUserId = $this->checkUserExists($user);
            $moodleCredentials = null;

            // Step 2: Create user if doesn't exist
            if (!$moodleUserId) {
                Log::info('Creating new Moodle user', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                $creationResult = $this->createMoodleUser($user);

                if (!$creationResult) {
                    return [
                        'success' => false,
                        'error' => 'Failed to create Moodle user',
                        'step' => 'user_creation'
                    ];
                }

                $moodleUserId = $creationResult['id'];
                $moodleCredentials = [
                    'username' => $creationResult['username'],
                    'password' => $creationResult['password']
                ];

                // Update local user with Moodle ID
                $user->update(['moodle_user_id' => $moodleUserId]);

                Log::info('Moodle user created successfully', [
                    'user_id' => $user->id,
                    'moodle_user_id' => $moodleUserId
                ]);
            } else {
                Log::info('User already exists in Moodle', [
                    'user_id' => $user->id,
                    'moodle_user_id' => $moodleUserId
                ]);

                // Update local record if not already set
                if (!$user->moodle_user_id) {
                    $user->update(['moodle_user_id' => $moodleUserId]);
                }
            }

            // Step 3: Enroll user in course
            $enrolled = $this->enrollUserInCourse($moodleUserId, $course->id_course);

            if (!$enrolled) {
                return [
                    'success' => false,
                    'error' => 'Failed to enroll user in course',
                    'step' => 'enrollment',
                    'moodle_user_id' => $moodleUserId
                ];
            }

            Log::info('Moodle enrollment completed successfully', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'moodle_user_id' => $moodleUserId,
                'course_id' => $course->id,
                'moodle_course_id' => $course->id_course
            ]);

            return [
                'success' => true,
                'moodle_user_id' => $moodleUserId,
                'moodle_course_id' => $course->id_course,
                'user_created' => $user->wasRecentlyCreated ?? false,
                'credentials' => $moodleCredentials // Return credentials if created
            ];

        } catch (\Exception $e) {
            Log::error('Exception during Moodle enrollment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'step' => 'exception'
            ];
        }
    }

    /**
     * Process Epayco transaction enrollment - create user and enroll from webhook data
     *
     * @param array $payload Epayco webhook payload
     * @param int|null $courseId Moodle course ID
     * @return array Status information about the enrollment process
     */
    public function processEpaycoEnrollment(array $payload, ?int $courseId = null): array
    {
        try {
            // Use MOODLE_COUSE_ID from env if not provided
            if ($courseId === null) {
                $courseId = env('MOODLE_COUSE_ID', 2);
            }

            // Extract user data from Epayco payload
            $userData = $this->extractUserDataFromEpayco($payload);

            if (empty($userData['email'])) {
                Log::error('Epayco enrollment failed: Missing email', [
                    'transaction_id' => $payload['x_transaction_id'] ?? null
                ]);

                return [
                    'success' => false,
                    'error' => 'Missing email in transaction data',
                    'step' => 'validation'
                ];
            }

            Log::info('Processing Epayco enrollment', [
                'transaction_id' => $payload['x_transaction_id'] ?? null,
                'email' => $userData['email'],
                'course_id' => $courseId
            ]);

            // Step 1: Check if user exists in Moodle
            $moodleUserId = $this->checkUserExistsByEmail($userData['email']);
            $moodleCredentials = null;

            // Step 2: Create user if doesn't exist
            if (!$moodleUserId) {
                Log::info('Creating new Moodle user from Epayco', [
                    'email' => $userData['email'],
                    'transaction_id' => $payload['x_transaction_id'] ?? null
                ]);

                $creationResult = $this->createMoodleUserFromEpayco($userData);

                if (!$creationResult) {
                    return [
                        'success' => false,
                        'error' => 'Failed to create Moodle user',
                        'step' => 'user_creation'
                    ];
                }

                $moodleUserId = $creationResult['id'];
                $moodleCredentials = [
                    'username' => $creationResult['username'],
                    'password' => $creationResult['password'],
                    'email' => $userData['email']
                ];

                Log::info('Moodle user created successfully from Epayco', [
                    'moodle_user_id' => $moodleUserId,
                    'email' => $userData['email']
                ]);
            } else {
                Log::info('User already exists in Moodle', [
                    'moodle_user_id' => $moodleUserId,
                    'email' => $userData['email']
                ]);
            }

            // Step 3: Enroll user in course
            $enrolled = $this->enrollUserInCourse($moodleUserId, $courseId);

            if (!$enrolled) {
                return [
                    'success' => false,
                    'error' => 'Failed to enroll user in course',
                    'step' => 'enrollment',
                    'moodle_user_id' => $moodleUserId
                ];
            }

            // Step 4: Send welcome email if user was newly created
            if ($moodleCredentials) {
                try {
                    $this->sendWelcomeEmail(
                        $moodleCredentials['email'],
                        $moodleCredentials['username'],
                        $moodleCredentials['password'],
                        $userData['firstname'],
                        $userData['lastname']
                    );

                    Log::info('Welcome email sent successfully', [
                        'email' => $moodleCredentials['email'],
                        'moodle_user_id' => $moodleUserId
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send welcome email', [
                        'email' => $moodleCredentials['email'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Epayco enrollment completed successfully', [
                'transaction_id' => $payload['x_transaction_id'] ?? null,
                'moodle_user_id' => $moodleUserId,
                'course_id' => $courseId,
                'email' => $userData['email']
            ]);

            return [
                'success' => true,
                'moodle_user_id' => $moodleUserId,
                'moodle_course_id' => $courseId,
                'credentials' => $moodleCredentials,
                'transaction_id' => $payload['x_transaction_id'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Exception during Epayco enrollment', [
                'transaction_id' => $payload['x_transaction_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'step' => 'exception'
            ];
        }
    }

    /**
     * Extract user data from Epayco webhook payload
     *
     * @param array $payload
     * @return array
     */
    private function extractUserDataFromEpayco(array $payload): array
    {
        $nombres = trim($payload['x_customer_name']);
        $apellidos = trim($payload['x_customer_lastname']);

        if(count(preg_split('/\s+/', $apellidos)) >= 2){
            $partes = preg_split('/\s+/', $apellidos);
            $apellidos = $partes[0].' '.$partes[1];
        }

        if(count(preg_split('/\s+/', $nombres)) >= 2){
            $nombres = $partes[1].' '.$partes[0];
        }


        return [
            'email' => $payload['x_customer_email'] ?? '',
            'firstname' => $nombres ?? 'Usuario',
            'lastname' => $apellidos ?? 'Apellido',
            'phone' => $payload['x_customer_phone'] ?? '',
            'city' => $payload['x_customer_city'] ?? 'Colombia',
            'country' => $payload['x_customer_country'] ?? 'CO',
            'idnumber' => $payload['x_customer_document'] ?? '',
            'institution' => $payload['x_business'] ?? 'ACOFICUM',
        ];
    }

    /**
     * Check if user exists in Moodle by email (simplified version)
     *
     * @param string $email
     * @return int|null Moodle user ID if exists, null otherwise
     */
    private function checkUserExistsByEmail(string $email): ?int
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->moodleUrl, [
                    'wstoken' => $this->wsToken,
                    'wsfunction' => 'core_user_get_users_by_field',
                    'moodlewsrestformat' => 'json',
                    'field' => 'email',
                    'values[0]' => $email,
                ]);

            /** @var Response $response */
            if ($response->failed()) {
                Log::warning('Failed to check Moodle user by email', [
                    'status' => $response->status(),
                    'email' => $email
                ]);
                return null;
            }

            $result = $response->json();

            if (isset($result['exception'])) {
                Log::warning('Moodle API error checking user by email', [
                    'error' => $result['message'] ?? 'Unknown error',
                    'email' => $email
                ]);
                return null;
            }

            if (is_array($result) && !empty($result[0]['id'])) {
                return (int) $result[0]['id'];
            }

            return null;

        } catch (\Exception $e) {
            Log::warning('Exception checking Moodle user by email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create a new user in Moodle from Epayco data
     *
     * @param array $userData Extracted user data
     * @return array|null Array with 'id', 'username', 'password' if created, null on failure
     */
    public function createMoodleUserFromEpayco(array $userData): ?array
    {
        try {
            // Generate username from email
            $username = $this->generateMoodleUsername($userData['email']);

            // Generate a secure random password
            $password = Str::password(10, true, true, true, false) . '!1A';

            // Prepare user data
            $moodleUserData = [
                'users' => [
                    [
                        'username' => $username,
                        'password' => $password,
                        'firstname' => $userData['firstname'],
                        'lastname' => $userData['lastname'],
                        'email' => $userData['email'],
                        'auth' => 'manual',
                        'lang' => 'es',
                        'timezone' => 'America/Bogota',
                        'mailformat' => 1,
                        'city' => $userData['city'],
                        'country' => $userData['country'],
                        'idnumber' => $userData['idnumber'],
                        'institution' => $userData['institution'],
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->moodleUrl . '?wstoken=' . $this->wsToken . '&wsfunction=core_user_create_users&moodlewsrestformat=json', $moodleUserData);

            /** @var Response $response */
            if ($response->failed()) {
                Log::error('Failed to create Moodle user from Epayco', [
                    'status' => $response->status(),
                    'email' => $userData['email'],
                    'body' => $response->body()
                ]);
                return null;
            }

            $result = $response->json();

            if (isset($result['exception'])) {
                Log::error('Moodle API error creating user from Epayco', [
                    'error' => $result['message'] ?? 'Unknown error',
                    'email' => $userData['email']
                ]);
                return null;
            }

            if (is_array($result) && !empty($result[0]['id'])) {
                return [
                    'id' => (int) $result[0]['id'],
                    'username' => $username,
                    'password' => $password
                ];
            }

            Log::error('Unexpected response creating Moodle user from Epayco', [
                'response' => $result,
                'email' => $userData['email']
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception creating Moodle user from Epayco', [
                'email' => $userData['email'],
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if user exists in Moodle by email
     *
     * @param User $user
     * @return int|null Moodle user ID if exists, null otherwise
     */
    public function checkUserExists(User $user): ?int
    {
        try {
            // First check local database
            if ($user->moodle_user_id) {
                return $user->moodle_user_id;
            }

            // Query Moodle API
            $response = Http::timeout($this->timeout)
                ->get($this->moodleUrl, [
                    'wstoken' => $this->wsToken,
                    'wsfunction' => 'core_user_get_users_by_field',
                    'moodlewsrestformat' => 'json',
                    'field' => 'email',
                    'values[0]' => $user->email,
                ]);

            /** @var Response $response */
            if ($response->failed()) {
                Log::error('Failed to check Moodle user existence', [
                    'status' => $response->status(),
                    'email' => $user->email
                ]);
                return null;
            }

            $result = $response->json();

            // Check for API errors
            if (isset($result['exception'])) {
                Log::error('Moodle API error checking user', [
                    'error' => $result,
                    'email' => $user->email
                ]);
                return null;
            }

            // User found
            if (is_array($result) && !empty($result[0]['id'])) {
                return $result[0]['id'];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Exception checking Moodle user existence', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create a new user in Moodle
     *
     * @param User $user
     * @return array|null Array with 'id', 'username', 'password' if created, null on failure
     */
    public function createMoodleUser(User $user): ?array
    {
        try {
            // Generate username from email (remove domain)
            $username = $this->generateMoodleUsername($user->email);

            // Generate a secure random password
            // Requirements: 8 chars, 1 digit, 1 lower, 1 upper, 1 non-alphanumeric
            $password = Str::password(10, true, true, true, false) . '!1A'; // Ensure complexity

            // Prepare user data
            $userData = [
                'users' => [
                    [
                        'username' => $username,
                        'password' => $password,
                        'firstname' => $this->extractFirstName($user->name),
                        'lastname' => $this->extractLastName($user->name),
                        'email' => $user->email,
                        'auth' => 'manual',
                        'lang' => 'es',
                        'timezone' => 'America/Bogota',
                        'mailformat' => 1, // HTML email format
                        'city' => 'Colombia',
                        'country' => 'CO',
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->moodleUrl . '?wstoken=' . $this->wsToken . '&wsfunction=core_user_create_users&moodlewsrestformat=json', $userData);

            /** @var Response $response */
            if ($response->failed()) {
                Log::error('Failed to create Moodle user', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'email' => $user->email
                ]);
                return null;
            }

            $result = $response->json();

            // Check for API errors
            if (isset($result['exception'])) {
                Log::error('Moodle API error creating user', [
                    'error' => $result,
                    'email' => $user->email
                ]);
                return null;
            }

            // User created successfully
            if (is_array($result) && !empty($result[0]['id'])) {
                return [
                    'id' => $result[0]['id'],
                    'username' => $username,
                    'password' => $password
                ];
            }

            Log::error('Unexpected response creating Moodle user', [
                'response' => $result,
                'email' => $user->email
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception creating Moodle user', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Enroll user in a Moodle course
     *
     * @param int $moodleUserId
     * @param int $moodleCourseId
     * @return bool Success status
     */
    public function enrollUserInCourse(int $moodleUserId, int $moodleCourseId): bool
    {
        try {
            // Prepare enrollment data
            // roleid = 5 is typically the "student" role in Moodle
            $enrollmentData = [
                'enrolments' => [
                    [
                        'roleid' => 5, // Student role
                        'userid' => $moodleUserId,
                        'courseid' => $moodleCourseId,
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->moodleUrl . '?wstoken=' . $this->wsToken . '&wsfunction=enrol_manual_enrol_users&moodlewsrestformat=json', $enrollmentData);

            /** @var Response $response */
            if ($response->failed()) {
                Log::error('Failed to enroll user in Moodle course', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'moodle_user_id' => $moodleUserId,
                    'moodle_course_id' => $moodleCourseId
                ]);
                return false;
            }

            $result = $response->json();

            // Check for API errors
            if (isset($result['exception'])) {
                $errorCode = $result['errorcode'] ?? 'unknown';
                $errorMessage = $result['message'] ?? 'Unknown error';

                // Detect specific error: manual enrollment not enabled
                if ($errorCode === 'wsnoinstance') {
                    Log::error('Moodle API error enrolling user', [
                        'error' => $result,
                        'moodle_user_id' => $moodleUserId,
                        'moodle_course_id' => $moodleCourseId,
                        'solution' => 'Manual enrollment plugin is not enabled for this course. Enable it in Moodle: Course → More → Users → Enrollment methods → Add/Enable "Manual enrolment"'
                    ]);
                } else {
                    Log::error('Moodle API error enrolling user', [
                        'error' => $result,
                        'moodle_user_id' => $moodleUserId,
                        'moodle_course_id' => $moodleCourseId
                    ]);
                }

                return false;
            }

            // Successful enrollment returns null or empty array
            Log::info('User enrolled in Moodle course successfully', [
                'moodle_user_id' => $moodleUserId,
                'moodle_course_id' => $moodleCourseId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Exception enrolling user in Moodle course', [
                'moodle_user_id' => $moodleUserId,
                'moodle_course_id' => $moodleCourseId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate Moodle username from email
     *
     * @param string $email
     * @return string
     */
    private function generateMoodleUsername(string $email): string
    {
        // Extract part before @ and sanitize
        $username = Str::before($email, '@');
        $username = Str::slug($username, '');

        // Ensure it's not too long (Moodle username max is 100 chars)
        return Str::limit($username, 100, '');
    }

    /**
     * Extract first name from full name
     *
     * @param string $fullName
     * @return string
     */
    private function extractFirstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? 'Usuario';
    }

    /**
     * Extract last name from full name
     *
     * @param string $fullName
     * @return string
     */
    private function extractLastName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));

        if (count($parts) > 1) {
            array_shift($parts); // Remove first name
            return implode(' ', $parts);
        }

        return 'Apellido';
    }

    /**
     * Get enrollment methods for a course
     *
     * @param int $courseId
     * @return array
     */
    public function getEnrollmentMethods(int $courseId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->moodleUrl, [
                    'wstoken' => $this->wsToken,
                    'wsfunction' => 'core_enrol_get_course_enrolment_methods',
                    'moodlewsrestformat' => 'json',
                    'courseid' => $courseId,
                ]);

            /** @var Response $response */
            if ($response->failed()) {
                return [];
            }

            $result = $response->json();

            if (isset($result['exception'])) {
                return [];
            }

            return is_array($result) ? $result : [];

        } catch (\Exception $e) {
            Log::error('Exception fetching enrollment methods', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Send welcome email to new user with credentials
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @return bool
     */
    private function sendWelcomeEmail(
        string $email,
        string $username,
        string $password,
        string $firstname,
        string $lastname
    ): bool {
        try {
            $campusUrl = env('MOODLE_URL');
            // Extract base URL from full API URL
            if (strpos($campusUrl, '/webservice/rest/server.php') !== false) {
                $campusUrl = str_replace('/webservice/rest/server.php', '/', $campusUrl);
            }

            Mail::to($email)->send(
                new WelcomeNewMoodleUser(
                    username: $username,
                    password: $password,
                    firstname: $firstname,
                    lastname: $lastname,
                    email: $email,
                    membershipName: env('MOODLE_COURSE_NAME', 'Membresía ACOFICUM'),
                    campusUrl: $campusUrl
                )
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Error sending welcome email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
