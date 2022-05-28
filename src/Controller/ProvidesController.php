<?php

namespace App\Controller;

use App\Entity\Provides;
use App\Form\ProvidesType;
use App\Repository\ProvidesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/provides')]
class ProvidesController extends AbstractController
{
    #[Route('/', name: 'app_provides_index', methods: ['GET'])]
    public function index(ProvidesRepository $providesRepository): Response
    {
        return $this->render('provides/index.html.twig', [
            'provides' => $providesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_provides_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProvidesRepository $providesRepository): Response
    {
        $provide = new Provides();
        $form = $this->createForm(ProvidesType::class, $provide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $providesRepository->add($provide, true);

            return $this->redirectToRoute('app_provides_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('provides/new.html.twig', [
            'provide' => $provide,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provides_show', methods: ['GET'])]
    public function show(Provides $provide): Response
    {
        return $this->render('provides/show.html.twig', [
            'provide' => $provide,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_provides_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Provides $provide, ProvidesRepository $providesRepository): Response
    {
        $form = $this->createForm(ProvidesType::class, $provide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $providesRepository->add($provide, true);

            return $this->redirectToRoute('app_provides_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('provides/edit.html.twig', [
            'provide' => $provide,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provides_delete', methods: ['POST'])]
    public function delete(Request $request, Provides $provide, ProvidesRepository $providesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provide->getId(), $request->request->get('_token'))) {
            $providesRepository->remove($provide, true);
        }

        return $this->redirectToRoute('app_provides_index', [], Response::HTTP_SEE_OTHER);
    }
}
