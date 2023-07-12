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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OptionController extends AbstractController
{

    #[Route('/api/option', name: 'app_post_option', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {

        $option = $serializer->deserialize($request->getContent(), Option::class, 'json');

        $violations = $validator->validate($option);

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
    #[IsGranted('ROLE_USER')]
    public function find(
        Request $request, 
        Option $option, 
        SerializerInterface $serializer
    ): JsonResponse
    {

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
    #[IsGranted('ROLE_ADMIN')]
    public function update(
        Request $request, 
        Option $currentOption, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ValidatorInterface $validator

    ): JsonResponse
    {

        $updatedOption = $serializer->deserialize($request->getContent(), 
            Option::class, 
            'json', 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentOption]
        );

        $violations = $validator->validate($updatedOption);

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
    #[IsGranted('ROLE_USER')]
    public function delete(
        Request $request, 
        Option $option, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $em->remove($option);
        $em->flush();

        return $this->json([
            'message' => 'option deleted!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );

    }

}
