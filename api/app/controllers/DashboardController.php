<?php

require_once '../models/Task.php';
require_once '../helpers/jwt_helper.php'; // تأكد أنه موجود للتعامل مع JWT

class DashboardController
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

        $jwt = trim(str_replace('Bearer', '', $headers['Authorization']));
        $decoded = jwt_helper::validateToken($jwt);

        if (!$decoded || !isset($decoded->user_id)) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid or expired token']);
            exit;
        }

        return $decoded->user_id;
    }

    // ✅ جلب كل المهام للمستخدم
    public function index()
    {
        $userId = $this->getUserIdFromToken();
        $tasks = $this->taskModel->getTasksByUser($userId);
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

        $result = $this->taskModel->createTask(
            $userId,
            $data['title'],
            $data['description'],
            $data['status'],
            $data['priority'],
            $data['due_date']
        );

        echo json_encode(['success' => $result]);
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
