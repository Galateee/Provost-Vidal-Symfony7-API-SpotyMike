## Etat des lieux:

```diff
! 2: Route d'authentication d'un utilisateur POST /login -> manque token dans le succes  
+ 3: Route d'inscription d'un utilisateur POST /register -> FINI  
! 4: Route de création de compte user POST /user -> manque non authentifié  
! 5: Route de password lost POST /password-lost -> manque token dans le succes  
! 6: Route de réinitialisation de mot de passe GET /reset-password/{token} -> manque token manquant ou invalide et token expiré  
! 7: Route de suppression du compte DELETE /account-deactivation -> manque succes, non authentifié et compte déjà désactivé  
! 8: Route création artist POST /artist -> manque succes, non authentifié et compte artist existant pour l'utilisateur   
! 9: Route de récupération de toutes les infos des artistes GET /artist -> manque succes et non authentifié  
! 10: Route de récupération de toutes les infos d'un artiste GET /artist/xxx -> manque succes et non authentifié  
- 11: Route de mise à jour de compte artist POST /artist -> pas fait  
- 12: Route de désactivation du compte artist DELETE /artist -> pas fait  
- 13: Route de récupération des albums -> pas fait  
- 14: Route de récupération d'un album -> pas fait  
- 15: Route de recherche d'albums -> pas fait  
- 16: Route de création d'un album -> pas fait  
- 17: Route de modification d'un album -> pas fait  
- 18: Route d'ajout de song -> pas fait  
```