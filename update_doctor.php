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
<?php
require_once 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'medecin') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب بيانات الطبيب
$stmt = $pdo->prepare("SELECT * FROM medecins WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $specialite = $_POST['specialite'];
    $description = $_POST['description'];
    $disponibilite = $_POST['disponibilite'];
    
    try {
        if($doctor) {
            // تحديث
            $stmt = $pdo->prepare("
                UPDATE medecins 
                SET specialite = ?, description = ?, disponibilite = ? 
                WHERE utilisateur_id = ?
            ");
            $stmt->execute([$specialite, $description, $disponibilite, $user_id]);
        } else {
            // إضافة
            $stmt = $pdo->prepare("
                INSERT INTO medecins (utilisateur_id, specialite, description, disponibilite) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $specialite, $description, $disponibilite]);
        }
        
        $success = "Profil mis à jour avec succès";
        $doctor = ['specialite' => $specialite, 'description' => $description, 'disponibilite' => $disponibilite];
        
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier profil médecin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier votre profil médecin</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Spécialité</label>
                <input type="text" name="specialite" value="<?php echo $doctor['specialite'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?php echo $doctor['description'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Disponibilité</label>
                <input type="text" name="disponibilite" value="<?php echo $doctor['disponibilite'] ?? 'Lundi - Vendredi, 9h - 17h'; ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="dashboard.php" class="btn">Retour</a>
        </form>
    </div>
</body>
</html>