<?php

namespace App\Controller;

use App\Entity\VinylMix;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class MixController extends AbstractController
{
    #[Route('/mix/{id}', name: 'app_mix_show', requirements: ['id' => Requirement::DIGITS])]
    public function show(VinylMix $mix): Response
    {
        return $this->render('mix/show.html.twig', [
           'mix' => $mix
        ]);
    }

    #[Route('/mix/{id}/vote', name: 'app_mix_vote', methods: ['POST'])]
    public function vote(VinylMix $mix, Request $request, EntityManagerInterface $entityManager): Response
    {
        $direction = $request->request->get('direction', 'up');
        if ($direction === 'up') {
            $mix->upVote();
        } else {
            $mix->downVote();
        }
        $entityManager->flush();
        $this->addFlash('success', 'Vote counted');

        return $this->redirectToRoute('app_mix_show', [
            'id' => $mix->getId()
        ]);
    }

    #[Route('/mix/new')]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $genre = ['pop', 'rock'];
        $mix = new VinylMix();
        $mix->setTitle('Do you Remember... Phil Collins?!');
        $mix->setDescription('A pure mix of drummers turned singers!');
        $mix->setGenre($genre[array_rand($genre)]);
        $mix->setTrackCount(rand(5, 20));
        $mix->setVotes(rand(-50, 50));

        $entityManager->persist($mix);
        $entityManager->flush();

        return new Response(sprintf(
            'Mix %d is created with %d tracks',
            $mix->getId(),
            $mix->getTrackCount()
        ));
    }
}