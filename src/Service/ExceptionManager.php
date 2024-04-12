<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionManager
{
    public function missingData(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Des champs obligatoires sont manquants.'], 400);
    }

    public function invalidEmail(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Le format de l\'email est invalide'], 400);
    }

    public function invalidPasswordCriteria(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et avoir 8 caractères minimum.'], 400);
    }

    public function inactiveAccount(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Le compte n\'est plus actif ou est suspendu.'], 403);
    }

    public function invalidPassword(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Mot de passe incorrect.'], 400 );
    }

    public function maxPasswordTry(): JsonResponse
    {
     return new JsonResponse(['error' => 'true','message' => 'Trop de tentatives de connexion (5 max). Veuillez réessayer ultérieurement - xxx min d\'attente'], 429);
    }
    
    public function invalidDateOfBirthFormat(): JsonResponse
    {
     return new JsonResponse(['error' => 'true','message' => 'Le format de la date de naissance est invalide. Le format attendu est JJ/MM/AAAA.'], 400);
    }

    public function minimumAgeNotMet(): JsonResponse
    {
     return new JsonResponse(['error' => 'true','message' => 'L\'utilisateur doit avoir au moins 12 ans.'], 400);
    }

    public function invalidPhoneNumberFormat(): JsonResponse
    {
     return new JsonResponse(['error' => 'true','message' => 'Le format du numéro de téléphone est invalide.'], 400);
    }
    
    public function invalidGenderValue(): JsonResponse
    {
     return new JsonResponse(['error' => 'true','message' => 'La valeur du champ sexe est invalide. Les valeurs autorisées sont 0 pour Femme, 1 pour Homme.'], 400);
    }
    
    public function emailAlreadyUsed(): JsonResponse
    {
     return new JsonResponse(['error' => 'true','message' => 'Cet email est déjà utilisé par un autre compte'], 409);
    }
    
    public function userDontExist(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'L\'utilisateur n\'existe pas.'], 400);
    }

    public function noDataProvided(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Aucune donnée'], 400);
    }

    public function invalidDataProvided(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Les données fournies sont invalides ou imcomplètes'], 400);
    }

    public function telAlreadyUsed(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Conflit de données. Le numéro de téléphone est déjà utilisé par  un autre utilisateur.'], 409);
    }

    public function EmailMissing(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Email manquant. Veuillez fournir votre email pour la récupération du mot de passe.'], 400);
    }
    
    public function emailNotFound(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Aucun compte n\'est associé à cet email. Veuillez vérifier et réessayer.'], 404);
    }
    
    public function invalidNewPassword(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Veuillez fournir un nouveau mot de passe'], 400);
    }
    
    public function invalidNewPasswordCriteria(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Le nouveau mot de passe ne respecte pas les critères requis. Il doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et être composé d\'au moins 8 caractères.'], 400);
    }

    public function invalidLabelFormat(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Le format du label est invalide.'], 400);
    }

    public function minimumAgeForArtist(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Vous devez avoir au moins 16 ans pour être artiste.'], 403);
    }
    
    public function artistAllreadyExist(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Ce nom d\'artiste est déjà pris. Veuillez en choisir un autre.'], 409);
    }

    public function invalidPaginationValue(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Le paramètre de pagination est invalide. Veuillez fournir un numéro de page valide.'], 400);
    }

    public function NoArtistInPagination(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Aucun artiste trouvé pour la page demandée.'], 404);
    }
    
    
    
    // TODO
    // manque les erreurs de : "route de mise à jour de compte user" POST /user 

    // Ajoutez d'autres fonctions pour gérer les exceptions supplémentaires selon vos besoins
}