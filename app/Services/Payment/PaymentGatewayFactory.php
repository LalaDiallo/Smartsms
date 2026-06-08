<?php

namespace App\Services\Payment;

class PaymentGatewayFactory
{
    /**
     * Retourne le gateway actif selon PAYMENT_GATEWAY dans .env
     *
     * Pour ajouter un nouveau provider :
     *   1. Créer MyNewGateway implements PaymentGatewayInterface
     *   2. Ajouter une entrée dans le switch ci-dessous
     *   3. Changer PAYMENT_GATEWAY=mynewgateway dans .env
     *   C'est tout.
     */
    public static function make(): PaymentGatewayInterface
    {
        $driver = strtolower(config('services.payment_gateway.driver', 'lengopay'));

        return match ($driver) {
            'lengopay'  => new LengoPayGateway(),
            // 'cinetpay'  => new CinetPayGateway(),   // exemple futur
            // 'stripe'    => new StripeGateway(),      // exemple futur
            // 'wave'      => new WaveGateway(),        // exemple futur
            default     => throw new \InvalidArgumentException("Gateway inconnu : {$driver}"),
        };
    }
}
