<?php
require_once 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// جلب بيانات المستخدم
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if(!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// جلب المواعيد
if($user_type == 'medecin') {
    $stmt = $pdo->prepare("SELECT * FROM medecins WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    
    $appointments = $pdo->prepare("
        SELECT r.*, u.nom_complet as patient_name 
        FROM rendezvous r 
        JOIN utilisateurs u ON r.patient_id = u.id 
        WHERE r.medecin_id = ? 
        ORDER BY r.date_rendezvous DESC
    ");
    $appointments->execute([$user_id]);
    $appointments = $appointments->fetchAll();
} else {
    $appointments = $pdo->prepare("
        SELECT r.*, u.nom_complet as doctor_name, m.specialite 
        FROM rendezvous r 
        JOIN utilisateurs u ON r.medecin_id = u.id 
        LEFT JOIN medecins m ON m.utilisateur_id = u.id
        WHERE r.patient_id = ? 
        ORDER BY r.date_rendezvous DESC
    ");
    $appointments->execute([$user_id]);
    $appointments = $appointments->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - MediRendez-vous</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- الهيدر -->
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <img src="https://img.icons8.com/color/96/000000/medical-doctor.png" 
                     alt="MediRendez-vous Logo" 
                     class="logo-img"
                     style="height: 50px; width: 50px;">
                <div>
                    <div class="logo-text">Tableau de bord</div>
                    <div class="logo-subtext">Espace personnel</div>
                </div>
            </a>
            
            <nav class="nav-menu">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i> Accueil
                </a>
                <div class="user-badge">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($user['nom_complet']); ?></span>
                </div>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </nav>
        </div>
    </header>

    <!-- المحتوى الرئيسي -->
    <main class="container">
        <div class="dashboard-layout">
            <!-- القائمة الجانبية -->
            <aside class="sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="https://img.icons8.com/color/100/000000/user-male-circle--v1.png" 
                             alt="Avatar" 
                             style="width: 80px; height: 80px;">
                    </div>
                    <h3><?php echo htmlspecialchars($user['nom_complet']); ?></h3>
                    <div class="user-role">
                        <?php echo $user_type == 'medecin' ? '👨‍⚕️ Médecin' : '👤 Patient'; ?>
                    </div>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <nav class="sidebar-nav">
                    <a href="dashboard.php" class="active">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                    
                    <?php if($user_type == 'medecin'): ?>
<a href="update_doctor.php">
                            <i class="fas fa-user-md"></i> Profil médecin
                        </a>
                    <?php else: ?>
                        <a href="book_rdv.php">
                            <i class="fas fa-calendar-plus"></i> Nouveau rendez-vous
                        </a>
                    <?php endif; ?>
                    
                    <a href="index.php">
                        <i class="fas fa-globe"></i> Site public
                    </a>
                    
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </nav>
            </aside>

            <!-- المحتوى الرئيسي -->
            <div class="main-content">
                <!-- رسالة ترحيب -->
                <div class="welcome-header">
                    <h1>Bonjour, <?php echo htmlspecialchars($user['nom_complet']); ?> ! 👋</h1>
                    <p>Bienvenue dans votre espace personnel MediRendez-vous.</p>
                </div>

                <!-- الإحصائيات -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Rendez-vous</h3>
                        <p><?php echo count($appointments); ?> total</p>
                    </div>
                    
                    <?php if($user_type == 'patient'): ?>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>Confirmés</h3>
                            <p>
                                <?php 
                                $accepted = array_filter($appointments, function($a) {
                                    return $a['statut'] == 'accepte';
                                });
                                echo count($accepted);
                                ?>
                            </p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h3>Médecins</h3>
                            <p>
                                <?php 
                                $doctors = array_unique(array_column($appointments, 'doctor_name'));
                                echo count($doctors);
                                ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>Patients</h3>
                            <p>
                                <?php 
                                $patients = array_unique(array_column($appointments, 'patient_name'));
                                echo count($patients);
                                ?>
                            </p>
                        </div>
                        
                        <?php if(isset($doctor) && $doctor['specialite']): ?>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-stethoscope"></i>
                                </div>
                                <h3>Spécialité</h3>
                                <p><?php echo htmlspecialchars($doctor['specialite']); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- المواعيد -->
<div class="appointments-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <h2>
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo $user_type == 'medecin' ? 'Rendez-vous à venir' : 'Mes rendez-vous'; ?>
                        </h2>
                        
                        <?php if($user_type == 'patient'): ?>
                            <a href="book_rdv.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nouveau rendez-vous
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(empty($appointments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>Aucun rendez-vous pour le moment</h3>
                            <p>
                                <?php if($user_type == 'patient'): ?>
                                    Prenez votre premier rendez-vous médical en ligne.
                                <?php else: ?>
                                    Vous n'avez pas de rendez-vous programmés.
                                <?php endif; ?>
                            </p>
                            <?php if($user_type == 'patient'): ?>
                                <a href="book_rdv.php" class="btn btn-primary" style="margin-top: 20px;">
                                    <i class="fas fa-calendar-plus"></i> Prendre un rendez-vous
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach($appointments as $app): ?>
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <h3>
                                        <?php if($user_type == 'medecin'): ?>
                                            <i class="fas fa-user-injured"></i> Patient: <?php echo htmlspecialchars($app['patient_name']); ?>
                                        <?php else: ?>
                                            <i class="fas fa-user-md"></i> Dr. <?php echo htmlspecialchars($app['doctor_name']); ?>
                                            <?php if($app['specialite']): ?>
                                                <span style="color: #666; font-size: 14px;">(<?php echo htmlspecialchars($app['specialite']); ?>)</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </h3>
                                    <span class="status-badge status-<?php echo $app['statut']; ?>">
                                        <?php 
                                        switch($app['statut']) {
                                            case 'en_attente': echo '⏳ En attente'; break;
                                            case 'accepte': echo '✅ Confirmé'; break;
                                            case 'refuse': echo '❌ Refusé'; break;
                                        }
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="appointment-details">
                                    <p>
                                        <i class="fas fa-calendar" style="color: #3498db;"></i>
                                        <strong>Date:</strong> <?php echo $app['date_rendezvous']; ?>
                                    </p>
                                    <p>
                                        <i class="fas fa-clock" style="color: #3498db;"></i>
                                        <strong>Heure:</strong> <?php echo substr($app['heure_rendezvous
'], 0, 5); ?>
                                    </p>
                                    <p>
                                        <i class="fas fa-info-circle" style="color: #3498db;"></i>
                                        <strong>Statut:</strong> 
                                        <?php 
                                        switch($app['statut']) {
                                            case 'en_attente': 
                                                echo 'En attente de confirmation'; 
                                                break;
                                            case 'accepte': 
                                                echo 'Rendez-vous confirmé'; 
                                                break;
                                            case 'refuse': 
                                                echo 'Rendez-vous refusé'; 
                                                break;
                                        }
                                        ?>
                                    </p>
                                </div>
                                
                                <?php if($app['notes']): ?>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 3px solid #3498db;">
                                        <strong><i class="fas fa-sticky-note"></i> Notes:</strong>
                                        <p style="margin-top: 5px;"><?php echo htmlspecialchars($app['notes']); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($user_type == 'medecin' && $app['statut'] == 'en_attente'): ?>
                                    <div class="appointment-actions">
                                        <button class="btn btn-success accept-btn" data-id="<?php echo $app['id']; ?>">
                                            <i class="fas fa-check"></i> Accepter
                                        </button>
                                        <button class="btn btn-danger reject-btn" data-id="<?php echo $app['id']; ?>">
                                            <i class="fas fa-times"></i> Refuser
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- الفوتر -->
    <footer class="footer">
        <div class="copyright">
            <p>© 2024 MediRendez-vous - Tableau de bord personnel | Utilisateur: <?php echo htmlspecialchars($user['nom_complet']); ?></p>
        </div>
    </footer>

    <script>
    $(document).ready(function() {
        $('.accept-btn').click(function() {
            var id = $(this).data('id');
            var button = $(this);
            
            $.ajax({
                url: 'ajax/update_appointment.php',
                type: 'POST',
                data: {id: id, status: 'accepte'},
                success: function(response) {
                    try {
                        var result = JSON.parse(response);
                        if(result.success) {
                            var card = button.closest('.appointment-card');
                            card.find('.status-badge').removeClass('status-en_attente').addClass('status-accepte').html('✅ Confirmé');
                            card.find('.appointment-actions').remove();
                            
                            // تحديث تفاصيل الحالة
                            card.find('.appointment-details p:last strong').parent().html(
                                '<i class="fas fa-info-circle" style="color: #3498db;"></i>' +
                                '<strong>Statut:</strong> Rendez-vous confirmé'
                            );
}
                    } catch(e) {
                        alert('Erreur de traitement');
                    }
                }
            });
        });
        
        $('.reject-btn').click(function() {
            var id = $(this).data('id');
            var button = $(this);
            
            $.ajax({
                url: 'ajax/update_appointment.php',
                type: 'POST',
                data: {id: id, status: 'refuse'},
                success: function(response) {
                    try {
                        var result = JSON.parse(response);
                        if(result.success) {
                            var card = button.closest('.appointment-card');
                            card.find('.status-badge').removeClass('status-en_attente').addClass('status-refuse').html('❌ Refusé');
                            card.find('.appointment-actions').remove();
                            
                            // تحديث تفاصيل الحالة
                            card.find('.appointment-details p:last strong').parent().html(
                                '<i class="fas fa-info-circle" style="color: #3498db;"></i>' +
                                '<strong>Statut:</strong> Rendez-vous refusé'
                            );
                        }
                    } catch(e) {
                        alert('Erreur de traitement');
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
