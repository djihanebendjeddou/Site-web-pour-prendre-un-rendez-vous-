<?php
require_once 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'patient') {
    header("Location: login.php");
    exit;
}

// جلب الأطباء من utilisateurs مع medecins
$doctors = $pdo->query("
    SELECT u.id, u.nom_complet, m.specialite 
    FROM utilisateurs u 
    JOIN medecins m ON u.id = m.utilisateur_id 
    WHERE u.type_utilisateur = 'medecin'
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prendre rendez-vous</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Prendre rendez-vous</div>
            <div class="nav-links">
                <a href="index.php">Accueil</a>
                <a href="dashboard.php">Tableau de bord</a>
                <a href="logout.php">Déconnexion</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Prendre un rendez-vous</h1>
        
        <form id="bookingForm">
            <div class="form-group">
                <label>Choisir un médecin</label>
                <select name="doctor_id" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach($doctors as $doctor): ?>
                        <option value="<?php echo $doctor['id']; ?>">
                            <?php echo $doctor['nom_complet'] . ' - ' . $doctor['specialite']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label>Heure</label>
                <select name="appointment_time" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Notes (optionnel)</label>
                <textarea name="notes" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Réserver</button>
        </form>
        
        <div id="result"></div>
    </div>

    <script>
    $(document).ready(function() {
        $('#bookingForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'ajax/book_rdv.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    try {
                        var result = JSON.parse(response);
                        if(result.success) {
                            $('#result').html('<div class="alert alert-success">' + result.message + '</div>');
                            $('#bookingForm')[0].reset();
                            setTimeout(function() {
                                window.location.href = 'dashboard.php';
                            }, 2000);
                        } else {
                            $('#result').html('<div class="alert alert-error">' + result.message + '</div>');
                        }
                    } catch(e) {
                        $('#result').html('<div class="alert alert-error">Erreur de traitement</div>');
                    }
                }
            });
        });
    });
    </script>
</body>
</html>