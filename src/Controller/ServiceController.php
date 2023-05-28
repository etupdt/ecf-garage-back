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
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $payload = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $payload->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $service = $serializer->deserialize($request->getContent(), Service::class, 'json');
        $em->persist($service);
        $em->flush();

        return $this->json([
            'message' => 'service created!'
        ]);

    }
    
    #[Route('/api/service', name: 'app_get_service', methods: ['GET'])]
    public function findAll(
        ServiceRepository $serviceRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $services = $serializer->serialize(
            $serviceRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'services', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $services, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/service/{id}', name: 'app_get_service_id', methods: ['GET'])]
    public function find(
        Request $request, 
        Service $service, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $payload = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $payload->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $services = $serializer->serialize(
            $service,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $services, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/service/{id}', name: 'ap_put_service_id', methods: ['PUT'])]
    public function update(
        Request $request, 
        Service $currentService, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $payload = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $payload->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

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
    public function delete(
        Request $request, 
        Service $service, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $payload = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if (!in_array("ROLE_ADMIN", $payload->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $em->remove($service);
        $em->flush();

        return $this->json([
            'message' => 'service deleted!'
            ], 
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
