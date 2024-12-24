<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php');

class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 사용자 등록
    public function registerUser($admin_id, $admin_pw, $admin_role) {
        $admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO wms_admin (admin_id, admin_pw, admin_role) VALUES (:admin_id, :admin_pw, :admin_role)");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':admin_pw', $admin_pw);
        $stmt->bindParam(':admin_role', $admin_role);
        $stmt->execute();
    }

    // 사용자 로그인
    public function loginUser($admin_id, $admin_pw) {
        $stmt = $this->conn->prepare("SELECT * FROM wms_admin WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($admin_pw, $user['admin_pw'])) {
            session_start();
            $_SESSION['admin_idx']   = $user['admin_idx'];
            $_SESSION['admin_id']    = $user['admin_id'];
            $_SESSION['admin_name']    = $user['admin_name'];
            $_SESSION['admin_role']  = $user['admin_role'];
            return true;
        } else {
            return false;
        }
    }

    // 사용자 로그아웃
    public function logoutUser() {
        session_start();
        session_unset();
        session_destroy();
    }

    // 사용자 권한 확인
    public function checkUserRole($requiredRole) {
        session_start();
        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            return false; // 사용자가 로그인하지 않았음
        }

        $stmt = $this->conn->prepare("SELECT admin_role FROM wms_admin WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();
        $userRole = $stmt->fetchColumn();

        return $userRole === $requiredRole;
    }
}

$userManager = new UserManager($conn);
?>
