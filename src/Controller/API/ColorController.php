<?php

namespace App\Controller\API;

use Faker\Factory;
use App\Entity\Color;
use OpenApi\Attributes as OA;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name:'app_api')]
class ColorController extends AbstractController
{
    #[Route('/colors', name: 'app_colors', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Lecture de toute les couleurs.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Colors')]
    #[Security(name: 'Bearer')]
    public function index(ColorRepository $colorRepository): JsonResponse
    {
        $colors = $colorRepository->findAll();

        return $this->json([
            'colors'=>$colors,
        ], context:[
            'groups'=> ['pen:read']
        ]);
    }

    #[Route('/color/{id}', name: 'app_color_get', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Lecture de une seule couleur.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Colors')]
    public function get(Color $color): JsonResponse{
            return $this->json($color, context:[
                'groups' => ['pen:read'],
            ]);
    }

    #[Route('/colors', name: 'app_colors_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Créer une couleur.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pen:create', 'color:create']))
        )
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Color::class,
                    groups: ['pen:create'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Colors')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        ColorRepository $colorRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $color = new Color();
            $color->setName($data['name']);

            
            $em->persist($color);
            $em->flush();

            return $this->json($color, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Mets a jour les couleurs.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pen:update','color:update']))
        )
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Color::class,
                    groups: ['pen:update'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Colors')]
    public function update(
        Color $color,
        Request $request,
        EntityManagerInterface $em,
        ColorRepository $colorRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $color->setName($data['name']);
    
            $em->persist($color);
            $em->flush();

            return $this->json($color, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Colors')]
    public function delete(Color $color, EntityManagerInterface $em): JsonResponse{
        $em->remove($color);
        $em->flush();
        return $this->json([
            'code'=> 200,
            'message'=> 'Couleur supprimé',
        ]);
    }
}
