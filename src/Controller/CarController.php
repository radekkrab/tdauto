<?php

namespace App\Controller;

use App\Entity\RequestEntity;
use App\Repository\CarRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class CarController extends AbstractController
{
    private CarRepository $carRepository;

    private ProgramRepository $programRepository;

    public function __construct(CarRepository $carRepository, ProgramRepository $programRepository)
    {
        $this->carRepository = $carRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * @Route("/api/v1/cars", methods={"GET"})
     */
    public function getCars(): JsonResponse
    {
        // Получение списка автомобилей из репозитория
        $cars = $this->carRepository->findAll();

        // Преобразование объектов автомобилей в массив для JSON-ответа
        $carData = [];
        foreach ($cars as $car) {
            $carData[] = [
                'id' => $car->getId(),
                'brand' => [
                    'id' => $car->getBrand()->getId(),
                    'name' => $car->getBrand()->getName(),
                ],
                'photo' => $car->getPhoto(),
                'price' => $car->getPrice(),
            ];
        }

        // Возвращаем JSON-ответ
        return new JsonResponse($carData);
    }

    /**
     * @Route("/api/v1/cars/{id}", methods={"GET"})
     */
    public function getCar(int $id): JsonResponse
    {
        // Получение автомобиля по ID
        $car = $this->carRepository->find($id);

        // Проверка, существует ли автомобиль
        if (!$car) {
            return new JsonResponse(['error' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        // Формирование ответа в нужном формате
        $response = [
            'id' => $car->getId(),
            'brand' => [
                'id' => $car->getBrand()->getId(),
                'name' => $car->getBrand()->getName(),
            ],
            'model' => [
                'id' => $car->getModel()->getId(),
                'name' => $car->getModel()->getName(),
            ],
            'photo' => $car->getPhoto(),
            'price' => $car->getPrice(),
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/v1/credit/calculate", methods={"GET"})
     */
    public function calculateCredit(Request $request): JsonResponse
    {
        $programms = $this->programRepository->findAll();

        // Получаем входные данные из запроса
        $price = (int) $request->query->get('price');
        $initialPayment = (float) $request->query->get('initialPayment');
        $loanTerm = (int) $request->query->get('loanTerm');

        // Проверка входных данных
        if ($price <= 0 || $initialPayment < 0 || $loanTerm <= 0) {
            return new JsonResponse(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }

        // Логика выбора программы
        $programID = null;
        $interestRate = 0.0;
        $title = '';

        // Условия для выбора программы
        if ($initialPayment > 200000 && $loanTerm < 60) {
            $programID = $programms[1]->getId(); // Используем метод getId()
            $interestRate = $programms[1]->getInterestRate(); // Используем метод getInterestRate()
            $title = $programms[1]->getTitle(); // Используем метод getTitle()
        } else {
            $programID = $programms[0]->getId(); // Используем метод getId() для первой программы
            $interestRate = $programms[0]->getInterestRate(); // Используем метод getInterestRate()
            $title = $programms[0]->getTitle(); // Используем метод getTitle()
        }

        $loanAmount = $price - $initialPayment;
        $monthlyPayment = ($loanAmount * ($interestRate / 100 / 12)) / (1 - pow(1 + ($interestRate / 100 / 12), -$loanTerm));

        $response = [
            "programId" => $programID,
            "interestRate" => round($interestRate, 1),
            "monthlyPayment" => (int) round($monthlyPayment),
            "title" => $title
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/v1/request", methods={"POST"})
     */
    public function saveRequest(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Проверка входных данных
        if (empty($data['carId']) || empty($data['programId']) || empty($data['initialPayment']) || empty($data['loanTerm'])) {
            return new JsonResponse(['error' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        // Создание новой заявки
        $requestEntity = new RequestEntity();
        $requestEntity->setCar($this->carRepository->find($data['carId'])); // Предполагается, что у вас есть carRepository
        $requestEntity->setProgram($this->programRepository->find($data['programId'])); // Предполагается, что у вас есть programRepository
        $requestEntity->setInitialPayment((int) $data['initialPayment']);
        $requestEntity->setLoanTerm((int) $data['loanTerm']);

        // Сохранение заявки в базе данных
        $entityManager = $doctrine->getManager();
        
        $entityManager->persist($requestEntity);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
