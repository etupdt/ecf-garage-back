<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Option;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\OptionRepository;

class OptionController extends AbstractController
{

    #[Route('/api/option', name: 'app_post_option', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null) {
            return new JsonResponse(
                ['message' => 'user non habilit !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
            );
        }

        $option = $serializer->deserialize($request->getContent(), Option::class, 'json');
        $em->persist($option);
        $em->flush();

        $returnOption = $serializer->serialize(
            $option,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
            ]
        );

        return new JsonResponse(
            $returnOption, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }
    
    #[Route('/api/option', name: 'app_get_option', methods: ['GET'])]
    public function findAll(
        OptionRepository $optionRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $options = $serializer->serialize(
            $optionRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'options', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $options, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/option/{id}', name: 'app_get_option_id', methods: ['GET'])]
    public function find(
        Request $request, 
        Option $option, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $options = $serializer->serialize(
            $option,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['options', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $options, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/option/{id}', name: 'ap_put_option_id', methods: ['PUT'])]
    public function update(
        Request $request, 
        Option $currentOption, 
        SerializerInterface $serializer, 
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

        $updatedOption = $serializer->deserialize($request->getContent(), 
                Option::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentOption]);

        $em->persist($updatedOption);
        $em->flush();

        $returnOption = $serializer->serialize(
            $updatedOption,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garage'],
            ]
        );

        return new JsonResponse(
            $returnOption, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }

    #[Route('/api/option/{id}', name: 'app_delete_option_id', methods: ['DELETE'])]
    public function delete(
        Request $request, 
        Option $option, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $em->remove($option);
        $em->flush();

        return $this->json([
            'message' => 'option deleted!'
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
