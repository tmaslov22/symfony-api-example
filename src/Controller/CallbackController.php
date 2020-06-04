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

class CallbackController
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
    public function events(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $this->validateParams($data);

        $flight = $this->flightRepository->findOrFail($data['flight_id']);

        $flight->setStatus($data['event']);

        if($data['event'] == 'flight_canceled') {
            // возвращаем деньги
            // у транзакции ставим статус money_back
            // отправляем всем сообщение на почту
        }

        return new JsonResponse(['status' => 'Flight updated!'], Response::HTTP_OK);
    }

    private function validateParams($data)
    {
        Validator::required($data, ['flight_id', 'event', 'secret_key']);
        Validator::paramExist($data['event'], ['flight_ticket_sales_completed', 'flight_canceled']);
    }
}