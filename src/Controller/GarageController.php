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
use App\Repository\ServiceRepository;

class GarageController extends AbstractController
{

    #[Route('/api/garage', name: 'app_post_garage', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        ServiceRepository $serviceRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $newGarage = $serializer->deserialize($request->getContent(), 
            Garage::class, 
            'json'
        );

        foreach ($newGarage->getServices() as $service) {
            $serviceInitial = $serviceRepository->find($service->getId());
            $newGarage->removeService($service);
            if ($serviceInitial) {
                $newGarage->addService($serviceInitial);
            }
        }
        
        $em->persist($newGarage);
        $em->flush();

        return $this->json([
            'message' => 'garage created!'
        ]);

    }
    
    #[Route('/api/garage', name: 'app_get_garage', methods: ['GET'])]
    public function findAll(
        Request $request, 
        GarageRepository $garageRepository, 
        SerializerInterface $serializer): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

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
    public function find(
        Request $request, 
        Garage $garage, 
        SerializerInterface $serializer
    ): JsonResponse
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
    public function update(
        Request $request, 
        Garage $currentGarage, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ServiceRepository $serviceRepository
    ): JsonResponse
    {
        
        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $newGarage = $serializer->deserialize($request->getContent(), 
            Garage::class, 
            'json'
        );

        $indexes = [];

        foreach ($newGarage->getServices() as $service) {
            $serviceInitial = $serviceRepository->find($service->getId());
            $newGarage->removeService($service);
            if ($serviceInitial) {
                $newGarage->addService($serviceInitial);
            } else {
                array_push($indexes, $service->getId());
            }
        }
        
        foreach ($currentGarage->getServices() as $service) {
            if (!array_key_exists($service->getId(), $indexes)) {
                $currentGarage->removeService($service);
            }
        }
        
        $updatedGarage = $serializer->deserialize($request->getContent(), 
            Garage::class, 
            'json', 
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentGarage,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services']
            ]
        );
        
        foreach($newGarage->getServices() as $service) {
            $updatedGarage->addService($service);
        }
    
        $em->persist($updatedGarage);
        $em->flush();

        return $this->json(['message' => 'garage modified!'], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
  
    }
    
    #[Route('/api/garage/{id}', name: 'app_delete_garage_id', methods: ['DELETE'])]
    public function delete(
        Request $request, 
        Garage $garage, 
        EntityManagerInterface $em
        ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $em->remove($garage);
        $em->flush();

        return $this->json(['message' => 'garage deleted!'], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
  
    }
    
    public function jwtDecodePayload (string $jwt) 
    {
        $tokenPayload = base64_decode(explode('.', str_replace(
            "Bearer ",
            "",
            $jwt
        ))[1]);
        return json_decode($tokenPayload);        
    }

}
