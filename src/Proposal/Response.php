<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Proposal;

use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

class Response
{
    /**
     * @var ProposalResponse|\Exception
     */
    private $response;

    /**
     * ProposalResponse constructor.
     * @param mixed $response
     */
    private function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param ProposalResponse $response
     * @return Response
     */
    public static function fromProposalResponse(ProposalResponse $response): Response
    {
        return new Response($response);
    }

    /**
     * @param \Exception $exception
     * @return Response
     */
    public static function fromException(\Exception $exception): Response
    {
        return new Response($exception);
    }

    /**
     * @return ProposalResponse|null
     */
    public function getProposalResponse(): ?ProposalResponse
    {
        return $this->isException() ? null : $this->response;
    }

    /**
     * @return \Exception|null
     */
    public function getException(): ?\Exception
    {
        return $this->isException() ? $this->response : null;
    }

    /**
     * @return bool
     */
    public function isException(): bool
    {
        return $this->response instanceof \Exception;
    }
}
