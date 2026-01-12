<?php
// ✅ مصححة الآن - تستخدم utilisateurs بشكل صحيح
require_once 'config/db.php';

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$user_type = isset($_GET['type']) ? $_GET['type'] : 'patient';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone'] ?? '');
    $user_type = $_POST['user_type'];
    
    if($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } elseif(strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères";
    } else {
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE nom_utilisateur = ? OR email = ?");
        $check->execute([$username, $email]);
        
        if($check->rowCount() > 0) {
            $error = "Nom d'utilisateur ou email déjà utilisé";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, type_utilisateur, nom_complet, telephone) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed, $user_type, $full_name, $phone]);
                
                $user_id = $pdo->lastInsertId();
                
                if($user_type == 'medecin') {
                    $pdo->prepare("INSERT INTO medecins (utilisateur_id) VALUES (?)")->execute([$user_id]);
                }
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = $user_type;
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = $full_name;
                
                header("Location: dashboard.php");
                exit;
                
            } catch(PDOException $e) {
                $error = "Erreur: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .type-buttons { display: flex; gap: 10px; margin-bottom: 20px; }
        .type-btn { flex: 1; padding: 15px; text-align: center; border: 2px solid #ddd; border-radius: 5px; text-decoration: none; color: #555; }
        .type-btn.active { border-color: #3498db; background: #e3f2fd; color: #3498db; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Créer un compte</h2>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="user_type" value="<?php echo $user_type; ?>">
                
                <div class="form-group">
                    <label>Type de compte</label>
                    <div class="type-buttons">
                        <a href="?type=patient" class="type-btn <?php echo $user_type == 'patient' ? 'active' : ''; ?>">Patient</a>
                        <a href="?type=medecin" class="type-btn <?php echo $user_type == 'medecin' ? 'active' : ''; ?>">Médecin</a>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nom complet *</label>
                    <input type="text" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label>Nom d'utilisateur *</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <lab
el>Email *</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone">
                </div>
                
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">S'inscrire</button>
                
                <div class="auth-links">
                    Déjà un compte? <a href="login.php">Connectez-vous</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>