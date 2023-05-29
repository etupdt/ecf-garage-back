<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CommentRepository;

class CommentController extends AbstractController
{

    #[Route('/api/comment', name: 'app_post_comment', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $comment = $serializer->deserialize($request->getContent(), Comment::class, 'json');
        $em->persist($comment);
        $em->flush();

        return $this->json([
            'message' => 'comment created!'
        ]);

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
    
    #[Route('/api/comment/{id}', name: 'app_get_comment_id', methods: ['GET'])]
    public function find(
        Request $request, 
        Comment $comment, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

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
    public function update(
        Request $request, 
        Comment $currentComment, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $updatedComment = $serializer->deserialize($request->getContent(), 
                Comment::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentComment]);
        
        $em->persist($updatedComment);
        $em->flush();

        return $this->json([
            'message' => 'comment modified!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }
    
    #[Route('/api/comment/{id}', name: 'app_delete_comment_id', methods: ['DELETE'])]
    public function delete(
        Request $request, 
        Comment $comment, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $em->remove($comment);
        $em->flush();

        return $this->json([
            'message' => 'comment deleted!'
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
