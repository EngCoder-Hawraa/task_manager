<?php

require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../helpers/jwt_helper.php';

class TaskController extends Controller
{
    private $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    // ✅ توثيق المستخدم عن طريق JWT
    private function getUserIdFromToken()
    {
        $headers = apache_request_headers();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Missing Authorization header']);
            exit;
        }

        $authHeader = $headers['Authorization'];

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid Authorization header format']);
            exit;
        }

        $decoded = is_jwt_valid($jwt);

        if (!$decoded || !isset($decoded->user_id)) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid or expired token']);
            exit;
        }

        return $decoded->user_id;
    }

    public function Tasks()
    {
        header('Content-Type: application/json');

        $status = $_GET['status'] ?? null;
        $priority = $_GET['priority'] ?? null;

        $tasks = $this->taskModel->getGroupTasks($status, $priority);
        echo json_encode($tasks);
    }

    // ✅ جلب كل المهام للمستخدم
    public function index()
    {
        $userId = $this->getUserIdFromToken();
        // دعم فلترة حسب الحالة (status)
        $status = $_GET['status'] ?? null;
//        $tasks = $this->taskModel->getTasksByUser2($userId);
        $tasks = $this->taskModel->getTasksByUser($userId, $status);
        echo json_encode($tasks);
    }

    // ✅ إنشاء مهمة جديدة
    public function store()
    {
        $userId = $this->getUserIdFromToken();
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['title']) ||
            empty($data['description']) ||
            empty($data['status']) ||
            empty($data['priority']) ||
            empty($data['due_date'])
        ) {
            http_response_code(400);
            echo json_encode(['message' => 'All fields are required']);
            return;
        }

        if (strlen($data['title']) > 255) {
            http_response_code(400);
            echo json_encode(['message' => 'العنوان طويل جدًا']);
            return;
        }

        // 1. إنشاء المهمة
        $taskId = $this->taskModel->createTask(
            $userId,
            $data['title'],
            $data['description'],
            $data['status'],
            $data['priority'],
            $data['due_date']
        );

        // 2. إذا تم إنشاؤها بنجاح، أضف المستخدمين المرتبطين
        if (!empty($data['assigned_users']) && is_array($data['assigned_users'])) {
            foreach ($data['assigned_users'] as $assigned) {
                if (!empty($assigned['user_id'])) {
                    $assignedUserId = $assigned['user_id'];
                    $usedAt = !empty($assigned['used_at']) ? $assigned['used_at'] : date('Y-m-d H:i:s');

                    // نفترض أن لديك دالة createTaskUser داخل taskModel أو model آخر
                    $this->taskModel->assignUserToTask($taskId, $assignedUserId, $usedAt);
                }
            }
        }

        echo json_encode(['success' => true, 'task_id' => $taskId]);
    }


    // ✅ جلب مهمة واحدة
    public function show($id)
    {
        $this->getUserIdFromToken(); // تأكد فقط أنه مصرح
        $task = $this->taskModel->getTaskById($id);
        echo json_encode($task);
    }

    // ✅ تحديث مهمة
    public function update($id)
    {
        $this->getUserIdFromToken();
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->taskModel->updateTask(
            $id,
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['status'] ?? 'open',
            $data['priority'] ?? 'medium',
            $data['due_date'] ?? null
        );

        echo json_encode(['success' => $result]);
    }

    // ✅ حذف مهمة حذف ناعم
    public function destroy($id)
    {
        $this->getUserIdFromToken();
        $result = $this->taskModel->deleteTask($id);
        echo json_encode(['success' => $result]);
    }

    // ✅ تعليم مهمة كمكتملة
    public function markAsDone($id)
    {
        $this->getUserIdFromToken();
        $result = $this->taskModel->markCompleted($id);
        echo json_encode(['success' => $result]);
    }
}
