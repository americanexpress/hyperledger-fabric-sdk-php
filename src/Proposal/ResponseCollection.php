<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Proposal;

use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

class ResponseCollection
{
    /**
     * @var Response[]
     */
    private $responses;

    /**
     * ProposalResponseCollection constructor.
     * @param mixed[] $responses
     */
    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    /**
     * @return ProposalResponse[]
     */
    public function getProposalResponses(): array
    {
        return \array_filter(\array_map(function (Response $response) {
            return $response->getProposalResponse();
        }, $this->responses));
    }

    /**
     * @return bool
     */
    public function hasProposalResponses(): bool
    {
        return \count($this->getProposalResponses()) > 0;
    }

    /**
     * @return \Exception[]
     */
    public function getExceptions(): array
    {
        return \array_filter(\array_map(function (Response $response) {
            return $response->getException();
        }, $this->responses));
    }

    /**
     * @return bool
     */
    public function hasExceptions(): bool
    {
        return \count($this->getExceptions()) > 0;
    }
}
