<?php
namespace leeroy\cloudfrontcacheinvalidator\controllers;

use Craft;
use craft\web\Controller;
use leeroy\cloudfrontcacheinvalidator\services\CacheService;
use yii\web\Response;

class CacheController extends Controller
{
    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|bool|int $allowAnonymous = [];

    public function actionInvalidCache()
    {
        $success = (new CacheService)->invalidCache();
        if ($success) {
            Craft::$app->getSession()->setFlash('notice', 'Le cache a été vidé.');
        }
    }

    /**
     * @return mixed
     */
    public function actionCloudflareCacheInvalidator()
    {
        return $this->renderTemplate('cloudfront-cache-invalidator/cache-invalidator.twig', [], Craft::$app->view::TEMPLATE_MODE_CP);
    }
}
