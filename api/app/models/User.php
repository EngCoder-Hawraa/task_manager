<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // موجود في libraries
    }

    // البحث عن مستخدم عبر البريد الإلكتروني
    public function findByEmail($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        return $this->db->single(); // يرجع صف واحد أو false
    }

    // إنشاء مستخدم جديد
    public function createUser($name, $email, $password)
    {
        $this->db->query("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $password);

        return $this->db->execute();
    }
}
