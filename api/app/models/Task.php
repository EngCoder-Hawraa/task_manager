<?php

class Task
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // موجود في libraries
    }

    // ✅ جلب كل المهام مع المرفقات والتعليقات والمسؤولين + دعم الفلترة
    public function getGroupTasks($status = null, $priority = null)
    {
        $query = "SELECT * FROM tasks WHERE deleted_at IS NULL";

        if ($status && $status !== 'الكل') {
            $query .= " AND status = :status";
        }

        if ($priority && $priority !== 'الكل') {
            $query .= " AND priority = :priority";
        }

        $query .= " ORDER BY due_date ASC";

        $this->db->query($query);

        if ($status && $status !== 'الكل') {
            $this->db->bind(':status', $status);
        }
        if ($priority && $priority !== 'الكل') {
            $this->db->bind(':priority', $priority);
        }

        $tasks = $this->db->resultSet();

        foreach ($tasks as &$task) {
            $task->attachments = $this->getAttachments($task->id);
            $task->comments = $this->getComments($task->id);
            $task->assigned_to = $this->getAssignedUsers($task->id);
        }

        return $tasks;
    }

// ✅ جلب المستخدمين المخصصين لمهمة معينة
    private function getAssignedUsers($taskId)
    {
        $this->db->query("SELECT users.id, users.name, users.avatar
                      FROM task_user
                      JOIN users ON users.id = task_user.user_id
                      WHERE task_user.task_id = :task_id");
        $this->db->bind(':task_id', $taskId);
        return $this->db->resultSet();
    }

    // ✅ جلب مهمة واحدة كاملة
    public function getTaskById($id)
    {
        $this->db->query("SELECT * FROM tasks WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':id', $id);
        $task = $this->db->single();

        if ($task) {
            $task['attachments'] = $this->getAttachments($id);
            $task['comments'] = $this->getComments($id);
        }

        return $task;
    }
    // إنشاء مهمة جديدة
    public function createTask($userId, $title, $description, $status, $priority, $dueDate)
    {
        $this->db->query("INSERT INTO tasks (user_id, title, description, status, priority, due_date) 
                          VALUES (:user_id, :title, :description, :status, :priority, :due_date)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':status', $status);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':due_date', $dueDate);

        return $this->db->execute();
    }

    public function assignUserToTask($taskId, $userId)
    {
        $stmt = $this->db->prepare("INSERT INTO task_user (task_id, user_id) VALUES (:task_id, :user_id)");
        $stmt->bindParam(':task_id', $taskId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }


    // جلب المهام حسب المستخدم
    public function getTasksByUser2($userId)
    {
        $this->db->query("SELECT * FROM tasks WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY due_date ASC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function getTasksByUser($userId, $status = null)
    {
        if ($status && $status !== 'الكل') {
            $this->db->query("SELECT * FROM tasks WHERE user_id = :user_id AND status = :status AND deleted_at IS NULL ORDER BY due_date ASC");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':status', $status);
        } else {
            $this->db->query("SELECT * FROM tasks WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY due_date ASC");
            $this->db->bind(':user_id', $userId);
        }

        return $this->db->resultSet();
    }
    // جلب مهمة واحدة بالمعرف
//    public function getTaskById($id)
//    {
//        $this->db->query("SELECT * FROM tasks WHERE id = :id AND deleted_at IS NULL");
//        $this->db->bind(':id', $id);
//        return $this->db->single();
//    }

    // تحديث مهمة
    public function updateTask($id, $title, $description, $status, $priority, $dueDate)
    {
        $this->db->query("UPDATE tasks SET 
                            title = :title,
                            description = :description,
                            status = :status,
                            priority = :priority,
                            due_date = :due_date,
                            updated_at = NOW()
                          WHERE id = :id AND deleted_at IS NULL");

        $this->db->bind(':id', $id);
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':status', $status);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':due_date', $dueDate);

        return $this->db->execute();
    }

    // حذف مهمة (حذف ناعم)
    public function deleteTask($id)
    {
        $this->db->query("UPDATE tasks SET deleted_at = NOW() WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // تعليم المهمة كمكتملة
    public function markCompleted($id)
    {
        $this->db->query("UPDATE tasks SET status = 'completed', completed_at = NOW(), updated_at = NOW() WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // جلب المهام النشطة (غير مكتملة وغير محذوفة)
    public function getActiveTasks($userId)
    {
        $this->db->query("SELECT * FROM tasks 
                          WHERE user_id = :user_id 
                          AND status != 'completed' 
                          AND deleted_at IS NULL 
                          ORDER BY due_date ASC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    // ✅ جلب المرفقات لمهمة
    public function getAttachments($taskId)
    {
        $this->db->query("SELECT * FROM task_attachments WHERE task_id = :task_id");
        $this->db->bind(':task_id', $taskId);
        return $this->db->resultSet();
    }

    // ✅ إضافة مرفق
    public function addAttachment($taskId, $filePath)
    {
        $this->db->query("INSERT INTO task_attachments (task_id, file_path, uploaded_at)
                          VALUES (:task_id, :file_path, NOW())");
        $this->db->bind(':task_id', $taskId);
        $this->db->bind(':file_path', $filePath);
        return $this->db->execute();
    }

    // ✅ جلب التعليقات
    public function getComments($taskId)
    {
        $this->db->query("SELECT task_comments.*, users.name 
                          FROM task_comments 
                          JOIN users ON users.id = task_comments.user_id
                          WHERE task_id = :task_id
                          ORDER BY created_at ASC");
        $this->db->bind(':task_id', $taskId);
        return $this->db->resultSet();
    }

    // ✅ إضافة تعليق
    public function addComment($taskId, $userId, $comment)
    {
        $this->db->query("INSERT INTO task_comments (task_id, user_id, comment, created_at)
                          VALUES (:task_id, :user_id, :comment, NOW())");
        $this->db->bind(':task_id', $taskId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':comment', $comment);
        return $this->db->execute();
    }
}
