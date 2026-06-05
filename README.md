# PlantDoc

Application de diagnostic phytosanitaire par IA pour les agriculteurs camerounais.

## Stack
- PHP 8.2+ (MVC custom)
- MySQL 8 / MariaDB 10.5+
- Claude Haiku Vision API
- Vanilla JS + FontAwesome
- WAMP / XAMPP en dev

## Installation

```bash
# 1. Copier dans WAMP
cp -r plantdoc/ C:/wamp64/www/

# 2. Configurer
cd C:/wamp64/www/plantdoc
cp .env.example .env
# Éditer .env (DB_PASS, ANTHROPIC_API_KEY)

# 3. Importer le schéma
mysql -u root -p < database/schema.sql

# 4. Permissions
chmod -R 755 public/uploads logs
```

## Accès
- URL : http://localhost/plantdoc
- Compte admin par défaut : `admin@plantdoc.cm` / `password`
  ⚠️ **Changer immédiatement** en production

## Structure
```
plantdoc/
├── index.php              # Front controller
├── .env                   # Config (hors Git)
├── .htaccess              # Routing Apache
├── config/                # env, app, database
├── core/                  # Router, Controller, Model, Database
├── controllers/           # Home, Auth, Diagnostic, Admin
├── models/                # User, Diagnostic, Maladie, Culture, Region
├── services/              # AIService (Claude)
├── helpers/               # csrf, upload, logger, html, ratelimit
├── views/                 # layouts + auth/diagnostic/admin/home
├── public/                # css, js, uploads
├── database/schema.sql    # Schéma BDD
└── logs/                  # Logs applicatifs
```

## Sécurité implémentée
- CSRF tokens sur tous les formulaires
- bcrypt pour mots de passe
- Rate limiting (login, register, diagnostic)
- Validation MIME upload + dimensions
- XSS via `h()` systématique
- Session régénérée à login (anti-fixation)
- En-têtes sécurité (.htaccess)
- Logs d'audit
