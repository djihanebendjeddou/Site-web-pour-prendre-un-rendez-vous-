<?php
require_once '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'patient') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $notes = $_POST['notes'] ?? '';
    
    try {
        // التحقق من عدم وجود حجز مسبق
        $check = $pdo->prepare("
            SELECT id FROM rendezvous 
            WHERE medecin_id = ? 
            AND date_rendezvous = ? 
            AND heure_rendezvous = ?
            AND statut != 'refuse'
        ");
        $check->execute([$doctor_id, $date, $time]);
        
        if($check->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Ce créneau est déjà pris']);
            exit;
        }
        
        // إضافة الحجز
        $stmt = $pdo->prepare("
            INSERT INTO rendezvous (patient_id, medecin_id, date_rendezvous, heure_rendezvous, notes) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$patient_id, $doctor_id, $date, $time, $notes]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Rendez-vous pris avec succès!'
        ]);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
}
?>