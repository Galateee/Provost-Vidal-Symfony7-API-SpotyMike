## Etat des lieux:

1: Route d'authentication d'un utilisateur POST /login -> manque succès, compte non activé ou suspendu et trop de tentatives (rate limiting)  
2: Route d'inscription d'un utilisateur POST /register -> manque succès  
3: Route de mise à jour de compte user POST /user -> manque non authentifié et erreur de validation  
4: Route de suppression du compte POST /password-lost -> manque trop de demandes  
5: Route de réinitialisation de mot de passe GET /reset-password/{token} -> manque token manquant ou invalide et token expiré  
6: Route de suppression du compte DELETE /account-deactivation -> manque succes, non authentifié et compte déjà désactivé  
7: Route création artist POST /artist -> manque succes, non authentifié et compte artist existant pour l'utilisateur   
8: Route de récupération de toutes les infos des artistes GET /artist -> manque succes et non authentifié  
9: Route de récupération de toutes les infos d'un artiste GET /artist/xxx -> manque succes et non authentifié  
10: Route de mise à jour de compte artist POST /artist -> pas fait  
11: Route de désactivation du compte artist DELETE /artist -> pas fait  
