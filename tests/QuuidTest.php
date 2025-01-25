<?php

declare(strict_types=1);

namespace Qerbia\Quuid\Tests;

use PHPUnit\Framework\TestCase;
use Qerbia\Quuid\InvalidQuuidException;
use Qerbia\Quuid\Quuid;

class QuuidTest extends TestCase
{
    private const UINT32_MAX = 4294967295;
    private const UINT32_MIN = 0;

    /** @var Quuid */
    public $quuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quuid = new Quuid();
    }

    /**
     * @dataProvider uuid8DataProvider
     */
    public function testUuid8Format(bool $expectedOutput, string $input): void
    {
        $this->assertEquals($expectedOutput, $this->checkUuid8Format($input));
    }

    /**
     * @return array[]
     */
    public function uuid8DataProvider(): array
    {
        return [
            // valid
            [true, '0d1b4268-e341-8873-9c21-185378350c35'],
            [true, 'd38d5a1c-a201-8af0-ab60-6d1817e5078e'],
            [true, 'c16a96c3-ab89-8fcf-b3b4-605ab7867d5f'],
            [true, '8b9446c0-535e-89ba-b1a6-1ae46553c042'],
            [true, 'bbcba7ba-67d6-8489-a374-067f963c10af'],
            [true, '71ccbfcd-f319-8406-8169-ad983ed4957f'],
            [true, '6c3108dd-155e-846b-8f1e-f993555bb29a'],
            [true, '1e00ad4d-c5c4-8c73-938a-5fe404920342'],
            [true, '22c08e38-c68a-877c-a1b5-d7557b53721d'],
            [true, '72facc44-b857-8ac2-8b9b-4248a8aaf1ca'],

            // invalid - invalid version
            [false, '0d1b4268-e341-1873-9c21-185378350c35'],
            [false, 'd38d5a1c-a201-2af0-ab60-6d1817e5078e'],
            [false, 'c16a96c3-ab89-3fcf-b3b4-605ab7867d5f'],
            [false, '8b9446c0-535e-49ba-b1a6-1ae46553c042'],
            [false, 'bbcba7ba-67d6-5489-a374-067f963c10af'],
            [false, '71ccbfcd-f319-6406-8169-ad983ed4957f'],
            [false, '6c3108dd-155e-746b-8f1e-f993555bb29a'],

            // invalid - invalid variant
            [false, '1e00ad4d-c5c4-8c73-738a-5fe404920342'],
            [false, '22c08e38-c68a-877c-c1b5-d7557b53721d'],
            [false, '72facc44-b857-8c2a-0b9b-4248a8aaf1ca'],
            [false, '8b9446c0-535e-49ba-b1a6-1ae46553c042'],
        ];
    }

    /**
     * @dataProvider quuidDataProvider
     */
    public function testQuuidFormat(bool $expectedOutput, string $input): void
    {
        $this->assertEquals($expectedOutput, $this->checkQuuidFormat($input));
    }

    /**
     * @dataProvider quuidDataProvider
     */
    public function testQuuidIsValidUuid8Format(bool $expectedOutput, string $input): void
    {
        $this->assertTrue($this->checkUuid8Format($input));
        $this->assertEquals($expectedOutput, $this->checkQuuidFormat($input));
    }

    /**
     * @return array[]
     */
    public function quuidDataProvider(): array
    {
        return [
            // detect quuid characters in uuid
            [true, '0d1b4268-e341-8873-9bb1-185378350c35'],
            [true, 'd38d5a1c-a201-8af0-abd6-6d1817e5078e'],
            [true, 'c16a96c3-ab89-8fcf-bb64-605ab7867d5f'],
            [true, '8b9446c0-535e-89ba-bb96-1ae46553c042'],
            [true, 'bbcba7ba-67d6-8489-adb4-067f963c10af'],
            [true, '71ccbfcd-f319-8406-8dd8-ad983ed4957f'],
            [true, '6c3108dd-155e-846b-8d67-f993555bb29a'],
            [true, '1e00ad4d-c5c4-8c73-9d95-5fe404920342'],
            [true, '22c08e38-c68a-877c-a6b5-d7557b537212'],
            [true, '72facc44-b857-8ac2-86d2-4248a8aaf1ca'],
            [true, '0d1b4268-e341-8873-9661-185378350c35'],
            [true, 'd38d5a1c-a201-8af0-a694-6d1817e5078e'],
            [true, 'c16a96c3-ab89-8fcf-b9b4-605ab7867d5f'],
            [true, '8b9446c0-535e-89ba-b9d6-1ae46553c042'],
            [true, '6c3108dd-155e-846b-8962-f993555bb29a'],
            [true, '1e00ad4d-c5c4-8c73-9993-5fe404920342'],
            [true, '67952779-fe16-8a53-bd98-69d2ffffffff'],

            // invalid 4 block - invalid quuid chars
            [false, '0d1b4268-e341-8873-9c11-185378350c35'],
            [false, 'd38d5a1c-a201-8af0-a3b6-6d1817e5078e'],
            [false, 'c16a96c3-ab89-8fcf-b1a4-605ab7867d5f'],
            [false, '8b9446c0-535e-89ba-b376-1ae46553c042'],
            [false, 'bbcba7ba-67d6-8489-a164-067f963c10af'],
            [false, '71ccbfcd-f319-8406-8f18-ad983ed4957f'],
            [false, '6c3108dd-155e-846b-81b7-f993555bb29a'],
            [false, '1e00ad4d-c5c4-8c73-9c95-5fe404920342'],
            [false, '22c08e38-c68a-877c-af15-d7557b537212'],
            [false, '72facc44-b857-8ac2-8d42-4248a8aaf1ca'],
            [false, '0d1b4268-e341-8873-9e61-185378350c35'],
            [false, 'd38d5a1c-a201-8af0-aa94-6d1817e5078e'],

            // invalid 4 block - last character is not a number 1-8
            [false, '1d7ead4d-b3c4-8c53-99d0-5fe505920421'],
            [false, 'c16a96c3-ab89-8fcf-bb69-605ab7867d5f'],
            [false, '8b9446c0-535e-89ba-bb9a-1ae46553c042'],
            [false, '6c3108dd-155e-846b-8d6b-f993555bb29a'],
            [false, '1e00ad4d-c5c4-8c73-9d9c-5fe404920342'],
        ];
    }

    /**
     * @dataProvider validQuuidProvider
     */
    public function testDecodeId(int $expectedOutput, string $input): void
    {
        $this->assertEquals($expectedOutput, $this->quuid->decode($input));
    }

    /**
     * @return array[]
     */
    public function validQuuidProvider(): array
    {
        return [
            // detect quuid characters in uuid
            [self::UINT32_MIN, '67954dd8-5b5e-8988-a961-7aab12edc4e0'], // min
            [self::UINT32_MAX, '67952779-fe16-8a53-bd98-69d2ffffffff'], // max
            [5, '0d1b4268-e341-8873-9bb1-185378350c35'],
            [15009678, 'd38d5a1c-a201-8af0-abd6-6d1817e5078e'],
            [32095, 'c16a96c3-ab89-8fcf-bb64-605ab7867d5f'],
            [5488706, '8b9446c0-535e-89ba-bb96-1ae46553c042'],
            [4271, 'bbcba7ba-67d6-8489-adb4-067f963c10af'],
            [1054119295, '71ccbfcd-f319-8406-8dd8-ad983ed4957f'],
            [89895578, '6c3108dd-155e-846b-8d67-f993555bb29a'],
            [131906, '1e00ad4d-c5c4-8c73-9d95-5fe404920342'],
            [225810, '22c08e38-c68a-877c-a6b5-d7557b537212'],
            [202, '72facc44-b857-8ac2-86d2-4248a8aaf1ca'],
            [5, '0d1b4268-e341-8873-9661-185378350c35'],
            [1934, 'd38d5a1c-a201-8af0-a694-6d1817e5078e'],
            [32095, 'c16a96c3-ab89-8fcf-b9b4-605ab7867d5f'],
            [5488706, '8b9446c0-535e-89ba-b9d6-1ae46553c042'],
            [154, '6c3108dd-155e-846b-8962-f993555bb29a'],
            [834, '1e00ad4d-c5c4-8c73-9993-5fe404920342'],
        ];
    }

    /**
     * @dataProvider validQuuidWithInvalidExpectProvider
     */
    public function testValidQuuidWithInvalidExpect(int $expectedOutput, string $input): void
    {
        $this->assertNotEquals($expectedOutput, $this->quuid->decode($input));
    }

    /**
     * @return array[]
     */
    public function validQuuidWithInvalidExpectProvider(): array
    {
        return [
            [4294967296, '67952779-fe16-8a53-bd98-69d2ffffffff'], // valid: 4294967295
            [115, '0d1b4268-e341-8873-9661-185378350c35'], // valid: 5
            [111934, 'd38d5a1c-a201-8af0-a694-6d1817e5078e'], // valid: 1934
            [1132095, 'c16a96c3-ab89-8fcf-b9b4-605ab7867d5f'], // valid: 32095
            [115488706, '8b9446c0-535e-89ba-b9d6-1ae46553c042'], // valid: 5488706
            [11154, '6c3108dd-155e-846b-8962-f993555bb29a'], // valid: 154
            [11834, '1e00ad4d-c5c4-8c73-9993-5fe404920342'], // valid: 834
        ];
    }

    public function testInvalidMinId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be between 0 and 4294967295');
        $this->quuid->generate(-1);
    }

    public function testInvalidMaxId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be between 0 and 4294967295');
        $this->quuid->generate(self::UINT32_MAX + 1);
    }

    /**
     * @dataProvider invalidQuuidProvider
     */
    public function testInvalidFormats(string $quuid): void
    {
        $this->expectException(InvalidQuuidException::class);
        $this->expectExceptionMessage('The uuid ' . $quuid . ' is invalid.');
        $this->quuid->decode($quuid);
    }

    /**
     * @return array[]
     */
    public function invalidQuuidProvider(): array
    {
        return [
            ['67952968-23c2-8de3-ad61-7cdf332a3cdd9b59dc66a70'],
            ['1d7ead4d-b3c4-8c53-99d0-5fe505920421'],
            ['c16a96c3-ab89-8fcf-bb69-605ab7867d5f'],
            ['8b9446c0-535e-89ba-bb9a-1ae46553c042'],
            ['6c3108dd-155e-846b-8d6b-f993555bb29a'],
            ['1e00ad4d-c5c4-8c73-9d9c-5fe404920342'],
        ];
    }

    private function checkUuid8Format(string $uuid): bool
    {
        return (preg_match(Quuid::VALID_UUID8_PATTERN, $uuid) === 1);
    }

    private function checkQuuidFormat(string $uuid): bool
    {
        return (preg_match(Quuid::VALID_QUUID_PATTERN, $uuid) === 1);
    }
}
