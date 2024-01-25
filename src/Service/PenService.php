<?php

namespace App\Service;


use Faker\Factory;
use App\Entity\Pen;
use App\Repository\PenRepository;
use App\Repository\MaterialRepository;
use App\Repository\TypeRepository;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;

class PenService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PenRepository $penRepository,   
        private MaterialRepository $materialRepository,
        private TypeRepository $typeRepository,
        private BrandRepository $BrandRepository,
        private ColorRepository $colorRepository,
    ){}

    /**
     * @param string $data
     * @return void
     */
    public function createFromJsonString(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        return $this->createFromArray($data);
    }

    /**
     * @param Pen $pen
     * @param string $data
     * @return void
     */
    public function updateWithJsonData(Pen $pen, string $data){
        $data = json_decode($data, true);
        $this->update($pen, $data);
    }


    public function createFromArray(array $data): Pen{
        $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $pen = new Pen();
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);
            $pen->setRef($faker->unique()->ean13);

            if(!empty($data['type'])) {
                $type= $this->typeRepository->find($data['type']);
                if(!$type) {
                    throw new \Exception('Type non valide');
                }
                $pen->setType($type);
            }

            if(!empty($data['material'])) {
                $material= $this->materialRepository->find($data['material']);
                if(!$material) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setMaterial($material);
            }

            if(!empty($data['brand'])) {
                $brand= $this->BrandRepository->find($data['brand']);
                if(!$brand) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setBrand($brand);
            }

            if (!empty($data['color'])) {
                foreach($data['color'] as $colorId) {
                    $color = $this->colorRepository->find($colorId);
                    if (!$color)
                        throw new \Exception('Couleur(s) non valide');
                    $pen->addColor($color);
                }
            }

            $this->em->persist($pen);
            $this->em->flush();

            return $pen;
    }

    public function update(Pen $pen, array $data){
            if(!empty($data['name']))
            $pen->setName($data['name']);

            if(!empty($data['price']))
                $pen->setPrice($data['price']);

            if(!empty($data['description']))
                $pen->setDescription($data['description']);

            if(!empty($data['type'])) {
                $type= $this->typeRepository->find($data['type']);
                if(!$type) {
                    throw new \Exception('Type non valide');
                }
                $pen->setType($type);
            }

            if(!empty($data['material'])) {
                $material= $this->materialRepository->find($data['material']);
                if(!$material) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setMaterial($material);
            }

            if(!empty($data['brand'])) {
                $brand= $this->BrandRepository->find($data['brand']);
                if(!$brand) {
                    throw new \Exception('Materiel non valide');
                }
                $pen->setBrand($brand);
            }

            if (!empty($data['color'])) {
                $pen->resetColors();
                foreach($data['color'] as $colorId) {
                    $color = $this->colorRepository->find($colorId);
                    if (!$color)
                        throw new \Exception('Couleur(s) non valide');
                    $pen->addColor($color);
                }
            }

            $this->em->persist($pen);
            $this->em->flush();
    }

    public function delete(Pen $pen) {
        $this->em->remove($pen);
        $this->em->flush();
    }

    public function index() {
        return $this->penRepository->findAll();
    }
}