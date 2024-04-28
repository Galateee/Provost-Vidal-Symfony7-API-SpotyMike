<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionManager
{

    //route GET/ 
    // erreur 404 classique

    //route POST/login

    public function missingDataLogin(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Email/password manquants.'], 400);
    }

    public function invalidEmailLogin(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le format de l\'email est invalide.'], 400);
    }

    public function invalidPasswordCriteriaLogin(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et avoir 8 caractères minimum.'], 400);
    }

    public function inactiveAccountLogin(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le compte n\'est plus actif ou est suspendu.'], 403);
    }

    public function maxPasswordTryLogin(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Trop de tentatives de connexion (5 max). Veuillez réessayer ultérieurement - 2 min d\'attente.'], 429);
    }

    // BONUS
    public function userNotFoundLogin(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'L\'utilisateur n\'existe pas.'], 400);
    }
    // BONUS
    public function invalidCredentialsLogin(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Mauvais mot de passe.'], 400);
    }



    //route POST/register

    public function missingDataRegister(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Des champs obligatoires sont manquants.'], 400);
    }

    public function invalidEmailRegister(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le format de l\'email est invalide.'], 400);
    }

    public function invalidPasswordCriteriaRegister(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et avoir 8 caractères minimum.'], 400);
    }

    public function invalidDateOfBirthFormatRegister(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Le format de la date de naissance est invalide. Le format attendu est JJ/MM/AAAA.'], 400);
    }

    public function minimumAgeNotMetRegister(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'L\'utilisateur doit avoir au moins 12 ans.'], 400);
    }

    public function invalidPhoneFormatRegister(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Le format du numéro de téléphone est invalide.'], 400);
    }

    public function invalidGenderValueRegister(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'La valeur du champ sexe est invalide. Les valeurs autorisées sont 0 pour Femme, 1 pour Homme.'], 400);
    }

    public function emailAlreadyUsedRegister(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Cet email est déjà utilisé par un autre compte.'], 409);
    }
    
    // BONUS
    public function invalidDataProvidedRegister(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les données fournies sont invalides ou imcomplètes'], 400);
    }


    //route POST/user

    public function invalidPhoneNumberFormatUser(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Le format du numéro de téléphone est invalide.'], 400);
    }

    public function invalidGenderValueRegisterUser(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'La valeur du champ sexe est invalide. Les valeurs autorisées sont 0 pour Femme, 1 pour Homme.'], 400);
    }

    public function invalidDataProvidedUser(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les données fournies sont invalides ou incomplètes.'], 400);
    }

    public function noAuthenticationUser(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function telAlreadyUsedUser(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Conflit de données. Le numéro de téléphone est déjà utilisé par un autre utilisateur.'], 409);
    }

    public function errorDataValidationUser(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Erreur de validation des données.'], 422);
    }



    //route POST/password-lost

    public function EmailMissingPassLost(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Email manquant. Veuillez fournir votre email pour la récupération du mot de passe.'], 400);
    }

    public function invalidEmailPassLost(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le format de l\'email est invalide. Veuillez entrer un email valide.'], 400);
    }

    public function emailNotFoundPassLost(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun compte n\'est associé à cet email. Veuillez vérifier et réessayer.'], 404);
    }

    public function lotTryPassLost(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Trop de demandes de réinitialisation de mot de passe (3 max). Veuillez attendre avant de réessayer (Dans xxx min).'], 429);
    }



    //route POST/reset-password/{token}

    public function noTokenResetPass(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Token de réinitialisation manquant ou invalide. Veuillez utiliser le lien fourni dans l\'email de réinitialisation de mot de passe.'], 400);
    }

    public function newMDPResetPass(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Veuillez fournir un nouveau mot de passe.'], 400);
    }

    public function invalidFormatMDPResetPass(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Le nouveau mot de passe ne respecte pas les critères requis. Il doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et être composé d\'au moins 8 caractères.'], 400);
    }

    public function tokenExpirationResetPass(): JsonResponse
    {
     return new JsonResponse(['error' => true,'message' => 'Votre token de réinitialisation de mot de passe a expiré. Veuillez refaire une demande de réinitialisation de mot de passe.'], 410);
    }



   
    //route DELETE/account-desactivation

    public function noAuthenticationAccDesa(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function isAccDesa(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le compte est déjà désactivé.'], 409);
    }



    //route POST/artist  creation artist

    public function noDataCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'L\'id du label et le fullname sont obligatoires.'], 400);
    }

    public function invalidLabelFormatCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le format du label est invalide.'], 400);
    }

    public function noAuthenticationCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function minimumAgeCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Vous devez avoir au moins 16 ans pour être artiste.'], 403);
    }

    public function artistAlreadyExistCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Ce nom d\'artiste est déjà pris. Veuillez en choisir un autre.'], 409);
    }

    public function decodageCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le serveur ne peut pas décoder le contenu base64 en fichier binaire.'], 422);
    }

    public function errorFormatFileCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Erreur sur le format du fichier qui n\'est pas pris en compte.'], 422);
    }

    public function sizeFileCreateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le fichier envoyé est trop ou pas assez volumineux. Vous devez respecter la taille entre 1Mb et 7Mb.'], 422);
    }



    //route GET/artist

    public function invalidPaginationValueGetArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le paramètre de pagination est invalide. Veuillez fournir un numéro de page valide.'], 400);
    }

    public function noAuthenticationGetArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function NoArtistInPaginationGetArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun artiste trouvé pour la page demandée.'], 404);
    }



    //route GET/artist/{fullname}

    public function missingArtistNameArtistFullname(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le nom d\'artiste est obligatoire pour cette requête.'], 400);
    }

    public function invalidArtistNameFormatArtistFullname(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le format du nom d\'artiste fourni est invalide.'], 400);
    }

    public function noAuthenticationArtistFullname(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }
    
    public function artistNotFoundArtistFullname(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun artiste trouvé correspondant au nom fourni.'], 404);
    }



    //route POST/artist  update artist

    public function invalideParameterUpdateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les peramètres fournis sont invalides. Veuillez vérifier les données soumises.'], 400);
    }

    public function noAuthenticationUpdateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function accesDeniedUpdateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Vous n\'êtes pas autorisé à accéder aux informations de cet artiste.'], 403);
    }

    public function nameUsedUpdateArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le nom d\'artiste est déjà utilisé. Veuillez choisir un autre nom.'], 409);
    }



    //route DELETE/artist

    public function noAuthenticationDeleteArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }
    
    public function nameUsedDeleteArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Compte artiste non trouvé. Vérifiez les informations fournies et réessayez.'], 404);
    }

    public function isDeleteArtist(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Compte artiste est déjà désactivé.'], 410);
    }



    //route GET/albums

    public function invalidPaginationValueGetAlbums(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le paramètre de pagination est invalide. Veuillez fournir un numéro de page valide.'], 400);
    }

    public function noAuthenticationGetAlbums(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function albumNotFoundGetAlbums(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun album trouvé pour la page demandée.'], 404);
    }



    //route GET/album/{id}

    public function obligatoryIdAlbumId(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'L\'id de l\'album est obligatoire pour cette requête.'], 400);
    }

    public function noAuthenticationAlbumId(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function albumNotFoundAlbumId(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'L\'album non trouvé. Vérifiez les informations fournies et réessayez.'], 404);
    }



    //route GET/album/search

    public function invalidPaginationValueSearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le paramètre de pagination est invalide. Veuillez fournir un numéro de page valide.'], 400);
    }

    public function invalidParameterSearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les paramètres fournis sont invalides. Veuillez vérifier les données soumises.'], 400);
    }

    public function invalidCategorySearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les catégories ciblées sont invalides.'], 400);
    }

    public function invalidFeaturingSearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les featuring ciblés sont invalides.'], 400);
    }

    public function noAuthenticationSearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function albumNotFoundSearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun album trouvé pour la page demandée.'], 404);
    }

    public function invalidYearSearch(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'L\'année n\'est pas valide.'], 400);
    }



    //route POST/album & route PUT/album/{id}

    public function invalidParameterPostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les paramètres fournis sont invalides. Veuillez vérifier les données soumises.'], 400);
    }

    public function invalidVisibilityPostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'La valeur du champ visibility est invalide. Les valeurs autorisées sont 0 pour invisible, 1 pour visible.'], 400);
    }

    public function invalidCategoryPostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Les catégories ciblées sont invalides.'], 400);
    }

    public function noAuthenticationPostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function noAutorisationPostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Vous n\'avez pas l\'autorisation pour accéder à cet album.'], 403);
    }

    //uniquement pour la route PUT/album/{id}
    public function albumNotFoundPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun album trouvé correspondant au nom fourni.'], 404);
    }

    public function titleUsePostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Ce titre est déjà pris. Veuillez en choisir un autre.'], 409);
    }

    public function validationDataErrorPostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Erreur de validation des données.'], 422);
    }
    
    public function decodagePostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le serveur ne peut pas décoder le contenu base64 en fichier binaire.'], 422);
    }

    public function errorFormatFilePostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Erreur sur le format du fichier qui n\'est pas pris en compte.'], 422);
    }

    public function sizeFilePostPutAlbum(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le fichier envoyé est trop ou pas assez volumineux. Vous devez respecter la taille entre 1Mb et 7Mb.'], 422);
    }



    //route POST/album/{id}/song

    public function noAuthenticationAlbumSong(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Authentification requise. Vous devez être connecté pour effectuer cette action.'], 401);
    }

    public function accessDeniedAlbumSong(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Vous n\'avez pas l\'autorisation pour accéder à cet album.'], 403);
    }

    public function notFoundAlbumSong(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Aucun album trouvé correspondant au nom fourni.'], 404);
    }

    public function errorFormatFileAlbumSong(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Erreur sur le format du fichier qui n\'est pas pris en compte.'], 422);
    }

    public function sizeFileCreateAlbumSong(): JsonResponse
    {
        return new JsonResponse(['error' => true,'message' => 'Le fichier envoyé est trop ou pas assez volumineux. Vous devez respecter la taille entre 1Mb et 7Mb.'], 422);
    }
    
}