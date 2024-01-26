<?php

namespace App\Service;

class BrandService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PenRepository $penRepository,   
        private BrandRepository $BrandRepository,
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
     * @param Brand $brand
     * @param string $data
     * @return void
     */
    public function updateWithJsonData(Brand $brand, string $data){
        $data = json_decode($data, true);
        $this->update($brand, $data);
    }

    public function createFromArray(array $data){

    }

    public function update(Brand $brand, array $data){

    }

    public function delete(){

    }

    public function index() {
        return $this->brandRepository->findAll();
    }
}