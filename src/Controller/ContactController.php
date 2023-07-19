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
use App\Entity\Garage;
use App\Repository\GarageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{

    #[Route('/api/contact', name: 'app_post_contact', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        GarageRepository $garageRepository, 
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {

        $content = json_decode($request->getContent());
        $garage = $garageRepository->find($content->garage->id);

        $contact = $serializer->deserialize(
            $request->getContent(), 
            Contact::class, 
            'json',
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garage', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        $contact->setGarage($garage);

        $violations = $validator->validate($contact);

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

        $em->persist($contact);
        $em->flush();

        $contactSerialized = $serializer->serialize(
            $contact,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['services', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $contactSerialized, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );


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
    
    #[Route('/api/contact/garage/{id}', name: 'app_get_contact_garage', methods: ['GET'])]
    public function findByGarage(
        Garage $garage,
        Request $request, 
        ContactRepository $commentRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $filtre = ['garage' => $garage];

        $contacts = $serializer->serialize(
            $commentRepository->findBy($filtre),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'users', 'users', 'cars', 'comments', 'services'],
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
    #[IsGranted('ROLE_USER')]
    public function find(
        Request $request, 
        Contact $contact, 
        SerializerInterface $serializer
    ): JsonResponse
    {

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
    #[IsGranted('ROLE_USER')]
    public function update(
        Request $request, 
        Contact $currentContact, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {

        $updatedContact = $serializer->deserialize($request->getContent(), 
                Contact::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentContact]);
        
        $violations = $validator->validate($updatedContact);

        if (count($violations) > 0) {
            
            $messages =[];
            foreach($violations as $violation) {
                array_push($messages, $violation->getMessage());
            }

            return new JsonResponse(
                ['errors' => $messages], 
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 
                ['Content-Type' => 'application/json;charset=UTF-8']
            );
            
        }

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
    #[IsGranted('ROLE_USER')]
    public function delete(
        Request $request, 
        Contact $contact, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $em->remove($contact);
        $em->flush();

        return $this->json([
            'message' => 'contact deleted!'
            ], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }

}
