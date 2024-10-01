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
    public function invalidCache(string $url = ''): bool
    {
        $secretKey = CloudfrontCacheInvalidator::$plugin->settings->secretKey;
        $apiToken = CloudfrontCacheInvalidator::$plugin->settings->apiToken;
        $distribId = CloudfrontCacheInvalidator::$plugin->settings->distribId;

        if (substr($secretKey, 0, 1) == '$' && getenv('CLOUDFRONT_SECRET_KEY')) {
            $secretKey = getenv('CLOUDFRONT_SECRET_KEY');
        }

        if (substr($apiToken, 0, 1) == '$' && getenv('CLOUDFRONT_API_TOKEN')) {
            $apiToken = getenv('CLOUDFRONT_API_TOKEN');
        }

        if (substr($distribId, 0, 1) == '$' && getenv('CLOUDFRONT_DISTRIB_ID')) {
            $distribId = getenv('CLOUDFRONT_DISTRIB_ID');
        }

        $path = '/*';

        if ($url) {
            $path = str_replace(getenv('S3_BASE_URL'), '', $url);
        }

        $caller = $this->_generateRandomString(16);

        $cloudFront = new CloudFrontClient([
            'version' => 'latest',
            'region' => getenv('S3_BUCKET_REGION') ?? 'ca-central-1',
            'credentials' => [
                'key' => $apiToken,
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
    private function _generateRandomString(int $length = 10): string
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
