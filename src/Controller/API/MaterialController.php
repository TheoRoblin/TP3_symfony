<?php

namespace App\Controller\API;

use Faker\Factory;
use App\Entity\Material;
use OpenApi\Attributes as OA;
use App\Repository\MaterialRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name:'app_api')]
class MaterialController extends AbstractController
{
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Liste tout les materiels.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pen:read']))
        )
    )]

    #[OA\Tag(name: 'Material')]
    #[Security(name: 'Bearer')]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        $materials = $materialRepository->findAll();

        return $this->json([
            'materials'=>$materials,
        ], context:[
            'groups'=> ['pen:read']
        ]);
    }

    #[Route('/material/{id}', name: 'app_material_get', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Liste un seul materiel.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Material')]
    public function get(Material $material): JsonResponse{
            return $this->json($material, context:[
                'groups' => ['pen:read'],
            ]);
    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Creer un materiel.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pen:create','material:create']))
        )
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class,
                    groups: ['pen:create'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Material')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        MaterialRepository $materialRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $material = new Material();
            $material->setName($data['name']);

            
            $em->persist($material);
            $em->flush();

            return $this->json($material, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Mets a jour les materiels.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pen:update','material:update']))
        )
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Material::class,
                    groups: ['pen:update'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Material')]
    public function update(
        Material $material,
        Request $request,
        EntityManagerInterface $em,
        MaterialRepository $materialRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $material->setName($data['name']);
    
            $em->persist($material);
            $em->flush();

            return $this->json($material, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Material')]
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse{
        $em->remove($material);
        $em->flush();
        return $this->json([
            'code'=> 200,
            'message'=> 'Materiel supprimé',
        ]);
    }
}
