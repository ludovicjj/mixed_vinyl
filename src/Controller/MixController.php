<?php

namespace App\Controller;

use App\Entity\VinylMix;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MixController extends AbstractController
{
    #[Route('/mix/new')]
    public function new(EntityManagerInterface $em): Response
    {
        $mix = new VinylMix();
        $mix->setTitle('Do you Remember... Phil Collins?!')
            ->setDescription('A pure mix of drummers turned singers!')
            ->setGenre('pop')
            ->setTrackCount(rand(5, 20))
            ->setVotes(rand(-50, 50));
        $em->persist($mix);
        $em->flush();

        return new Response(sprintf('Mix %d is %d tracks', $mix->getId(), $mix->getTrackCount()));
    }
}