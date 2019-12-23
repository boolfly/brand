<?php declare(strict_types=1);

namespace Boolfly\Brand\Controller;

use Boolfly\Brand\Model\BrandFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param BrandFactory $brandFactory
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        BrandFactory $brandFactory,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->brandFactory = $brandFactory;
        $this->response = $response;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     * @throws LocalizedException
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if (strpos($identifier, 'brands') !== false) {
            $request->setModuleName('bf_brand')
                    ->setControllerName('view')
                    ->setActionName('allbrands');
            return $this->actionFactory->create(Forward::class);
        }

        $brand = $this->brandFactory->create();
        $brandId = $brand->checkUrlKey($identifier);
        if (!$brandId) {
            return null;
        }
        $request->setModuleName('bf_brand')
            ->setControllerName('view')
            ->setActionName('index')
            ->setParam('id', $brandId);
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        return $this->actionFactory->create(
            Forward::class
        );
    }
}
