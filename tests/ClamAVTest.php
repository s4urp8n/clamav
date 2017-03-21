<?php

use Zver\ClamAV;
use Zver\Common;

class ClamAVTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Test;

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }

    public function testUpdate()
    {
        ClamAV::update();
    }

    public function testScan()
    {
        $regexp = '/EICAR/i';
        $testFile = Common::getPackageTestFilePath('EICAR');

        $this->assertTrue(ClamAV::isClean(__FILE__));
        $this->assertFalse(ClamAV::isClean($testFile) === true);
        $this->assertFalse(ClamAV::isClean('unewfkm32948fdwefsdkfsdf'));

        $this->assertEmpty(ClamAV::getCleanRegexps());

        ClamAV::addCleanRegexp($regexp);

        $this->assertNotEmpty(ClamAV::getCleanRegexps());
        $this->assertSame(ClamAV::getCleanRegexps(), [$regexp]);

        $this->assertTrue(ClamAV::isClean($testFile));

        ClamAV::cleanCleanRegexps();

        $this->assertEmpty(ClamAV::getCleanRegexps());

    }

}