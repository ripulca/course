<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Doctor;
use App\Form\DoctorType;
use App\Repository\DoctorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/doctor')]
class DoctorController extends AbstractController
{
    #[Route('/', name: 'app_doctor_index', methods: ['GET'])]
    public function index(Request $request, ManagerRegistry $doctrine, DoctorRepository $doctorRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        return $this->render('doctor/index.html.twig', [
            'user'=>$user,
            'doctors' => $doctorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_doctor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DoctorRepository $doctorRepository): Response
    {
        $doctor = new Doctor();
        $form = $this->createForm(DoctorType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctorRepository->add($doctor, true);

            return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('doctor/new.html.twig', [
            'doctor' => $doctor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_doctor_show', methods: ['GET'])]
    public function show(Request $request, ManagerRegistry $doctrine, Doctor $doctor): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        $user_doc=$doctor->getUserProfile();
        return $this->render('doctor/show.html.twig', [
            'user'=>$user,
            'doctor' => $doctor,
            'user_doc'=>$user_doc,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_doctor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ManagerRegistry $doctrine, Doctor $doctor, DoctorRepository $doctorRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        $form = $this->createForm(DoctorType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctorRepository->add($doctor, true);

            return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('doctor/edit.html.twig', [
            'user'=>$user,
            'doctor' => $doctor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_doctor_delete', methods: ['POST'])]
    public function delete(Request $request, Doctor $doctor, DoctorRepository $doctorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$doctor->getId(), $request->request->get('_token'))) {
            $doctorRepository->remove($doctor, true);
        }

        return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
    }
}
