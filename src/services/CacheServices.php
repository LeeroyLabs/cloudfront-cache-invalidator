<?php

namespace leeroy\cloudfrontcacheinvalidator\services;

use Aws\CloudFront\CloudFrontClient;
use craft\base\Component;
use JsonException;
use leeroy\cloudfrontcacheinvalidator\CloudfrontCacheInvalidator;

class CacheService extends Component
{
    /**
     *
     * Use Cloudfront api to invalid cache
     *
     * @param string $url
     * @return bool
     * @throws JsonException
     */
    public function invalidCache(string $url = ''):bool
    {
        $secretKey = CloudfrontCacheInvalidator::$plugin->settings->secretKey;
        $apiToken  = CloudfrontCacheInvalidator::$plugin->settings->apiToken;
        $distribId = CloudfrontCacheInvalidator::$plugin->settings->distribId;
        $path = '/*';

        if ($url) {
            $path = str_replace(getenv('S3_BASE_URL'), '', $url);
        }

        $caller = $this->_generateRandomString(16);

        $cloudFront = new CloudFrontClient([
            'version'     => 'latest',
            'region'      =>  getenv('S3_REGION'),
            'credentials' => [
                'key'    => $apiToken,
                'secret' => $secretKey
            ]
        ]);

        $result = $cloudFront->createInvalidation([
            'DistributionId' => $distribId,
            'InvalidationBatch' => [
                'CallerReference' => $caller,
                'Paths' => [
                    'Items' => [$path],
                    'Quantity' => 1
                ]
            ]
        ]);

        return true;
    }

    /**
     * @param int $length
     * @return string
     */
    private function _generateRandomString(int $length = 10):string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}