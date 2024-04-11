<?php

namespace App\Controller;


use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MiddlewareController extends AbstractController
{


    private $jwtProvider;

    public function __construct(JWSProviderInterface $jwtProvider){
        $this->jwtProvider = $jwtProvider;


    }


    /**
     * @return User | true | false - false if token is not available | null is not 
     */

     public function checkToken(Request $request, $jwtProvider){
        
        if ($request->headers->has('Authorization')){
            $data = explode("", $request->headers->get('Authorization'));
            if (count ($data) == 2){
                $token = $data[1];
                $dataToken = $this->jwtProvider->load($token);
                if($jwtProvider->isVerified($token)){                  
                    $user = $repository->findOneBy(["email" => $dataToken-> getPayLoad["username"]]);
                    return ($user) ? $user : false;

                }               

            }
        }
        else{
            return true;
        }
        return false;
        
    }

    public function sendJsonErrorToken ($nullToken = true): JsonResponse {
        
        return $this->json{[
            'error' : true,

        ]}

    }

}