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

    public function invalidDataProvided(): JsonResponse
    {
        return new JsonResponse(['error' => 'true','message' => 'Données fournies non valides.'], 400);
    }
    
    
    
    
    // TODO
    // manque les erreurs de : "route de mise à jour de compte user" POST /user 

    // Ajoutez d'autres fonctions pour gérer les exceptions supplémentaires selon vos besoins
}