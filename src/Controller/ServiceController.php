<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Service;
use App\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ServiceRepository;
use Symfony\Component\Filesystem\Filesystem;

class ServiceController extends AbstractController
{

    #[Route('/api/service/{id}', name: 'app_post_service', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $imageFile = $request->files->get('garage_image');

        $image = new Image();
        $image->setFilename($imageFile->getClientOriginalName());
        $image->setHash($request->get('image_hash'));

        $service = new Service();
        $service->setName($request->get('name'));
        $service->setDescription($request->get('description'));
        $service->setImage($image);

        $em->persist($service);
        $em->flush();

        $imageFile->move(
            $this->getParameter('kernel.project_dir')."/public/images",
            $service->getImage()->getId().'_'.$imageFile->getClientOriginalName()
        );
        
        return $this->json([
            $service
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
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
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages'],
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

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_ADMIN", $bearer->roles)) {
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
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages'],
            ]
        );

        return new JsonResponse(
            $services, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/service', name: 'app_put_service_id', methods: ['POST'])]
    public function update(
        Request $request, 
        ServiceRepository $serviceRepository,
        EntityManagerInterface $em,
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }
    
        $imageFile = $request->files->get('garage_image');

        $currentService = $serviceRepository->find(intval($request->get('id')));
        
        if ($imageFile && $currentService->getImage()->getHash() !== $request->get('image_hash')) {

            $image = new Image();
            $image->setFilename($imageFile->getClientOriginalName());
            $image->setHash($request->get('image_hash'));
            $currentService->setImage($image);
            
            $imageFile->move(
                $this->getParameter('kernel.project_dir')."/public/images",
                $currentService->getImage()->getId().'_'.$imageFile->getClientOriginalName()
            );

            $filesystem = new Filesystem();
            $filesystem->remove(
                $currentService->getImage()->getId().'_'.$currentService->getImage()->getFilename()
            );

        }

        $currentService->setName($request->get('name'));
        $currentService->setDescription($request->get('description'));

        $em->persist($currentService);
        $em->flush();
        
        return $this->json([
            $currentService
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

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_ADMIN", $bearer->roles)) {
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
