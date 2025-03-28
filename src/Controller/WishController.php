<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WishController extends AbstractController
{
    #[Route('/wishes', name: 'app_wish_list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    #[Route('/wishes/{id}', name: 'app_wish_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('The wish does not exist');
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish,
        ]);
    }

    #[Route('/wishes/create', name: 'app_wish_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $wish = new Wish();
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wish->setDateCreated(new \DateTimeImmutable());
            $wish->setDateUpdated(new \DateTimeImmutable());

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'Wish created!');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/wishes/{id}/edit', name: 'app_wish_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, WishRepository $wishRepository, EntityManagerInterface $em): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('The wish does not exist');
        }

        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wish->setDateUpdated(new \DateTimeImmutable());

            $em->flush();

            $this->addFlash('success', 'Wish updated!');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/wishes/{id}', name: 'app_wish_delete', methods: ['POST'])]
    public function delete(Wish $wish,Request $request, EntityManagerInterface $em): Response
    {
        if($this->isCsrfTokenValid('delete_wish_'.$wish->getId(), $request->request->get('_token'))){
            $em->remove($wish);
            $this->addFlash('success', 'Wish deleted!');
        }
        else{
            $this->addFlash('danger', 'Invalid CSRF token');
        }
        return $this->redirectToRoute('app_wish_list');
    }




}