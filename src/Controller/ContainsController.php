<?php

namespace App\Controller;

use App\Entity\Contains;
use App\Form\ContainsType;
use App\Repository\ContainsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contains')]
class ContainsController extends AbstractController
{
    #[Route('/', name: 'app_contains_index', methods: ['GET'])]
    public function index(ContainsRepository $containsRepository): Response
    {
        return $this->render('contains/index.html.twig', [
            'contains' => $containsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_contains_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContainsRepository $containsRepository): Response
    {
        $contain = new Contains();
        $form = $this->createForm(ContainsType::class, $contain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $containsRepository->add($contain, true);

            return $this->redirectToRoute('app_contains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contains/new.html.twig', [
            'contain' => $contain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contains_show', methods: ['GET'])]
    public function show(Contains $contain): Response
    {
        return $this->render('contains/show.html.twig', [
            'contain' => $contain,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contains_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contains $contain, ContainsRepository $containsRepository): Response
    {
        $form = $this->createForm(ContainsType::class, $contain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $containsRepository->add($contain, true);

            return $this->redirectToRoute('app_contains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contains/edit.html.twig', [
            'contain' => $contain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contains_delete', methods: ['POST'])]
    public function delete(Request $request, Contains $contain, ContainsRepository $containsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contain->getId(), $request->request->get('_token'))) {
            $containsRepository->remove($contain, true);
        }

        return $this->redirectToRoute('app_contains_index', [], Response::HTTP_SEE_OTHER);
    }
}
