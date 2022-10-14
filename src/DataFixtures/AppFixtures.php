<?php

namespace App\DataFixtures;

use App\Entity\VinylMix;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $mix = new VinylMix();
        $mix->setTitle('Do you Remember... Phil Collins?!');
        $mix->setDescription('A pure mix of drummers turned singers!');
        $genre = ['pop', 'rock'];
        $mix->setGenre($genre[array_rand($genre)]);
        $mix->setTrackCount(rand(5, 20));
        $mix->setVotes(rand(-50, 50));

        $manager->persist($mix);
        $manager->flush();
    }
}
