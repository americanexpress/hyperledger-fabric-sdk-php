<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: msp/msp_config.proto

namespace Hyperledger\Fabric\Protos\MSP;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * FabricMSPConfig collects all the configuration information for
 * a Fabric MSP.
 * Here we assume a default certificate validation policy, where
 * any certificate signed by any of the listed rootCA certs would
 * be considered as valid under this MSP.
 * This MSP may or may not come with a signing identity. If it does,
 * it can also issue signing identities. If it does not, it can only
 * be used to validate and verify certificates.
 *
 * Generated from protobuf message <code>msp.FabricMSPConfig</code>
 */
class FabricMSPConfig extends \Google\Protobuf\Internal\Message
{
    /**
     * Name holds the identifier of the MSP; MSP identifier
     * is chosen by the application that governs this MSP.
     * For example, and assuming the default implementation of MSP,
     * that is X.509-based and considers a single Issuer,
     * this can refer to the Subject OU field or the Issuer OU field.
     *
     * Generated from protobuf field <code>string name = 1;</code>
     */
    private $name = '';
    /**
     * List of root certificates trusted by this MSP
     * they are used upon certificate validation (see
     * comment for IntermediateCerts below)
     *
     * Generated from protobuf field <code>repeated bytes root_certs = 2;</code>
     */
    private $root_certs;
    /**
     * List of intermediate certificates trusted by this MSP;
     * they are used upon certificate validation as follows:
     * validation attempts to build a path from the certificate
     * to be validated (which is at one end of the path) and
     * one of the certs in the RootCerts field (which is at
     * the other end of the path). If the path is longer than
     * 2, certificates in the middle are searched within the
     * IntermediateCerts pool
     *
     * Generated from protobuf field <code>repeated bytes intermediate_certs = 3;</code>
     */
    private $intermediate_certs;
    /**
     * Identity denoting the administrator of this MSP
     *
     * Generated from protobuf field <code>repeated bytes admins = 4;</code>
     */
    private $admins;
    /**
     * Identity revocation list
     *
     * Generated from protobuf field <code>repeated bytes revocation_list = 5;</code>
     */
    private $revocation_list;
    /**
     * SigningIdentity holds information on the signing identity
     * this peer is to use, and which is to be imported by the
     * MSP defined before
     *
     * Generated from protobuf field <code>.msp.SigningIdentityInfo signing_identity = 6;</code>
     */
    private $signing_identity = null;
    /**
     * OrganizationalUnitIdentifiers holds one or more
     * fabric organizational unit identifiers that belong to
     * this MSP configuration
     *
     * Generated from protobuf field <code>repeated .msp.FabricOUIdentifier organizational_unit_identifiers = 7;</code>
     */
    private $organizational_unit_identifiers;
    /**
     * FabricCryptoConfig contains the configuration parameters
     * for the cryptographic algorithms used by this MSP
     *
     * Generated from protobuf field <code>.msp.FabricCryptoConfig crypto_config = 8;</code>
     */
    private $crypto_config = null;
    /**
     * List of TLS root certificates trusted by this MSP.
     * They are returned by GetTLSRootCerts.
     *
     * Generated from protobuf field <code>repeated bytes tls_root_certs = 9;</code>
     */
    private $tls_root_certs;
    /**
     * List of TLS intermediate certificates trusted by this MSP;
     * They are returned by GetTLSIntermediateCerts.
     *
     * Generated from protobuf field <code>repeated bytes tls_intermediate_certs = 10;</code>
     */
    private $tls_intermediate_certs;

    public function __construct() {
        \GPBMetadata\Msp\MspConfig::initOnce();
        parent::__construct();
    }

    /**
     * Name holds the identifier of the MSP; MSP identifier
     * is chosen by the application that governs this MSP.
     * For example, and assuming the default implementation of MSP,
     * that is X.509-based and considers a single Issuer,
     * this can refer to the Subject OU field or the Issuer OU field.
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Name holds the identifier of the MSP; MSP identifier
     * is chosen by the application that governs this MSP.
     * For example, and assuming the default implementation of MSP,
     * that is X.509-based and considers a single Issuer,
     * this can refer to the Subject OU field or the Issuer OU field.
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * List of root certificates trusted by this MSP
     * they are used upon certificate validation (see
     * comment for IntermediateCerts below)
     *
     * Generated from protobuf field <code>repeated bytes root_certs = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getRootCerts()
    {
        return $this->root_certs;
    }

    /**
     * List of root certificates trusted by this MSP
     * they are used upon certificate validation (see
     * comment for IntermediateCerts below)
     *
     * Generated from protobuf field <code>repeated bytes root_certs = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setRootCerts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->root_certs = $arr;

        return $this;
    }

    /**
     * List of intermediate certificates trusted by this MSP;
     * they are used upon certificate validation as follows:
     * validation attempts to build a path from the certificate
     * to be validated (which is at one end of the path) and
     * one of the certs in the RootCerts field (which is at
     * the other end of the path). If the path is longer than
     * 2, certificates in the middle are searched within the
     * IntermediateCerts pool
     *
     * Generated from protobuf field <code>repeated bytes intermediate_certs = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getIntermediateCerts()
    {
        return $this->intermediate_certs;
    }

    /**
     * List of intermediate certificates trusted by this MSP;
     * they are used upon certificate validation as follows:
     * validation attempts to build a path from the certificate
     * to be validated (which is at one end of the path) and
     * one of the certs in the RootCerts field (which is at
     * the other end of the path). If the path is longer than
     * 2, certificates in the middle are searched within the
     * IntermediateCerts pool
     *
     * Generated from protobuf field <code>repeated bytes intermediate_certs = 3;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setIntermediateCerts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->intermediate_certs = $arr;

        return $this;
    }

    /**
     * Identity denoting the administrator of this MSP
     *
     * Generated from protobuf field <code>repeated bytes admins = 4;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Identity denoting the administrator of this MSP
     *
     * Generated from protobuf field <code>repeated bytes admins = 4;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setAdmins($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->admins = $arr;

        return $this;
    }

    /**
     * Identity revocation list
     *
     * Generated from protobuf field <code>repeated bytes revocation_list = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getRevocationList()
    {
        return $this->revocation_list;
    }

    /**
     * Identity revocation list
     *
     * Generated from protobuf field <code>repeated bytes revocation_list = 5;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setRevocationList($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->revocation_list = $arr;

        return $this;
    }

    /**
     * SigningIdentity holds information on the signing identity
     * this peer is to use, and which is to be imported by the
     * MSP defined before
     *
     * Generated from protobuf field <code>.msp.SigningIdentityInfo signing_identity = 6;</code>
     * @return \Hyperledger\Fabric\Protos\MSP\SigningIdentityInfo
     */
    public function getSigningIdentity()
    {
        return $this->signing_identity;
    }

