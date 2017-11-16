<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Serializer;

class BinaryStringSerializer
{
    /**
     * @param array $array
     * @return string
     */
    public function serialize(array $array): string
    {
        $str = '';

        foreach ($array as $value) {
            $str .= \chr((int) $value);
        }

        return $str;
    }

    /**
     * @param string $string
     * @return array
     */
    public function deserialize(string $string): array
    {
        return \unpack('c*', $string);
    }
}
