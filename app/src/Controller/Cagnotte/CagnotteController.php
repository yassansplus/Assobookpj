<?php

namespace App\Controller\Cagnotte;

use App\Entity\Association;
use App\Entity\Cagnotte;
use App\Form\CagnotteType;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Error;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CagnotteController extends AbstractController
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/cagnotte/paiement/{id}", name="payment")
     * @Security("is_granted('ROLE_ADH_CONFIRME')", statusCode=403, message="Acces denied")
     */
    public function cagnotte(Association $association): Response
    {
        $form = $this->createForm(CagnotteType::class);
        return $this->render('cagnotte/index.html.twig',[
            "form" => $form->createView(),
            "association" => $association
        ]);
    }

    /**
     * @Route("/cagnotte/merci", name="merci", methods={"POST"})
     * @Security("is_granted('ROLE_ADH_CONFIRME')", statusCode=403, message="Acces denied")
     */
    public function merci(Request $request): Response{
        //requestStack -> use in 5.3 because new session is deprecated but docker use symfo 5.1
        //$session = $this->requestStack->getSession();

        $session = new Session();
        if($session->get('form')){
            $session->remove('form');
            return $this->redirectToRoute('default_connect');
        }

        $data = $request->request->all();
        $cagnotte = new Cagnotte();
        $form = $this->createForm(CagnotteType::class,$cagnotte);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cagnotte->setAssociation($this->em->getRepository(Association::class)->findOneById((int)$data["id-assoc"]));
            $cagnotte->setDonateur($this->getUser()->getAdherent());
            $cagnotte->setMontant((float) $data["cagnotte"]["montant"]);
            $this->em->persist($cagnotte);
            $this->em->flush();
            $session->set('form', 'true');
        }

        return $this->render('cagnotte/merci.html.twig',[
            "cagnotte" => $cagnotte
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
                'description' =>  $json_obj->items[0]->name,
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
