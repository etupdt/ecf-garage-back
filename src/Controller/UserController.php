<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Garage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\GarageRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    #[Route('/api/user', name: 'app_post_user', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        GarageRepository $garageRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        
        $user = $serializer->deserialize(
            $request->getContent(), 
            User::class, 
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages']]
        );

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_ADMIN", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $user->setGarage($garageRepository->findOneBy(['raison' => $user->getGarage()->getRaison()]));

        $user->setPassword($passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));

        $em->persist($user);
        $em->flush();

        return new JsonResponse(
            ['message' => 'user created!'],
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }
    
    #[Route('/api/user', name: 'app_get_user', methods: ['GET'])]
    public function findAll(
        Request $request, 
        UserRepository $userRepository, 
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

        $users = $serializer->serialize(
            $userRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'users', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $users, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/user/garage/{id}', name: 'app_get_user', methods: ['GET'])]
    public function findByGarage(
        Garage $garage,
        Request $request, 
        UserRepository $userRepository, 
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

        $filtre = in_array("ROLE_ADMIN", $bearer->roles) ? ['garage' => $garage] : ['garage' => $garage, 'email' => $bearer->username];

        $users = $serializer->serialize(
            $userRepository->findBy($filtre),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages', 'users', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $users, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/user/{id}', name: 'app_get_user_id', methods: ['GET'])]
    public function find(
        Request $request, 
        User $user, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || !in_array("ROLE_ADMIN", $bearer->roles) && $user->getEmail() != $bearer->username) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        $users = $serializer->serialize(
            $user,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['users', 'contacts', 'users', 'cars', 'comments'],
            ]
        );

        return new JsonResponse(
            $users, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );
    
    }
    
    #[Route('/api/user/{id}', name: 'ap_put_user_id', methods: ['PUT'])]
    public function update(
        Request $request, 
        User $currentUser, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        GarageRepository $garageRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {

        $email = $currentUser->getEmail();

        $updatedUser = $serializer->deserialize(
            $request->getContent(), 
            User::class, 
            'json', 
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garages']
            ]
        );
        
        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == null || (!in_array("ROLE_ADMIN", $bearer->roles) && $updatedUser->getEmail() != $bearer->username)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
                true
            );
        }

        if (!in_array("ROLE_ADMIN", $bearer->roles)) {
            $updatedUser->setEmail($email);
        }

        $updatedUser->setGarage($garageRepository->findOneBy(['raison' => $updatedUser->getGarage()->getRaison()]));

        $updatedUser->setPassword($passwordHasher->hashPassword(
            $updatedUser,
            $updatedUser->getPassword()
        ));

        $em->persist($updatedUser);
        $em->flush();

        return $this->json(
            ['message' => 'user modified!'], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );

    }
    
    #[Route('/api/user/{id}', name: 'app_delete_user_id', methods: ['DELETE'])]
    public function delete(
        Request $request,
        User $user, 
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

        $em->remove($user);
        $em->flush();

        return $this->json([
            'message' => 'user deleted!'
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
