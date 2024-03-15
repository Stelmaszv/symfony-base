<?php


namespace App\Generic\Api\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;

trait Security
{
    protected ?string $voterAtribute = null;
    protected ?string $voterSubject = null;

    private function view(string $action): JsonResponse
    {
        $subject = ($this->voterAtribute !== null) ? new $this->voterSubject() : null;

        if(
            (null === $this->voterAtribute && $subject === null) || 
            (null !== $this->voterAtribute && $subject === null)){
            return $this->$action();
        }
        
        if(null !== $this->voterAtribute && $subject !== null){
            $vote = $this->security->isGranted($this->voterAtribute, $subject);
            if ($vote) {
                return $this->$action();
            }
        }

        return new JsonResponse(['Access Denied'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}