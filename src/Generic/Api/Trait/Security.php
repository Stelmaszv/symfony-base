<?php
namespace App\Generic\Api\Trait;

use App\Security\Roles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

trait Security
{
    protected ?string $voterAtribute = null;
    protected ?string $voterSubject = null;
    
    private function setSecurityView(string $action,TokenStorageInterface $token): JsonResponse
    {
        $subject = ($this->voterSubject !== null) ? new $this->voterSubject() : null;

        if(null === $this->voterAtribute && $subject === null){
            return $this->$action();
        }
        
        if((null !== $this->voterAtribute && $subject !== null) || (null !== $this->voterAtribute && $subject === null)){

            $userToken = $token->getToken()?->getUser();

            if($userToken === null){
                return $this->response();
            }

            foreach($userToken->getRoles() as $role){
                if(Roles::checkAtribute($role,$this->voterAtribute)){
                    $vote = $this->security->isGranted($this->voterAtribute, $subject);
                    if($subject !== null && $vote){
                        return $this->$action();
                    }

                    return $this->$action();
                }
            }
        }

        return $this->response();
    }

    private function response() :JsonResponse
    {
        return new JsonResponse(['success' => false,"message" => 'Access Denied'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}