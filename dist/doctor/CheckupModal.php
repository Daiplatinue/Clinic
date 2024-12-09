<?php
require_once __DIR__ . '/../config/db.php';

class CheckupModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function updateCheckup($healthStatus, $allergies, $checkupId) {
        try {
            $this->conn->begin_transaction();

            $updateUserStmt = $this->conn->prepare("
                UPDATE user u 
                JOIN check_up c ON u.u_id = c.u_id 
                SET u.u_hs = ?, 
                    u.u_allergy = ?
                WHERE c.c_id = ?
            ");
            $updateUserStmt->bind_param("ssi", $healthStatus, $allergies, $checkupId);
            $updateUserStmt->execute();

            $updateCheckupStmt = $this->conn->prepare("
                UPDATE check_up 
                SET c_status = 'completed' 
                WHERE c_id = ?
            ");
            $updateCheckupStmt->bind_param("i", $checkupId);
            $updateCheckupStmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function hasPendingCheckup($userId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as pending_count 
            FROM check_up 
            WHERE u_id = ? AND c_status = 'pending'
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['pending_count'] > 0;
    }

    public function createCheckup($userId, $date, $time, $reason, $isUrgent) {
        try {
            if ($this->hasPendingCheckup($userId)) {
                throw new Exception("You already have a pending check-up request. Please wait for it to be completed.");
            }

            $stmt = $this->conn->prepare("
                INSERT INTO check_up (u_id, c_pd, c_pt, c_rc, c_urgent, c_status) 
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $urgentStatus = $isUrgent ? 'urgent' : 'unurgent';
            $stmt->bind_param("issss", $userId, $date, $time, $reason, $urgentStatus);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUrgentCheckups() {
        $query = "SELECT c.*, u.u_fn as fullname, u.u_grade, u.u_gender, u.u_hs as health_status
                 FROM check_up c 
                 JOIN user u ON c.u_id = u.u_id 
                 WHERE c.c_urgent = 'urgent' AND c.c_status = 'pending' 
                 ORDER BY c.c_pd ASC, c.c_pt ASC";
        return $this->conn->query($query);
    }

    public function getRegularCheckups() {
        $query = "SELECT c.*, u.u_fn as fullname, u.u_grade, u.u_gender, u.u_hs as health_status
                 FROM check_up c 
                 JOIN user u ON c.u_id = u.u_id 
                 WHERE c.c_urgent = 'unurgent' AND c.c_status = 'pending' 
                 ORDER BY c.c_pd ASC, c.c_pt ASC";
        return $this->conn->query($query);
    }
}