<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Constant\PaginationDefault;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_index', methods: [Request::METHOD_GET])]
    public function index(Request $request, PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        $query = $userRepository->createQueryBuilder('a');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', PaginationDefault::PAGE),
            $request->query->getInt('limit', PaginationDefault::LIMIT),
        );

        return $this->render('user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    #[Route('/user/new', name: 'app_user_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userDTO = new UserDTO();
        $form = $this->createForm(UserType::class, $userDTO, [
            'validation_groups' => ['create']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userDTO->fillEntity(new User(), $passwordHasher);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $userDTO,
            'form' => $form,
        ]);
    }


    #[Route('/user/{id}', name: 'app_user_show', methods: [Request::METHOD_GET])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_user_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userDTO = UserDTO::createByEntity($user);

        $form = $this->createForm(UserType::class, $userDTO, [
            'validation_groups' => ['edit']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userDTO->fillEntity($user, $passwordHasher);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $userDTO,
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if($user == $this->getUser() ) {
            //can't delete yourself
            throw new AccessDeniedException($translator->trans('cant_delete_yourself'));
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}