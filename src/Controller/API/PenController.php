<?php

namespace App\Controller\API;

use Faker\Factory;
use App\Entity\Pen;
use App\Service\PenService;
use OpenApi\Attributes as OA;
use App\Repository\PenRepository;
use App\Repository\TypeRepository;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name:'app_api')]
class PenController extends AbstractController
{
    
    #[Route('/pens', name: 'app_pens', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les stylos.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Stylos')]
    #[Security(name: 'Bearer')]
    public function index(PenService $penService,): JsonResponse
    {
        $pens = $penService->index();
        return $this->json([
            'pens'=>$pens,
        ], context:[
            'groups'=> ['pen:read']
        ]);
    }

    
    #[Route('/pen/{id}', name: 'app_pen_get', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les stylos.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Stylos')]
    public function get(Pen $pen): JsonResponse{
            return $this->json($pen, context:[
                'groups' => ['pen:read'],
            ]);
    }

    #[Route('/pens', name: 'app_pen_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Créer un stylo',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pen:create']))
        )
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Pen::class,
                    groups: ['pen:create']
                )
            )
        )
    )]
    #[OA\Tag(name: 'Stylos')]
    public function add(
        Request $request,
        PenService $penService,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif

            $pen = $penService->createFromJsonString($request->getContent());

            return $this->json($pen, context:[
                'groups' => ['pen:create'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/pen/{id}', name: 'app_pen_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Mets a jour les stylos.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pen:update']))
        )
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Pen::class,
                    groups: ['pen:update'],
                )
            )
        )
    )]
    #[OA\Tag(name: 'Stylos')]
    public function update(
        Pen $pen,
        Request $request,
        PenService $penService,
    ): JsonResponse {
        try {
            $penService->updateWithJsonData($pen, $request->getContent());

            return $this->json($pen, context:[
                'groups' => ['pen:update'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/pen/{id}', name: 'app_pen_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Stylos')]
    public function delete(Pen $pen, PenService $penService,): JsonResponse{
        $penService ->delete($pen, $request->getContent());
        return $this->json([
            'code'=> 200,
            'message'=> 'Stylo supprimé',
        ]);
    }
}
