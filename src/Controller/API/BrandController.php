<?php

namespace App\Controller\API;

use Faker\Factory;
use App\Entity\Brand;
use OpenApi\Attributes as OA;
use App\Repository\BrandRepository;
use App\Repository\TypeRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name:'app_api')]
class BrandController extends AbstractController
{
    #[Route('/brands', name: 'app_brands', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Listes toutes les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Brand')]
    #[Security(name: 'Bearer')]
    public function index(BrandRepository $brandRepository): JsonResponse
    {

        $brands = $brandRepository->findAll();

        return $this->json([
            'brands'=>$brands,
        ], context:[
            'groups'=> ['pen:read']
        ]);
    }

    #[OA\Response(
        response: 200,
        description: 'Liste une marque.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Brand')]
    #[Route('/brand/{id}', name: 'app_brand_get', methods:['GET'])]
    public function get(Brand $brand): JsonResponse{
            return $this->json($brand, context:[
                'groups' => ['pen:read'],
            ]);
    }

    #[Route('/brands', name: 'app_brand_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Créer une marque.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pen:create', 'brand:create']))
        )
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class,
                    groups: ['pen:create'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Brand')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        BrandRepository $brandRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $brand = new Brand();
            $brand->setName($data['name']);

            
            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Mets a jour les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['pen:update', 'brand:update']))
        )
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Brand::class,
                    groups: ['pen:update'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Brand')]
    public function update(
        Brand $brand,
        Request $request,
        EntityManagerInterface $em,
        BrandRepository $brandRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $brand->setName($data['name']);
    
            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Brand')]
    public function delete(Brand $brand, EntityManagerInterface $em): JsonResponse{
        $em->remove($brand);
        $em->flush();
        return $this->json([
            'code'=> 200,
            'message'=> 'Marque supprimé',
        ]);
    }
}
