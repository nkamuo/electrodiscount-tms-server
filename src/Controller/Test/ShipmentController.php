<?php

namespace App\Controller\Test;

use App\Entity\Shipment\Shipment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentController extends AbstractController
{
    private array $options = [
        [
            'value' => 'shipment_is_safely_delivered',
            'text' => 'Shipment is safely delivered',
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ], [
            'value' => 'shipment_is_safely_delivered',
            'text' => 'Shipment is safely delivered',
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ], [
            'value' => 'shipment_is_safely_delivered',
            'text' => 'Shipment is safely delivered',
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ],
    ];

    #[Route('/test/shipment/{shipment}', name: 'app_test_shipment')]
    public function index(Shipment $shipment): Response
    {
        return $this->render('test/shipment/index.html.twig', [
            'shipment' => $shipment,
            'shipments' => [$shipment],
            'options' => $this->options,
        ]);
    }

    #[Route('/test/shipment/{shipment}/pdf', name: 'app_test_shipment_pdf')]
    public function pdf(Shipment $shipment): Response
    {
        $content =  $this->renderView('test/shipment/index.html.twig', [
            'shipment' => $shipment,
            'shipments' => [$shipment],
        ]);

        // instantiate and use the dompdf class
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($content);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        // $dompdf->stream();

        $output = $dompdf->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="mypdf.pdf"'
        ]);
    }
}
