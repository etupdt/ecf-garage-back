<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ContactRepository;

class ContactController extends AbstractController
{

    #[Route('/api/contact', name: 'app_post_contact', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $contact = $serializer->deserialize($request->getContent(), Contact::class, 'json');
        $em->persist($contact);
        $em->flush();

        return $this->json([
            'message' => 'contact created!'
        ]);

    }
    
    #[Route('/api/contact', name: 'app_get_contact', methods: ['GET'])]
    public function findAll(
        ContactRepository $contactRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $contacts = $serializer->serialize(
            $contactRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'contacts', 'contacts', 'users', 'cars', 'contacts'],
            ]
        );

        return new JsonResponse(
            $contacts, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/contact/{id}', name: 'app_get_contact_id', methods: ['GET'])]
    public function find(
        Request $request, 
        Contact $contact, 
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

        $contacts = $serializer->serialize(
            $contact,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages'],
            ]
        );

        return new JsonResponse(
            $contacts, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/contact/{id}', name: 'ap_put_contact_id', methods: ['PUT'])]
    public function update(
        Request $request, 
        Contact $currentContact, 
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

        $updatedContact = $serializer->deserialize($request->getContent(), 
                Contact::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentContact]);
        
        $em->persist($updatedContact);
        $em->flush();

        return $this->json([
            'message' => 'contact modified!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }
    
    #[Route('/api/contact/{id}', name: 'app_delete_contact_id', methods: ['DELETE'])]
    public function delete(
        Request $request, 
        Contact $contact, 
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

        $em->remove($contact);
        $em->flush();

        return $this->json([
            'message' => 'contact deleted!'
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
