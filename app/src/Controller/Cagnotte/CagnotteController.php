<?php

namespace App\Controller\Cagnotte;

use PhpParser\Error;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CagnotteController extends AbstractController
{
    /**
     * @Route("/cagnotte", name="cagnotte")
     */
    public function index(): Response
    {
        return $this->render('cagnotte/cagnotte/index.html.twig', [
            'controller_name' => 'CagnotteController',
        ]);
    }

    /**
     * @Route("/stripe", name="stripe")
     */
    public function stripe(): Response
    {
        Stripe::setApiKey('sk_test_51J6zl2BYvgiERdxUhmlt3VIiGu8b6IpcUAYdirr5r3aDJ8n8kavaA5ehrVS836UwziWI6uwzE469YYlwFrz6whoX00fQ4xJzRU');

        header('Content-Type: application/json');
        try {

            $json_str = file_get_contents('php://input');
            $json_obj = json_decode($json_str);

            $paymentIntent = PaymentIntent::create([
                'amount' => $json_obj->items[0]->price * 100,
                'currency' => 'eur',
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            return new JsonResponse($output);
        } catch (Error $e) {
            return new JsonResponse(['error' => $e->getMessage()],500);
        }
    }

}
