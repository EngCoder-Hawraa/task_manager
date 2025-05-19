<?php

class Task
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // موجود في libraries
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

    // جلب المهام حسب المستخدم
    public function getTasksByUser($userId)
    {
        $this->db->query("SELECT * FROM tasks WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY due_date ASC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    // جلب مهمة واحدة بالمعرف
    public function getTaskById($id)
    {
        $this->db->query("SELECT * FROM tasks WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

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
}
