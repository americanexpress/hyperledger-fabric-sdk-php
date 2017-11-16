<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions;

class ChannelContext extends AbstractOptions
{
    /**
     * @var string|null
     */
    private $host;

    /**
     * @var string|null
     */
    private $mspId;

    /**
     * @var \SplFileObject|null
     */
    private $adminCerts;

    /**
     * @var \SplFileObject|null
     */
    private $privateKey;

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    /**
     * @return string|null
     */
    public function getMspId(): ?string
    {
        return $this->mspId;
    }

    /**
     * @param string $mspId
     */
    public function setMspId(string $mspId)
    {
        $this->mspId = $mspId;
    }

    /**
     * @return \SplFileObject|null
     */
    public function getAdminCerts(): ?\SplFileObject
    {
        return $this->adminCerts;
    }

    /**
     * @param \SplFileObject $adminCerts
     */
    public function setAdminCerts(\SplFileObject $adminCerts)
    {
        $this->adminCerts = $adminCerts;
    }

    /**
     * @return \SplFileObject|null
     */
    public function getPrivateKey(): ?\SplFileObject
    {
        return $this->privateKey;
    }

    /**
     * @param \SplFileObject $privateKey
     */
    public function setPrivateKey(\SplFileObject $privateKey)
    {
        $this->privateKey = $privateKey;
    }
}
