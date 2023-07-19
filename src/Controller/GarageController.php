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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GarageController extends AbstractController
{

    #[Route('/api/garage', name: 'app_post_garage', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        ServiceRepository $serviceRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {

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
        
        $violations = $validator->validate($newGarage);

        if (count($violations) > 0) {

            $messages = [];
            foreach($violations as $violation) {
                array_push($messages, $violation->getMessage());
            }

            return new JsonResponse(
                ['errors' => $messages], 
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 
                ['Content-Type' => 'application/json;charset=UTF-8']
            );
            
        }

        $em->persist($newGarage);
        $em->flush();

        return $this->json([
            'message' => 'garage created!'
        ]);

    }
    
    #[Route('/api/garage/raison', name: 'app_post_garage_raison', methods: ['POST'])]
    public function findByRaison(
        Request $request, 
        SerializerInterface $serializer, 
        GarageRepository $garageRepository,
    ): JsonResponse
    {

        $garage = $garageRepository->findOneBy(['raison' => $request->toArray()['raison']]);
        
        $garageSerialized = $serializer->serialize(
            $garage,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['contacts', 'users', 'cars', 'comments', 'service'],
            ]
        );

        return new JsonResponse(
            $garageSerialized, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
  
    }
    
    #[Route('/api/garage', name: 'app_get_garage', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function findAll(
        Request $request, 
        GarageRepository $garageRepository, 
        SerializerInterface $serializer): JsonResponse
    {

        $garagesSerialized = $serializer->serialize(
            $garageRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'contacts', 'users', 'cars', 'comments'],
            ]);

        return new JsonResponse(
            $garagesSerialized, 
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

        error_log("=================================>".sizeof($garage->getServices()));

        $garageSerialized = $serializer->serialize(
            $garage,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['contacts', 'users', 'cars', 'comments'],
            ]
        );
        
        return new JsonResponse(
            $garageSerialized, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
  
    }
    
    #[Route('/api/garage/{id}', name: 'app_put_garage_id', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(
        Request $request, 
        Garage $currentGarage, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ServiceRepository $serviceRepository,
        ValidatorInterface $validator
    ): JsonResponse
    {
        
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
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'cars', 'comments']
            ]
        );
        
        foreach($newGarage->getServices() as $service) {
            $currentGarage->addService($service);
        }
    
        $violations = $validator->validate($currentGarage);

        if (count($violations) > 0) {

            $messages = [];
            foreach($violations as $violation) {
                array_push($messages, $violation->getMessage());
            }

            return new JsonResponse(
                ['errors' => $messages], 
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 
                ['Content-Type' => 'application/json;charset=UTF-8']
            );
            
        }

        $em->persist($currentGarage);
        $em->flush();

        $updatedGarage = $serializer->serialize(
            $currentGarage,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'users', 'contacts', 'cars', 'comments'],
            ]
        );
        
        return new JsonResponse(
            $updatedGarage, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }
    
    #[Route('/api/garage/{id}', name: 'app_delete_garage_id', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request, 
        Garage $garage, 
        EntityManagerInterface $em
        ): JsonResponse
        {

        $em->remove($garage);
        $em->flush();

        return $this->json(['message' => 'garage deleted!'], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
  
    }
    
}
