<?php
namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class RecipeController extends AbstractController
{
    #[Route('/api/recipes', methods: ['GET'])]
    public function index(
        RecipeRepository $repository,
        #[MapQueryString()]
        ?PaginationDTO $paginationDTO = null
    )
    {
        $recipes = $repository->paginateRecipes($paginationDTO?->page);
        return $this->json($recipes, 200, [], [
            'groups' => ['recipes.index']
        ]);
    }

    #[Route('/api/recipes/{id}', requirements: ['id' => Requirement::DIGITS])]
    public function show(Recipe $recipe)
    {
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }

    #[Route('/api/recipes', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function new(
        #[MapRequestPayload(
            serializationContext: [
                'groups'=> ['recipes.api.create']
            ]
        )]
        Recipe $recipe,
        EntityManagerInterface $em
    )
    {
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($recipe);
        $em->flush();
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }
}