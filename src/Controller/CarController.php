<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Car;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CarRepository;
use App\Repository\OptionRepository;
use App\Repository\GarageRepository;

class CarController extends AbstractController
{

    #[Route('/api/car', name: 'app_post_car', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        GarageRepository $garageRepository,
        OptionRepository $optionRepository
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == "" || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
            );
        }

        $newCar = $serializer->deserialize($request->getContent(), 
            Car::class, 
            'json'
        );

        foreach ($newCar->getOptions() as $option) {
            $optionInitial = $optionRepository->find($option->getId());
            $newCar->removeOption($option);
            if ($optionInitial) {
                $newCar->addOption($optionInitial);
            }
        }

        $newCar->setGarage($garageRepository->findOneBy(['raison' => $newCar->getGarage()->getRaison()]));

        $em->persist($newCar);
        $em->flush();

        return $this->json([
            'message' => 'car created!'
        ]);

    }

    #[Route('/api/car', name: 'app_get_car', methods: ['GET'])]
    public function findAll(
        CarRepository $carRepository, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $cars = $serializer->serialize(
            $carRepository->findAll(),
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garage', 'car'],
            ]
        );

        return new JsonResponse(
            $cars, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }

    #[Route('/api/car/{id}', name: 'app_get_car_id', methods: ['GET'])]
    public function find(
        Request $request, 
        Car $car, 
        SerializerInterface $serializer
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == "" || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
            );
        }

        $cars = $serializer->serialize(
            $car,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garage', 'car'],
            ]
        );

        return new JsonResponse(
            $cars, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }
    
    #[Route('/api/car/{id}', name: 'ap_put_car_id', methods: ['PUT'])]
    public function update(
        Request $request, 
        Car $currentCar, 
        SerializerInterface $serializer, 
        OptionRepository $optionRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == "" || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilit !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
            );
        }

        $newCar = $serializer->deserialize($request->getContent(), 
            Car::class, 
            'json'
        );

        $indexes = [];

        foreach ($newCar->getOptions() as $option) {
            $optionInitial = $optionRepository->find($option->getId());
            $newCar->removeOption($option);
            if ($optionInitial) {
                $newCar->addOption($optionInitial);
            } else {
                array_push($indexes, $option->getId());
            }
        }

        foreach ($currentCar->getOptions() as $option) {
            if (!array_key_exists($option->getId(), $indexes)) {
                $currentCar->removeOption($option);
            }
        }

        $updatedCar = $serializer->deserialize($request->getContent(), 
            Car::class, 
            'json', 
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentCar,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['options', 'garage']
            ]
        );

        foreach($newCar->getOptions() as $option) {
            $updatedCar->addOption($option);
        }

        $em->persist($updatedCar);
        $em->flush();

        return $this->json(['message' => 'car modified!'], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );
    
    }
    
    #[Route('/api/car/{id}', name: 'app_delete_car_id', methods: ['DELETE'])]
    public function delete(
        Request $request, 
        Car $car, 
        EntityManagerInterface $em
    ): JsonResponse
    {

        $bearer = $this->jwtDecodePayload($request->headers->get('Authorization'));

        if ($bearer == "" || !in_array("ROLE_USER", $bearer->roles)) {
            return new JsonResponse(
                ['message' => 'user non habilité !'],
                Response::HTTP_UNAUTHORIZED, 
                ['Content-Type' => 'application/json;charset=UTF-8'], 
            );
        }

        foreach ($car->getImages() as $image) {
            $em->remove($image);
        }
        $em->flush();
        $em->remove($car);
        $em->flush();

        return $this->json(['message' => 'car deleted!'], 
            JsonResponse::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
        );

    }

    public function jwtDecodePayload (string | null $jwt) 
    {

        if ($jwt != null) {

            $tokenPayload = base64_decode(explode('.', str_replace(
                "Bearer ",
                "",
                $jwt
            ))[1]);
            return json_decode($tokenPayload);    

        } 

        return "";

    }

}
