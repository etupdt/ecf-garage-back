<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Garage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GarageRepository;

class GarageController extends AbstractController
{

    #[Route('/api/garage', name: 'app_post_garage', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {

        $garage = $serializer->deserialize($request->getContent(), Garage::class, 'json');
        $em->persist($garage);
        $em->flush();

        return $this->json([
            'message' => 'garage created!'
        ]);

    }
    
    #[Route('/api/garage', name: 'app_get_garage', methods: ['GET'])]
    public function findAll(GarageRepository $garageRepository, SerializerInterface $serializer): JsonResponse
    {

        $garages = $serializer->serialize(
            $garageRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'contacts', 'users', 'cars', 'comments'],
            ]);

        return new JsonResponse(
            $garages, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
  
    }
    
    #[Route('/api/garage/{id}', name: 'app_get_garage_id', methods: ['GET'])]
    public function find(Garage $garage, SerializerInterface $serializer): JsonResponse
    {

        $garages = $serializer->serialize(
            $garage,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $garages, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
  
    }
    
    #[Route('/api/garage/{id}', name: 'app_put_garage_id', methods: ['PUT'])]
    public function update(Request $request, 
                            Garage $currentGarage, 
                            SerializerInterface $serializer, 
                            EntityManagerInterface $em
        ): JsonResponse
    {

        $updatedGarage = $serializer->deserialize($request->getContent(), 
                Garage::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentGarage]);
        
        $em->persist($updatedGarage);
        $em->flush();

        return $this->json([
            'message' => 'garage modified!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
  
    }
    
    #[Route('/api/garage/{id}', name: 'app_delete_garage_id', methods: ['DELETE'])]
    public function delete(Garage $garage, 
                            EntityManagerInterface $em
        ): JsonResponse
    {

        $em->remove($garage);
        $em->flush();

        return $this->json([
            'message' => 'garage deleted!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
  
    }
    
}
