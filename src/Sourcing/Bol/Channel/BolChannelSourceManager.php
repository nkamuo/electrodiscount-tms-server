<?php

namespace App\Sourcing\Bol\Channel;

use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use App\Entity\Shipment\Shipment;
use App\Sourcing\Bol\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Channel\ChannelSourceManager;
use App\Sourcing\Channel\OrderToShipmentMapper;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BolChannelSourceManager extends ChannelSourceManager
{


    public function __construct(

        private ChannelEntityRepositoryFactoryInterface $orderRepositoryFactory,
        private ChannelEntityRepositoryFactoryInterface $productRepositoryFactory,
        private OrderToShipmentMapper $orderToShipmentMapper,
        private AccessTokenProviderInterface $accessTokenProvider,
        private HttpClientInterface $httpClient,
        private array $config = [],

    ) {
        parent::__construct(
            $orderRepositoryFactory,
            $productRepositoryFactory,
            $orderToShipmentMapper,
            $config
        );
    }

    public function commitShipment(Shipment $shipment, Order $order): mixed
    {
        $reference = $shipment->getCode();
        $shipmentInfo = $this->createShipmentForOrder($order, $reference);
        return $shipmentInfo;
        //TODO: REVERT THIS IN ORDER TO COMMIT SHIPMENT
        // return null;
    }


    private function createShipmentForOrder(Order $order, ?string $reference = null): array
    {
        try {

            $authToken = $this->getAccessToken($order->getChannel());

            $orderItems = [];
            foreach ($order->getItems() as $orderItem) {
                $item = [
                    'orderItemId' => $orderItem->getChannelOrderItemId(),
                    'quantity' => $orderItem->getQuantity(),
                ];
                $orderItems[] = $item;
            }


            // Make a POST request with $this->httpClient
            $response = $this->httpClient->request('POST', 'https://api.bol.com/retailer/shipments', [
                'json' => [
                    'orderItems' => $orderItems,
                    'shipmentReference' => $reference,
                    'transport' => [
                        'transporterCode' => 'COURIER',//'OTHER',
                        'trackAndTrace' => 'ChauffeurBelt30minOpVoorhand', //'Carrier will call 30 min in advance',
                    ]
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                    'Content-Type' => 'application/vnd.retailer.v10+json',
                    'Accept' => 'application/vnd.retailer.v10+json',
                ]
            ]);

            // Process the response
            // $statusCode = $response->getStatusCode();
            $data = $response->toArray();
            return $data;

            // Your code here
        } catch (\Throwable $err) {
            throw $err;
        }
    }

    public function getAccessToken(Channel $channel): string
    {
        return $this->accessTokenProvider->getAccessTokenForChannel($channel);
    }
}
