<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\Pen;
use OpenApi\Attributes as OA;
use App\Repository\PenRepository;
use App\Repository\TypeRepository;
use App\Repository\MaterialRepository;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
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
    public function index(PenRepository $penRepository): JsonResponse
    {

        $pens = $penRepository->findAll();

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
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        MaterialRepository $materiaRepository,
        BrandRepository $BrandRepository,
        ColorRepository $colorRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $pen = new Pen();
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);
            $pen->setRef($faker->unique()->ean13);

            if(!empty($data['type'])) {
                $type= $typeRepository->find($data['type']);
                if(!$type) {
                    throw new \Exception('Type non valide');
                }
                $pen->setType($type);
            }

            if(!empty($data['material'])) {
                $material= $materiaRepository->find($data['material']);
                if(!$material) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setMaterial($material);
            }

            if(!empty($data['brand'])) {
                $brand= $BrandRepository->find($data['brand']);
                if(!$brand) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setBrand($brand);
            }

            if (!empty($data['color'])) {
                foreach($data['color'] as $colorId) {
                    $color = $colorRepository->find($colorId);
                    if (!$color)
                        throw new \Exception('Couleur(s) non valide');
                    $pen->addColor($color);
                }
            }

            $em->persist($pen);
            $em->flush();

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
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        MaterialRepository $materiaRepository,
        BrandRepository $brandRepository,
        ColorRepository $colorRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);

            if(!empty($data['type'])) {
                $type= $typeRepository->find($data['type']);
                if(!$type) {
                    throw new \Exception('Type non valide');
                }
                $pen->setType($type);
            }

            if(!empty($data['material'])) {
                $material= $materiaRepository->find($data['material']);
                if(!$material) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setMaterial($material);
            }

            if(!empty($data['brand'])) {
                $brand= $brandRepository->find($data['brand']);
                if(!$brand) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setBrand($brand);
            }

            if (!empty($data['color'])) {
                $pen->resetColors();
                foreach($data['color'] as $colorId) {
                    $color = $colorRepository->find($colorId);
                    if (!$color)
                        throw new \Exception('Couleur(s) non valide');
                    $pen->addColor($color);
                }
            }

            $em->persist($pen);
            $em->flush();

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
    public function delete(Pen $pen, EntityManagerInterface $em): JsonResponse{
        $em->remove($pen);
        $em->flush();
        return $this->json([
            'code'=> 200,
            'message'=> 'Stylo supprimé',
        ]);
    }
}
