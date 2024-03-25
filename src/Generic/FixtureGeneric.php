<?php


namespace App\Generic;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
class FixtureGeneric  extends Fixture
{
    public function load(ObjectManager $manager): void
    {
    }
}