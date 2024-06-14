## Etat des lieux:

```diff
+ 2: Route d'authentication d'un utilisateur POST /login -> FINI  
+ 3: Route d'inscription d'un utilisateur POST /register -> FINI  
+ 4: Route de création de compte user POST /user -> FINI  
! 5: Route de password lost POST /password-lost -> manque les token avec expiration de 2 min  
+ 6: Route de réinitialisation de mot de passe GET /reset-password/{token} -> FINI  
+ 7: Route de suppression du compte DELETE /account-deactivation -> FINI  
+ 8: Route création artist POST /artist -> FINI  
! 9: Route de récupération de toutes les infos des artistes GET /artist -> manque success -> avatar
! 10: Route de récupération de toutes les infos d'un artiste GET /artist/{fullname} -> manque succes
- 11: Route de mise à jour de compte artist POST /artist -> pas fait  
+ 12: Route de désactivation du compte artist DELETE /artist -> FINI  
! 13: Route de récupération des albums -> manque succes
- 14: Route de récupération d'un album -> pas fait  
- 15: Route de recherche d'albums -> pas fait  
! 16: Route de création d'un album -> manque accès refusé  
- 17: Route de modification d'un album -> pas fait  
- 18: Route d'ajout de song -> pas fait  
```
Questions:  
Est ce que le /user doit créer un utilisateur?  / Qu'est ce que /user est censé faire?  
Il y a 2 route /artist, une pour la création et l'autre pour la mise à jour comment faire?  

fix:
- probleme avec les serializer / Les succes