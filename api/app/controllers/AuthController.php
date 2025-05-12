<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/jwt_helper.php';

// تحميل .env عند الحاجة
if (!isset($_ENV['JWT_SECRET_KEY'])) {
    Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();
}

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'يرجى ملء جميع الحقول']);
            return;
        }

        if ($this->userModel->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'هذا البريد الإلكتروني مسجل مسبقًا']);
            return;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $userId = $this->userModel->createUser($data['name'], $data['email'], $hashedPassword);

        if ($userId) {
            http_response_code(201);
            echo json_encode(['message' => 'تم التسجيل بنجاح']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'حدث خطأ أثناء التسجيل']);
        }
    }

    public function login()
    {
        // استقبال البيانات
        $data = json_decode(file_get_contents("php://input"));

        if (!$data || empty($data->email) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(['error' => 'يرجى إدخال البريد وكلمة المرور']);
            return;
        }
        // البحث عن المستخدم
        $user = $this->userModel->findByEmail($data->email);

        if (!$user || !password_verify($data->password, $user->password)) {
            http_response_code(401);
            echo json_encode(['error' => 'بيانات غير صحيحة']);
            return;
        }

        // توليد التوكن JWT
        $token = generate_jwt([
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ?? 'user'
        ]);

        // إرسال التوكن
        echo json_encode([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token
        ]);
    }




    public function logout()
    {
        echo json_encode(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
