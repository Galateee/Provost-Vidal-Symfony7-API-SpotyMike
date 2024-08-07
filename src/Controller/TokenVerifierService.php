<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenVerifierService
{

    private $jwtManager;
    private $jwtProvider;
    private $userRepository;

    public function __construct(JWTTokenManagerInterface $jwtManager, JWSProviderInterface $jwtProvider, UserRepository $userRepository)
    {
        $this->jwtManager = $jwtManager;
        $this->jwtProvider = $jwtProvider;
        $this->userRepository = $userRepository;
    }

    /**
     * @return User | Boolean - false if token is not avalaible | null is not send
     */
    public function checkToken(Request $request)
    {

        if ($request->headers->has('Authorization')) {
            $data = explode(" ", $request->headers->get('Authorization'));
            if (count($data) == 2) {
                $token = $data[1];
                try {
                    $dataToken = $this->jwtProvider->load($token);
                    if ($dataToken->isVerified($token)) {
                        $user = $this->userRepository->findOneBy(["email" => $dataToken->getPayload()["email"]]);
                        return ($user) ? $user : false;
                    }
                } catch (\Throwable $th) {
                    return false;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    public function checkTokenWithParam($token)
    {
        try {
            $dataToken = $this->jwtProvider->load($token);
            if ($dataToken->isVerified($token)) {
                $user = $this->userRepository->findOneBy(["email" => $dataToken->getPayload()["email"]]);
                return ($user) ? $user : false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function createToken(string $email): string
    {
        return $this->jwtProvider->create(["email" => $email, "exp" => time() + 120, "iat" => time()])->getToken();
    }


    public function sendJsonErrorToken($nullToken): array
    {
        return [
            'error' => true,
            'message' => ($nullToken) ? "Authentification requise. Vous devez être connecté pour effectuer cette action." : "Vous n'êtes pas autorisé à accéder aux informations de cet artiste.",
        ];
    }
}
