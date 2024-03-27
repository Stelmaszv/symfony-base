<?php

namespace App\Generic\Components;

use Symfony\Component\Uid\Uuid;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Generic\Api\Identifier\Interfaces\IdentifierUid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractFixtureGeneric  extends Fixture
{
    protected UserPasswordHasherInterface $passwordEncoder;
    protected ?string $enetity = null;
    protected array $data = [];

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->passwordEncoder = $userPasswordHasher;

        if($this->enetity === null){
            throw new \Exception("Entity is not define in Fixture ".get_class($this)."!");
        }
    }
    
    public function load(ObjectManager $manager): void
    {
        foreach($this->data as $elelemnts){
            $enetityObj = new $this->enetity();
            $idetikatorUid = $enetityObj instanceof IdentifierUid;

            if($idetikatorUid){
                $enetityObj?->setId(Uuid::v4());
            }
            
            foreach($elelemnts as $field => $value){

                    $setMethod = "set" . ucfirst($field);

                    if (method_exists($this, "on" . ucfirst($field) . "Set")) {
                        $onMethodSet = "on" . ucfirst($field) . "Set";
                        $value = $this->$onMethodSet($value, $enetityObj);
                    }

                    $enetityObj?->$setMethod($value);
            }
            
            $manager->persist($enetityObj);
            $manager->flush();
            
        }
 
    }
}