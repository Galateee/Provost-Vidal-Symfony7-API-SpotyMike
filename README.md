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
! 10: Route de récupération de toutes les infos d'un artiste GET /artist/{fullname} -> succes à revoir  
+ 11: Route de mise à jour de compte artist POST /artist -> FINI  
+ 12: Route de désactivation du compte artist DELETE /artist -> FINI  
! 13: Route de récupération des albums GET /albums -> succes à revoir  
! 14: Route de récupération d'un album GET /album/{id} -> succes à revoir  
! 15: Route de recherche d'albums GET /album/search -> manque Featuring invalide, Année invalide & succes à revoir  
+ 16: Route de création d'un album POST /album -> FINI  
+ 17: Route de modification d'un album PUT /album/{id} -> FINI  
+ 18: Route d'ajout de song POST /album/{id}/song  -> FINI  
```
Questions:  
Est ce que le /user doit créer un utilisateur?  / Qu'est ce que /user est censé faire?  

fix:
- probleme avec les serializer / Les succes