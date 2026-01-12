<?php
// ✅ مصحح - يستخدم utilisateurs بشكل صحيح
require_once 'config/db.php';

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // البحث في جدول utilisateurs
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['type_utilisateur'];
        $_SESSION['username'] = $user['nom_utilisateur'];
        $_SESSION['full_name'] = $user['nom_complet'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Connexion</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur ou Email</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Se connecter</button>
                
                <div class="auth-links">
                    Pas de compte? <a href="register.php">Inscrivez-vous</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>