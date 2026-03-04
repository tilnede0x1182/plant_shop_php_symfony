# RAPPORT D'ANALYSE DES DUPLICATIONS DE CODE (Principe DRY)

## Introduction
Projet Symfony (controllers + Twig + Prisma? non). On constate plusieurs répétitions structurelles.

---

## Violations DRY

### 1. Deux contrôleurs Plantes pour la même logique - 🔴 Critique
`App\Controller\PlanteController` (public) et `App\Controller\Admin\PlanteController` réécrivent les mêmes requêtes Doctrine (`orderBy name`, `stock > 0`) et renvoient des vues différentes. Ajout d’un champ ou d’une règle de validation oblige à toucher deux contrôleurs + deux templates. **Action** : créer un service `PlantService` (liste + CRUD) et des composants Twig partagés, afin que les contrôleurs ne fassent que la gestion de routing.

### 2. Formulaires utilisateurs/plantes utilisés différemment mais déclarés pareil - 🟠 Haute
Les contrôleurs admin et public créent `PlanteType`/`UserType` puis appellent `remove('admin')`, `remove('plainPassword')` selon le contexte. Cela crée des doublons (même formulaire instancié puis mutilé). **Action** : définir des Form Types spécifiques (`AdminUserType`, `ProfileUserType`, `AdminPlantType`, etc.) ou utiliser des options (`'includeAdminField' => true`) pour éviter de répéter les suppressions et réduire les risques d’oubli.

### 3. Gestion utilisateurs (profil vs admin) dupliquée - 🟠 Haute
`App\Controller\UtilisateurController` et `App\Controller\Admin\UtilisateurController` contiennent tous deux les mêmes opérations (chargement de l’utilisateur, formulaire `UserType`, sauvegarde, flash, redirection). La seule différence réside dans les routes et les permissions. **Action** : factoriser la logique dans un service (`UserManager::updateUser($user, $data, $allowRoleChange)`) et dans des templates partagés (composants Twig) pour respecter DRY.

---

## Impact estimé

| Refactoring proposé                                       | Gain estimé | Complexité |
|-----------------------------------------------------------|-------------|------------|
| Service partagé pour la gestion des plantes               | ~100 lignes | Faible     |
| Form Types spécialisés ou configurables                   | ~40 lignes  | Faible     |
| Service + templates communs pour la gestion utilisateur   | ~80 lignes  | Moyenne    |

---

## Conclusion
La séparation public/admin repose sur des copies quasi mot à mot des contrôleurs et formulaires. Sans mutualisation, chaque évolution (nouveau champ, sécurité) devra être répliquée plusieurs fois. Centraliser ces responsabilités est indispensable pour honorer le principe DRY.***
