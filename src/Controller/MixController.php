<?php

namespace App\Controller;

use App\Repository\VinylMixRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MixController extends AbstractController
{
    #[Route('/mix/{id}', name: 'app_mix_show')]
    public function show($id, VinylMixRepository $mixRepository): Response
    {
        $mix = $mixRepository->find($id);

        return $this->render('mix/show.html.twig', [
           'mix' => $mix
        ]);
    }
}