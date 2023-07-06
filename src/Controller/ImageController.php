<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ImageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ImageController extends AbstractController
{

    #[Route('/api/image', name: 'app_post_image', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        ImageRepository $imageRepository, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $imageFile = $request->files->get('garage_image');

        $image = new Image();
        $image->setFilename($imageFile->getClientOriginalName());

        $em->persist($image);
        $em->flush();
        
        $imageFile->move(
            $this->getParameter('kernel.project_dir')."/public/images",
            $image->getId().'_'.$imageFile->getClientOriginalName()
        );

        return $this->json(
            $image, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8']
        );

    }
    
    #[Route('/api/image', name: 'app_get_image', methods: ['GET'])]
    public function findAll(
        ImageRepository $imageRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $images = $serializer->serialize(
            $imageRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages'],
            ]
        );

        return new JsonResponse(
            $images, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/image/{id}', name: 'app_get_image_id', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function find(
        Request $request, 
        Image $image, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $imageSerialized = $serializer->serialize(
            $image,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages'],
            ]
        );

        return new JsonResponse(
            $imageSerialized, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/image/{id}', name: 'ap_put_image_id', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(
        Request $request, 
        Image $currentImage, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $updatedImage = $serializer->deserialize($request->getContent(), 
                Image::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentImage]);
        
        $em->persist($updatedImage);
        $em->flush();

        return $this->json([
            'message' => 'image modified!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }
    
    #[Route('/api/image/{id}', name: 'app_delete_image_id', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request, 
        Image $image, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $em->remove($image);
        $em->flush();

        return $this->json([
            'message' => 'image deleted!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }

}
