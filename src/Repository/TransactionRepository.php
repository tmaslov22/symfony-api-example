<?php

namespace App\Repository;

use App\Entity\Flight;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Transaction::class);
        $this->manager = $manager;
    }

    public function saveFlightTransaction(Flight $flight, User $user, $position, $payment_type)
    {
        $transaction = $this->findOneBy(['flight' => $flight->getId(), 'position' => $position]);

        if($transaction) {
            // Только владелец брони может её купить или вернуть билет
            if($transaction->getUser()->getId() == $user->getId()) {
                if ($transaction->getStatus() == 'reservation' && $payment_type == 'buy') {
                    $transaction->setStatus('buy');
                } elseif ($payment_type == 'ban') {
                    $transaction->setStatus($payment_type);
                } else {
                    throw new \Exception('User cant '.$payment_type.' this position');
                }
            } elseif($transaction->getStatus() == 'ban') {
                // Предыдущий покупатель вернул билет
                $transaction->setStatus($payment_type);
            }

        } else {
            $transaction = new Transaction();
            $transaction->setFlight($flight);
            $transaction->setPosition($position);
        }

        $transaction->setStatus($payment_type);
        $transaction->setUser($user);

        $total = 10000;
        if($payment_type == 'reservation') {
            $total = 1000;
        }

        $transaction->setTotalMoney($total);

        $this->manager->persist($transaction);
        $this->manager->flush();
    }
}
