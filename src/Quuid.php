<?php

declare(strict_types=1);

namespace Qerbia\Quuid;

final class Quuid
{
    public const VALID_UUID8_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-8[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
    public const VALID_QUUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-8[0-9a-f]{3}-[89ab][69bd]{2}[1-8]-[0-9a-f]{12}$/i';

    private const UINT32_MAX = 4294967295;
    private const UINT32_MIN = 0;
    private const CHARSET = ['b', 'd', '6', '9'];
    private const CHARSET_LENGTH = 4;

    /**
     * Generates QUUID strings with backward decodable ID
     *
     * @throws \Random\RandomException
     */
    public function generate(int $uniqueId): string
    {
        if ($uniqueId < self::UINT32_MIN || $uniqueId > self::UINT32_MAX) {
            throw new \InvalidArgumentException('ID must be between ' . self::UINT32_MIN . ' and ' . self::UINT32_MAX . '.');
        }

        $timestamp = time();
        $idHex = dechex($uniqueId);
        $idLength = strlen($idHex);
        $randomFillData = substr(bin2hex(random_bytes(6)), 0, 12 - $idLength); // random data to complete the last block

        // QUUID format identifier character set
        $quuidSpecific = self::CHARSET[random_int(0, self::CHARSET_LENGTH - 1)]
            . self::CHARSET[random_int(0, self::CHARSET_LENGTH - 1)];

        return sprintf(
            '%08x-%04x-8%03x-%1x%2s%1d-%s%s',
            random_int(0, 0xffffffff),  // random data
            random_int(0, 0xffff),      // random data
            random_int(0, 0xfff),       // version 8 a random data
            random_int(8, 11),          // required UUID version [8-b]
            $quuidSpecific,             // specific QUUID identifier [bd69]
            $idLength,                  // id length [1-8]
            $randomFillData,            // random fill data
            $idHex                      // encoded ID
        );
    }

    /**
     * Validates QUUID strings
     */
    public function validate(string $quuid): bool
    {
        return (preg_match(self::VALID_QUUID_PATTERN, $quuid) === 1);
    }

    /**
     * Decodes the stored ID from the QUUID string
     *
     * @throws InvalidQuuidException
     */
    public function decode(string $quuid): int
    {
        if (!$this->validate($quuid)) {
            throw new InvalidQuuidException($quuid);
        }

        [, , , $fourthBlock, $fifthBlock] = explode('-', $quuid);
        // get the length of the ID from the last character of the second to last block
        $idLength = hexdec(substr($fourthBlock, -1));
        // extract the unique ID from the end of the last block
        $uniqueIdHex = substr($fifthBlock, -$idLength);
        $uniqueId = hexdec($uniqueIdHex);

        return (int)$uniqueId;
    }
}
