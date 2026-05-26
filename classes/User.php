<?php
/**
 * Abstract Base Class for Users
 * Demonstrates: Abstraction and Encapsulation
 */
require_once __DIR__ . '/../config/Database.php';

abstract class User {
    protected $db;
    protected $id;
    protected $name;
    protected $email;
    protected $role;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // --- Encapsulation: Getters ---
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }

    // --- Encapsulation: Setters ---
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setRole($role) { $this->role = $role; }

    /**
     * Abstract method: Forces child classes to implement this.
     * Demonstrates: Abstraction
     */
    abstract public function getDashboardUrl();

    /**
     * Login User using Prepared Statements and password_verify
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        // SECURITY: Using Prepared Statements
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // SECURITY: Using password_verify
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            return true;
        }
        return false;
    }
    
    /**
     * Finds a user by ID and populates the object
     */
    public function loadById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if ($user) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            return true;
        }
        return false;
    }
}
?>
