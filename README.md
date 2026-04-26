# 🌙 MoodTrace - Application de Suivi d'Humeur

---

## 📖 Présentation du projet

**MoodTrace** est une application web de suivi d'humeur et de bien-être mental. Elle permet aux utilisateurs d'enregistrer quotidiennement leur état émotionnel, d'ajouter des réflexions personnelles, de suivre leurs heures de sommeil, et d'obtenir des analyses visuelles de leurs tendances émotionnelles.

L'application intègre **l'intelligence artificielle Google Gemini** pour analyser les journaux intimes et fournir des conseils personnalisés.

---

## 🎯 Problématique

Dans le monde actuel, les gens sont focalisés sur le travail et l'argent, oubliant leur bien-être mental.

**Problèmes identifiés :**
- ❌ Perte de données et absence d'historique fiable
- ❌ Manque de visualisation des tendances émotionnelles
- ❌ Aucune corrélation automatique entre humeur et sommeil
- ❌ Absence d'analyse intelligente des journaux

**Solution :** Une application web avec suivi quotidien, analyses IA, visualisations interactives.

---

## 🏗️ Architecture technique
┌─────────────────────────────────────────────────────────────────┐
│ FRONTEND │
├─────────────────────────────────────────────────────────────────┤
│ Blade (Laravel) + Tailwind CSS + DaisyUI + Chart.js │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ BACKEND │
├─────────────────────────────────────────────────────────────────┤
│ Laravel 11 + PHP 8.2 │
│ • Authentification native + Google OAuth │
│ • Notifications push (WebPush API) │
│ • Service Worker pour les notifications │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ BASE DE DONNÉES │
├─────────────────────────────────────────────────────────────────┤
│ MySQL / PostgreSQL │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ SERVICES EXTERNES │
├─────────────────────────────────────────────────────────────────┤
│ • Google Gemini AI (analyse de journal) │
│ • Google OAuth 2.0 (authentification) │
│ • WebPush Protocol (notifications) │
└─────────────────────────────────────────────────────────────────┘

text

---

## 🛠️ Technologies utilisées

| Catégorie | Technologies |
|-----------|--------------|
| **Backend** | Laravel 12, PHP 8.2 |
| **Frontend** | Blade, Tailwind CSS, DaisyUI, Chart.js |
| **Base de données** | MySQL / PostgreSQL |
| **Authentification** | Laravel Auth, Google OAuth 2.0 |
| **Notifications** | WebPush API, Service Workers, VAPID |
| **IA** | Google Gemini AI REST API |

---

## 📁 Structure du projet (essentielle)
Mood_Tracker/
├── app/
│ ├── Http/
│ │ └── Controllers/
│ │ ├── Admin/
│ │ │ └── UserManagementController.php
│ │ ├── Auth/
│ │ │ ├── AuthController.php
│ │ │ └── GoogleAuthController.php
│ │ ├── AnalyticsController.php
│ │ ├── DashboardController.php
│ │ └── MoodEntryController.php
│ ├── Models/
│ │ ├── User.php
│ │ ├── MoodEntry.php
│ │ ├── Feeling.php
│ │ └── Notification.php
│ ├── Notifications/
│ │ └── PushNotification.php
│ └── Services/
│ └── GeminiService.php
│
├── database/
│ ├── migrations/
│ └── seeders/
│
├── public/
│ ├── css/
│ ├── js/
│ └── sw.js (Service Worker)
│
├── resources/
│ └── views/
│ ├── layouts/
│ ├── admin/
│ ├── auth/
│ ├── dashboard/
│ ├── mood/
│ └── analytics/
│
└── routes/
└── web.php

text

---


---

## 🚀 Installation rapide

```bash
# 1. Cloner le projet
git clone https://github.com/votre-username/mood-trace.git
cd mood-trace

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données
php artisan migrate --seed

# 5. Démarrer le serveur
php artisan serve
