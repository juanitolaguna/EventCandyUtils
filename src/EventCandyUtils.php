<?php declare(strict_types=1);

namespace EventCandyUtils;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class EventCandyUtils extends Plugin
{

    /**
     * @var CustomFieldService
     */
    private $customFieldService;

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->customFieldService->createCustomFields($activateContext->getContext());
    }


    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);
        if ($context->keepUserData()) {
            return;
        }
        $this->customFieldService->deleteCustomFields($context->getContext());
    }





    /**
     * @required
     * @param CustomFieldService $customFieldService
     */
    public function setCustomFieldService(CustomFieldService $customFieldService): void
    {
        $this->customFieldService = $customFieldService;
    }
}