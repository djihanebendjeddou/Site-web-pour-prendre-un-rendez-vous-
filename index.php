<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRendez-vous - Plateforme de Réservation Médicale</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="https://img.icons8.com/color/96/000000/medical-doctor.png" type="image/png">
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
                    <div class="logo-text">MediRendez-vous</div>
                    <div class="logo-subtext">Votre santé, notre priorité</div>
                </div>
            </a>
            
            <nav class="nav-menu">
                <a href="index.php" class="nav-link active">
                    <i class="fas fa-home"></i> Accueil
                </a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-badge">
                        <i class="fas fa-user"></i>
                        <span><?php echo $_SESSION['full_name'] ?? 'Utilisateur'; ?></span>
                    </div>
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                    <a href="register.php" class="nav-link">
                        <i class="fas fa-user-plus"></i> Inscription
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- المحتوى principal -->
    <main>
        <!-- قسم Hero -->
        <section class="hero-section">
            <div class="container">
                <h1 class="hero-title">Prenez soin de votre santé en toute simplicité</h1>
                <p class="hero-subtitle">
                    Réservez vos rendez-vous médicaux en ligne, gérez vos consultations 
                    et suivez votre santé avec notre plateforme intuitive et sécurisée.
                </p>
                
                <div class="hero-buttons">
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="register.php?type=patient" class="btn btn-primary">
                            <i class="fas fa-user-injured"></i> Je suis un Patient
                        </a>
                        <a href="register.php?type=medecin" class="btn btn-secondary">
                            <i class="fas fa-user-md"></i> Je suis un Médecin
                        </a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Accéder à mon espace
                        </a>
                        <a href="book_rdv.php" class="btn btn-secondary">
                            <i class="fas fa-calendar-plus"></i> Prendre un rendez-vous
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- الميزات -->
        <section class="features-section">
            <div class="container">
                <div class="section-title">
                    <h2>Pourquoi choisir MediRendez-vous ?</h2>
                    <p>Une expérience de prise de
rendez-vous médical simplifiée et efficace</p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Trouvez le bon spécialiste</h3>
                        <p>Accédez à notre réseau de médecins qualifiés par spécialité, localisation et disponibilité.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Réservation en quelques clics</h3>
                        <p>Choisissez la date et l'heure qui vous conviennent parmi les créneaux disponibles.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Rappels intelligents</h3>
                        <p>Recevez des rappels par email et SMS pour ne jamais oublier vos rendez-vous.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Sécurité des données</h3>
                        <p>Vos informations médicales sont cryptées et protégées conformément aux normes.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Communication facilitée</h3>
                        <p>Échangez directement avec votre médecin via notre messagerie sécurisée.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Suivi personnalisé</h3>
                        <p>Consultez votre historique médical et suivez l'évolution de votre santé.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- دعوة للعمل -->
        <section class="container">
            <div class="feature-card" style="max-width: 800px; margin: 0 auto; text-align: center;">
                <div class="feature-icon" style="margin-bottom: 30px;">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h2>Prêt à prendre le contrôle de votre santé ?</h2>
                <p style="margin-bottom: 30px; font-size: 1.1rem;">
                    Rejoignez des milliers de patients et médecins qui utilisent déjà MediRendez-vous
                    pour simplifier leur parcours de soins.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-primary" style="padding: 15px 40px;">
                            <i class="fas fa-rocket"></i> Commencer maintenant
                        </a>
                        <a href="login.php" class="btn btn-secondary" style="padding: 15px 40px;">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                    <?php else: ?>
                        <a href="book_rdv.php" class="btn btn-primary" style="padding: 15px 40px;">
                            <i class="fas fa-calendar-plus"></i> Prendre un rendez-vous
                        </a>
                    <?php endif; ?>
</div>
            </div>
        </section>
    </main>

    <!-- الفوتر -->
    <footer class="footer">
        <div class="footer-container">
            <div>
                <div class="footer-logo">
                    <img src="https://img.icons8.com/color/96/000000/medical-doctor.png" 
                         alt="Logo" 
                         style="height: 40px; width: 40px; filter: brightness(0) invert(1);">
                    <div>
                        <div style="font-size: 20px; font-weight: bold;">MediRendez-vous</div>
                        <div style="font-size: 12px; opacity: 0.8;">Santé digitale</div>
                    </div>
                </div>
                <p style="color: #bdc3c7; margin-bottom: 20px;">
                    Simplifiez votre parcours de soins avec notre plateforme de prise de rendez-vous médicaux en ligne.
                </p>
            </div>
            
            <div class="footer-links">
                <h4>Navigation</h4>
                <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Inscription</a>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            </div>
            
            <div class="footer-links">
                <h4>À propos</h4>
                <a href="#"><i class="fas fa-info-circle"></i> Notre mission</a>
                <a href="#"><i class="fas fa-users"></i> Équipe</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Confidentialité</a>
                <a href="#"><i class="fas fa-file-contract"></i> Conditions</a>
            </div>
            
            <div class="footer-links">
                <h4>Contact</h4>
                <a href="#"><i class="fas fa-envelope"></i> contact@medirendezvous.fr</a>
                <a href="#"><i class="fas fa-phone"></i> 01 23 45 67 89</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> Paris, France</a>
                <div style="margin-top: 20px; display: flex; gap: 15px;">
                    <a href="#" style="color: white; font-size: 20px;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: white; font-size: 20px;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: white; font-size: 20px;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2024 MediRendez-vous. Tous droits réservés. | Projet académique - Système de gestion de rendez-vous médicaux</p>
        </div>
    </footer>
</body>
</html>