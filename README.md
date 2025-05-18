```markdown
# 🌿 PlantShop – Boutique Symfony de Plantes

Site de vente de plantes en ligne permettant à des utilisateurs de commander des plantes disponibles. Les administrateurs peuvent gérer les plantes et les utilisateurs via une interface dédiée.

## 🛠️ Technologies utilisées

- **PHP** 8.4.6
- **Symfony** 6+
- **PostgreSQL** (via Doctrine ORM)
- **Twig** (moteur de templates)
- **Bootstrap** 5.3 (via CDN)
- **JavaScript** vanilla + **Axios** (via CDN)
- **Alpine.js** (via CDN)
- **HTML5/CSS3**
- **Make** (via Makefile)
- **Composer**
- **Symfony CLI** (pour le serveur local)


## 📦 Entrées du `Makefile`

- `make run` : Démarre le serveur Symfony sur le port 8004 après un `clear`
- `make db-seed` : Exécute les seeds de développement via la commande personnalisée `app:seed:dev`
- `make db-reset` : Alias vers `db-seed` pour réinitialiser la base avec les données de dev
```
