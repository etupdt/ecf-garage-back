<?php

namespace App\Controller; 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use App\Entity\Garage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CommentRepository;
use App\Repository\GarageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{

    #[Route('/api/comment', name: 'app_post_comment', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        GarageRepository $garageRepository, 
        ValidatorInterface $validator
    ): JsonResponse
    {
        
        $content = json_decode($request->getContent());
        $garage = $garageRepository->find($content->garage->id);

        $comment = $serializer->deserialize(
            $request->getContent(), 
            Comment::class, 
            'json',
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garage', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        $comment->setGarage($garage);

        $violations = $validator->validate($comment);

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

        $em->persist($comment);
        $em->flush();

        $commentSerialized = $serializer->serialize(
            $comment,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $commentSerialized, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }

    #[Route('/api/comment', name: 'app_get_comment', methods: ['GET'])]
    public function findAll(
        CommentRepository $commentRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $comments = $serializer->serialize(
            $commentRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'comments', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $comments, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }

    #[Route('/api/comment/garage/{id}', name: 'app_get_comment_garage', methods: ['GET'])]
    public function findByGarage(
        Garage $garage,
        Request $request, 
        CommentRepository $commentRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $filtre = ['garage' => $garage];

        $comments = $serializer->serialize(
            $commentRepository->findBy($filtre),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'users', 'contacts', 'users', 'cars', 'comments', 'services'],
            ]
        );

        return new JsonResponse(
            $comments, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }

    #[Route('/api/comment/{id}', name: 'app_get_comment_id', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function find(
        Request $request, 
        Comment $comment, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $comments = $serializer->serialize(
            $comment,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages'],
            ]
        );

        return new JsonResponse(
            $comments, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }

    #[Route('/api/comment/{id}', name: 'ap_put_comment_id', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(
        Request $request, 
        Comment $currentComment, 
        SerializerInterface $serializer, 
        GarageRepository $garageRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {

        $content = json_decode($request->getContent());

        //        $currentComment->setGarage($garageRepository->find(6));
        
        $updatedComment = $serializer->deserialize(
            $request->getContent(), 
            Comment::class, 
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentComment,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'cars', 'users', 'contacts', 'comments']
            ]
        );
            
        $currentComment->setGarage($garageRepository->find($content->garage->id));

        $em->persist($currentComment);
        $em->flush();

        $updatedComment = $serializer->serialize(
            $currentComment,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'users', 'contacts', 'users', 'cars', 'comments', 'services'],
            ]
        );
        
        return new JsonResponse(
            $updatedComment, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }
     
    #[Route('/api/comment/{id}', name: 'app_delete_comment_id', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(
        Request $request, 
        Comment $comment, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $em->remove($comment);
        $em->flush();

        return $this->json([
            'message' => 'comment deleted!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }

}
