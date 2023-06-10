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
use App\Repository\ImageRepository;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CarController extends AbstractController
{

    #[Route('/api/car', name: 'app_post_car', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        GarageRepository $garageRepository,
        OptionRepository $optionRepository
    ): JsonResponse
    {

        $content = json_decode($request->get('car'));
        
        $newCar = $serializer->deserialize(
            $request->get('car'), 
            Car::class, 
            'json',
        );
        
        $newCar->setGarage($garageRepository->find($content->garage->id));
        
        foreach($newCar->getOptions() as $option) {
            error_log("=================>".$option->getname());
            $newCar->removeOption($option);
        }
        foreach($content->options as $option) {
            $newCar->addOption($optionRepository->find($option->id));
        }
    
        $imageFile = $request->files->get('garage_image');
        
        $newCar->getImage()->setFilename($imageFile->getClientOriginalName());
        
        $em->persist($newCar);
        $em->flush();

        $imageFile->move(
            $this->getParameter('kernel.project_dir')."/public/images",
            'car_'.$newCar->getId().'_'.$imageFile->getClientOriginalName()
        );    
        
        $cars = $serializer->serialize(
            $newCar,
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
    
    #[Route('/api/car/{id}', name: 'ap_put_car_id', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function update(
        Request $request, 
        Car $currentCar, 
        SerializerInterface $serializer, 
        ImageRepository $imageRepository,
        GarageRepository $garageRepository,
        OptionRepository $optionRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {

        $content = json_decode($request->get('car'));
        
        $newCar = $serializer->deserialize(
            $request->get('car'), 
            Car::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCar]
        );
        
        $currentCar->setGarage($garageRepository->find($content->garage->id));
        
        foreach($currentCar->getOptions() as $option) {
            error_log("=================>".$option->getname());
            $currentCar->removeOption($option);
        }
        foreach($content->options as $option) {
            $currentCar->addOption($optionRepository->find($option->id));
        }
    
        $imageOrigine = $imageRepository->find($content->image->id);
        $imageFile = $request->files->get('garage_image');
        
        if ($imageFile && $imageOrigine->getHash() !== $content->image->hash) {
            
            $imageOrigine->setFilename($imageFile->getClientOriginalName());
            $imageOrigine->setHash($newCar->getImage()->getHash());
            
            $imageFile->move(
                $this->getParameter('kernel.project_dir')."/public/images",
                'car_'.$imageOrigine->getId().'_'.$imageFile->getClientOriginalName()
            );
            
            $filesystem = new Filesystem();
            $filesystem->remove(
                'car_'.$currentCar->getImage()->getId().'_'.$currentCar->getImage()->getFilename()
            );

        }

        $currentCar->setImage($imageOrigine);

        $em->persist($currentCar);
        $em->flush();

        $carSerialized = $serializer->serialize(
            $currentCar,
            'json', 
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['garage'],
            ]
        );

        return new JsonResponse(
            $carSerialized, 
            Response::HTTP_OK, 
            ['Content-Type' => 'application/json;charset=UTF-8'], 
            true
        );

    }
    
    #[Route('/api/car/{id}', name: 'app_delete_car_id', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(
        Request $request, 
        Car $car, 
        EntityManagerInterface $em
    ): JsonResponse
    {

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

}
