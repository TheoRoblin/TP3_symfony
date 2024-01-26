<?php

namespace App\Controller\API;

use Faker\Factory;
use App\Entity\Type;
use OpenApi\Attributes as OA;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name:'app_api')]
class TypeController extends AbstractController
{
    #[Route('/types', name: 'app_types', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les types.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Type')]
    #[Security(name: 'Bearer')]
    public function index(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();

        return $this->json([
            'types'=>$types,
        ], context:[
            'groups'=> ['pen:read']
        ]);
    }

    #[Route('/type/{id}', name: 'app_type_get', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne un seul type de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Type')]
    public function get(Type $type): JsonResponse{
            return $this->json($type, context:[
                'groups' => ['pen:read'],
            ]);
    }

    #[Route('/types', name: 'app_types_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Creer des styles de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pen:create','type:create']))
        )
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Type::class,
                    groups: ['pen:create'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Type')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $type = new Type();
            $type->setName($data['name']);

            
            $em->persist($type);
            $em->flush();

            return $this->json($type, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Mets a jour les tpe de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['pen:update','type:update']))
        )
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Type::class,
                    groups: ['pen:update'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Type')]
    public function update(
        Type $type,
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $type->setName($data['name']);
    
            $em->persist($type);
            $em->flush();

            return $this->json($type, context:[
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Type')]
    public function delete(Type $type, EntityManagerInterface $em): JsonResponse{
        $em->remove($type);
        $em->flush();
        return $this->json([
            'code'=> 200,
            'message'=> 'Type de stylo supprimé',
        ]);
    }
}
