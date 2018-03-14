<?php

namespace Oro\Bundle\PayPalExpressBundle\Tests\Unit\Method\Translator;

use Oro\Bundle\PayPalExpressBundle\Method\Config\PayPalExpressConfig;
use Oro\Bundle\PayPalExpressBundle\Method\Translator\MethodConfigTranslator;
use Oro\Bundle\PayPalExpressBundle\Transport\DTO\ApiContextInfo;
use Oro\Bundle\PayPalExpressBundle\Transport\DTO\CredentialsInfo;

class MethodConfigTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetApiContextInfo()
    {
        $translator = new MethodConfigTranslator();

        $clientId = 'AxBU5pnHF6qNArI7Nt5yNqy4EgGWAU3K1w0eN6q77GZhNtu5cotSRWwZ';
        $clientSecret = 'BxBU5pnHF6qNArI7Nt5yNqy4EgGWAU3K1w0eN6q77GZhNtu5cotSRWwZ';
        $isSandbox = false;

        $expectedApiContextInfo = new ApiContextInfo(
            new CredentialsInfo(
                $clientId,
                $clientSecret
            ),
            $isSandbox
        );

        $config = new PayPalExpressConfig(
            'test',
            'test',
            'test',
            $clientId,
            $clientSecret,
            'test',
            $isSandbox
        );

        $apiContextInfo = $translator->getApiContextInfo($config);
        $this->assertEquals($expectedApiContextInfo, $apiContextInfo);
    }
}