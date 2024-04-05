<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Message\RecipePDFMessage;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/admin/recipe', name: 'admin.recipe.')]
#[IsGranted('ROLE_VERIFIED')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted(RecipeVoter::LIST)]
    public function index(RecipeRepository $repository, Request $request, Security $security): Response
    {
        $page = $request->query->getInt('page', 1);
        $userId = $security->getUser()->getId();
        $canListAll = $security->isGranted(RecipeVoter::LIST_ALL);
        $recipes = $repository->paginateRecipes($page, $canListAll ? null : $userId);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'page' => $page
        ]);
    }

    #[Route('/{slug}', name:'show')]
    #[IsGranted(RecipeVoter::LIST)]
    public function show(RecipeRepository $reposirory, Request $request): Response
    {
        $recipe = $reposirory->findOneBy(['slug' => $request->attributes->get('slug')]);

        return $this->render('admin/recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/edit/{id}', name:'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    #[IsGranted('RECIPE_EDIT', subject: 'recipe')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, MessageBusInterface $messageBus): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');

            $messageBus->dispatch(new RecipePDFMessage($recipe->getId()));

            return $this->redirectToRoute('admin.recipe.index', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    #[Route('/new', name:'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');

            return $this->redirectToRoute('admin.recipe.index', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('admin/recipe/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name:'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[IsGranted('RECIPE_EDIT', subject: 'recipe')]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        $recipeId = $recipe->getId();
        $em->remove($recipe);
        $em->flush();
        if ($request->getPreferredFormat() === TurboBundle::STREAM_FORMAT) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('admin/recipe/delete.html.twig', ['recipeId' => $recipeId]);
        }
        $this->addFlash('success', 'La recette a bien été supprimée');

        return $this->redirectToRoute('admin.recipe.index');
    }
}