    /**
     * SigningIdentity holds information on the signing identity
     * this peer is to use, and which is to be imported by the
     * MSP defined before
     *
     * Generated from protobuf field <code>.msp.SigningIdentityInfo signing_identity = 6;</code>
     * @param \Hyperledger\Fabric\Protos\MSP\SigningIdentityInfo $var
     * @return $this
     */
    public function setSigningIdentity($var)
    {
        GPBUtil::checkMessage($var, \Hyperledger\Fabric\Protos\MSP\SigningIdentityInfo::class);
        $this->signing_identity = $var;

        return $this;
    }

    /**
     * OrganizationalUnitIdentifiers holds one or more
     * fabric organizational unit identifiers that belong to
     * this MSP configuration
     *
     * Generated from protobuf field <code>repeated .msp.FabricOUIdentifier organizational_unit_identifiers = 7;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getOrganizationalUnitIdentifiers()
    {
        return $this->organizational_unit_identifiers;
    }

    /**
     * OrganizationalUnitIdentifiers holds one or more
     * fabric organizational unit identifiers that belong to
     * this MSP configuration
     *
     * Generated from protobuf field <code>repeated .msp.FabricOUIdentifier organizational_unit_identifiers = 7;</code>
     * @param \Hyperledger\Fabric\Protos\MSP\FabricOUIdentifier[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setOrganizationalUnitIdentifiers($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Hyperledger\Fabric\Protos\MSP\FabricOUIdentifier::class);
        $this->organizational_unit_identifiers = $arr;

        return $this;
    }

    /**
     * FabricCryptoConfig contains the configuration parameters
     * for the cryptographic algorithms used by this MSP
     *
     * Generated from protobuf field <code>.msp.FabricCryptoConfig crypto_config = 8;</code>
     * @return \Hyperledger\Fabric\Protos\MSP\FabricCryptoConfig
     */
    public function getCryptoConfig()
    {
        return $this->crypto_config;
    }

    /**
     * FabricCryptoConfig contains the configuration parameters
     * for the cryptographic algorithms used by this MSP
     *
     * Generated from protobuf field <code>.msp.FabricCryptoConfig crypto_config = 8;</code>
     * @param \Hyperledger\Fabric\Protos\MSP\FabricCryptoConfig $var
     * @return $this
     */
    public function setCryptoConfig($var)
    {
        GPBUtil::checkMessage($var, \Hyperledger\Fabric\Protos\MSP\FabricCryptoConfig::class);
        $this->crypto_config = $var;

        return $this;
    }

    /**
     * List of TLS root certificates trusted by this MSP.
     * They are returned by GetTLSRootCerts.
     *
     * Generated from protobuf field <code>repeated bytes tls_root_certs = 9;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getTlsRootCerts()
    {
        return $this->tls_root_certs;
    }

    /**
     * List of TLS root certificates trusted by this MSP.
     * They are returned by GetTLSRootCerts.
     *
     * Generated from protobuf field <code>repeated bytes tls_root_certs = 9;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setTlsRootCerts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->tls_root_certs = $arr;

        return $this;
    }

    /**
     * List of TLS intermediate certificates trusted by this MSP;
     * They are returned by GetTLSIntermediateCerts.
     *
     * Generated from protobuf field <code>repeated bytes tls_intermediate_certs = 10;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getTlsIntermediateCerts()
    {
        return $this->tls_intermediate_certs;
    }

    /**
     * List of TLS intermediate certificates trusted by this MSP;
     * They are returned by GetTLSIntermediateCerts.
     *
     * Generated from protobuf field <code>repeated bytes tls_intermediate_certs = 10;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setTlsIntermediateCerts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->tls_intermediate_certs = $arr;

        return $this;
    }

}

