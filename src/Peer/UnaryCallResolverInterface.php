<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Peer;

use AmericanExpress\HyperledgerFabricClient\Proposal\Response;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use Grpc\UnaryCall;

interface UnaryCallResolverInterface
{
    /**
     * @param UnaryCall $call
     * @return Response
     */
    public function resolveOne(UnaryCall $call): Response;

    /**
     * @param UnaryCall|UnaryCall[] ...$calls
     * @return ResponseCollection
     */
    public function resolveMany(UnaryCall ...$calls): ResponseCollection;
}
