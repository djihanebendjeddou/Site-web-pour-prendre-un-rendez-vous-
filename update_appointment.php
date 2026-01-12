<?php
require_once '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'medecin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['id'];
    $status = $_POST['status'];
    $doctor_id = $_SESSION['user_id'];
    
    try {
        // التحقق من أن الموعد لهذا الطبيب
        $check = $pdo->prepare("SELECT id FROM rendezvous WHERE id = ? AND medecin_id = ?");
        $check->execute([$appointment_id, $doctor_id]);
        
        if($check->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }
        
        // تحديث الحالة
        $stmt = $pdo->prepare("UPDATE rendezvous SET statut = ? WHERE id = ?");
        $stmt->execute([$status, $appointment_id]);
        
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
}
?>