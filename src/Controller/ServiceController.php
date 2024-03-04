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
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ServiceController extends AbstractController
{

    #[Route('/api/service/{id}', name: 'app_post_service', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(
        Request $request, 
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        ValidatorInterface $validator
    ): JsonResponse
    {

        $imageFile = $request->files->get('garage_image');

        $image = new Image();
        $safeFileName = $slugger->slug($imageFile->getClientOriginalName());
        $newFilename = $safeFileName.'-'.uniqid().'.'.$imageFile->guessExtension();
        $image->setFilename($newFilename);
        $image->setHash($request->get('image_hash'));

        $service = new Service();
        $service->setName($request->get('name'));
        $service->setDescription($request->get('description'));
        $service->setImage($image);

        $violations = $validator->validate($service);

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

        $em->persist($service);
        $em->flush();

        $imageFile->move(
            $this->getParameter('kernel.project_dir')."/public/images",
            $newFilename
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
    #[IsGranted('ROLE_ADMIN')]
    public function find(
        Request $request, 
        Service $service, 
        SerializerInterface $serializer
    ): JsonResponse
    {

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
    
    #[Route('/api/service', name: 'app_put_service_id', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(
        Request $request, 
        ServiceRepository $serviceRepository,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        ValidatorInterface $validator
    ): JsonResponse
    {

        $imageFile = $request->files->get('garage_image');

        $currentService = $serviceRepository->find(intval($request->get('id')));
        
        if ($imageFile && $currentService->getImage()->getHash() !== $request->get('image_hash')) {
            
            $filesystem = new Filesystem();
            $filesystem->remove(
                $this->getParameter('kernel.project_dir')."/public/images/".$currentService->getImage()->getFilename()
            );

            $image = new Image();
            $safeFileName = $slugger->slug($imageFile->getClientOriginalName());
            $newFilename = $safeFileName.'-'.uniqid().'.'.$imageFile->guessExtension();
            $image->setFilename($newFilename);
            $image->setHash($request->get('image_hash'));
            $currentService->setImage($image);
            
            $imageFile->move(
                $this->getParameter('kernel.project_dir')."/public/images",
                $newFilename
            );    

        }

        $currentService->setName($request->get('name'));
        $currentService->setDescription($request->get('description'));

        $violations = $validator->validate($currentService);

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
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request, 
        Service $service, 
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
