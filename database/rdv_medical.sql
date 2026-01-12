-- حذف القاعدة إذا كانت موجودة وإنشاؤها من جديد
DROP DATABASE IF EXISTS rdv_medical;
CREATE DATABASE rdv_medical CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rdv_medical;

-- جدول المستخدمين
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_utilisateur VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    type_utilisateur ENUM('patient', 'medecin') NOT NULL,
    nom_complet VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الأطباء
CREATE TABLE medecins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT UNIQUE,
    specialite VARCHAR(100),
    description TEXT,
    disponibilite VARCHAR(100),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول المواعيد
CREATE TABLE rendezvous (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT,
    medecin_id INT,
    date_rendezvous DATE NOT NULL,
    heure_rendezvous TIME NOT NULL,
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    notes TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (medecin_id) REFERENCES utilisateurs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- كلمات مرور مشفرة: 123456 (للتجربة فقط)
INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, type_utilisateur, nom_complet, telephone) VALUES
('docteur1', 'docteur1@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'medecin', 'Dr. Jean Martin', '0612345678'),
('patient1', 'patient1@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'Marie Dupont', '0698765432'),
('docteur2', 'docteur2@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'medecin', 'Dr. Sophie Bernard', '0601020304');

INSERT INTO medecins (utilisateur_id, specialite, disponibilite) VALUES
(1, 'Médecine Générale', 'Lundi - Vendredi, 9h - 17h'),
(3, 'Cardiologie', 'Mardi - Jeudi, 10h - 18h');

INSERT INTO rendezvous (patient_id, medecin_id, date_rendezvous, heure_rendezvous, statut, notes) VALUES
(2, 1, '2024-12-20', '10:00:00', 'accepte', 'Consultation générale');