<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Constant\PaginationDefault;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/admin/contacts', name: 'app_contact_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ContactRepository $contactRepository): Response
    {
        //todo change this
        $query = $contactRepository->findAll();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', PaginationDefault::PAGE),
            $request->query->getInt('limit', PaginationDefault::LIMIT),
        );

        return $this->render('contact/index.html.twig', [
            'contacts' => $pagination,
        ]);
    }

    #[Route('/', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactDTO = new ContactDTO();
        $form = $this->createForm(ContactType::class, $contactDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $contactDTO->fillEntity(new Contact());
            $entityManager->persist($contact);
            $entityManager->flush();

            //nem kell redirect, hanem üzenet
            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contactDTO,
            'form' => $form,
        ]);
    }

    #[Route('/admin/contact/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/admin/contact/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $contactDTO = ContactDTO::createByEntity($contact);
        $form = $this->createForm(ContactType::class, $contactDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $contactDTO->fillEntity($contact);
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('flash_message.contact_send_success'));

            return $this->redirectToRoute('app_contact_success', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contactDTO,
            'form' => $form,
        ]);
    }

    #[Route('admin/contact/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/success', name: 'app_contact_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('contact/success.html.twig');
    }
}