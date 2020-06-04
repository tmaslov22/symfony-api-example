<?php
namespace App\Controller;

use App\Repository\FlightRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FlightTransactionController
{
    private $transactionRepository;
    private $flightRepository;
    private $userRepository;

    public function __construct(
        TransactionRepository $transactionRepository, FlightRepository $flightRepository, UserRepository $userRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->flightRepository = $flightRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/flight_transactions/", name="add_transaction", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $this->validateParams($data);

        $flight = $this->flightRepository->findOrFail($data['flight_id']);

        Validator::flightStatusToBuy($flight);

        $user = $this->userRepository->findOrFail($data['user_id']);

        $this->transactionRepository->saveFlightTransaction($flight, $user, $data['position'], $data['payment_type']);

        return new JsonResponse(['status' => 'Flight transaction created!'], Response::HTTP_CREATED);
    }

    private function validateParams($data)
    {
        if($data['position'] > 150 && $data['position'] < 1) {
            throw new NotFoundHttpException('Unknown position');
        }
        Validator::required($data, ['flight_id', 'user_id', 'position', 'payment_type']);
        Validator::paramExist($data['payment_type'], ['buy', 'reservation', 'ban']);
    }
}