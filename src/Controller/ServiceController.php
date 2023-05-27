<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ServiceRepository;

class ServiceController extends AbstractController
{

    #[Route('/api/service', name: 'app_post_service', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {

        $service = $serializer->deserialize($request->getContent(), Service::class, 'json');
        $em->persist($service);
        $em->flush();

        return $this->json([
            'message' => 'service created!'
        ]);

    }
    
    #[Route('/api/service', name: 'app_get_service', methods: ['GET'])]
    public function findAll(ServiceRepository $serviceRepository, SerializerInterface $serializer): JsonResponse
    {

        $services = $serializer->serialize(
            $serviceRepository->findAll(),
            'json'
        );

        return new JsonResponse(
            $services, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/service/{id}', name: 'app_get_service_id', methods: ['GET'])]
    public function find(Service $service, SerializerInterface $serializer): JsonResponse
    {

        $services = $serializer->serialize(
            $service,
            'json'
        );

        return new JsonResponse(
            $services, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/service/{id}', name: 'ap_put_service_id', methods: ['PUT'])]
    public function update(Request $request, 
                            Service $currentService, 
                            SerializerInterface $serializer, 
                            EntityManagerInterface $em
        ): JsonResponse
    {

        $updatedService = $serializer->deserialize($request->getContent(), 
                Service::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentService]);
        
        $em->persist($updatedService);
        $em->flush();

        return $this->json([
            'message' => 'service modified!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }
    
    #[Route('/api/service/{id}', name: 'app_delete_service_id', methods: ['DELETE'])]
    public function delete(Service $service, 
                            EntityManagerInterface $em
        ): JsonResponse
    {

        $em->remove($service);
        $em->flush();

        return $this->json([
            'message' => 'service deleted!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }

}
