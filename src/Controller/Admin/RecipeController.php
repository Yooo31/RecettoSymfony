<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/recipe', name: 'admin.recipe.')]
#[IsGranted('ROLE_VERIFIED')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $repository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 2;
        $order = 'DESC';
        $recipes = $repository->paginateRecipes($page, $limit, $order);
        $maxPages = ceil($recipes->count() / 2);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }

    #[Route('/{id}', name:'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le recette a bien été modifiée');

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
    public function delete(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');

        return $this->redirectToRoute('admin.recipe.index');
    }
}
