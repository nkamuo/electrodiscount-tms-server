<?php

namespace App\Controller\Shipment;

use App\Entity\Shipment\Shipment;
use App\Repository\Shipment\ShipmentRepository;
use App\Service\Shipment\ShipmentNotificationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

class ShipmentPacklistRequestController extends AbstractController
{


    public function __construct(
        private ShipmentRepository $shipmentRepository,
        private ShipmentNotificationManager $notifier,
    ) {
    }

    #[Route('/shipment/shipment/packlist/{code}/request', name: 'app_shipment_shipment_packlist_request', defaults: ['_signed' => true],)]
    public function index(string $code): Response
    {
        $json = base64_decode($code);
        $shipmentIds = json_decode($json, true);

        /** @var Shipment[] */
        $shipments = [];

        foreach ($shipmentIds as $id) {
            $shipment = $this->shipmentRepository->find($id);
            if (!($shipment instanceof Shipment)) {
                throw $this->createNotFoundException('Shipment not found');
            }
            $shipments[] = $shipment;
        }

        $output = $this->generatePDF($shipments);

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="packlist.pdf"'
        ]);
    }


    /** @param Shipment[] $shipments */
    private function generatePDF(array $shipments): string
    {
        return $this->notifier->generatePDF($shipments);
    }
}
