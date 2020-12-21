<?php declare(strict_types=1);

namespace EventCandyUtils\Subscriber;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class ProductListingSubscriber
 * @package EventCandy\Sets\Storefront\Page\Product\Subscriber
 * Calculates stock before product is loaded in Storefront.
 */
class ProductSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaRepository;


    /**
     * ProductListingSubscriber constructor.
     * @param EntityRepositoryInterface $mediaRepository
     */
    public function __construct(EntityRepositoryInterface $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sales_channel.product.loaded' => ['salesChannelProductLoaded', -200]
        ];
    }

    public function salesChannelProductLoaded(SalesChannelEntityLoadedEvent $event) {
        /** @var SalesChannelProductEntity $product */
        foreach ($event->getEntities() as $product) {
            $keyIsTrue = array_key_exists('ec_product_data_pdf', $product->getCustomFields())
                && $product->getCustomFields()['ec_product_data_pdf'];
            if ($keyIsTrue) {
                $this->enrichProduct($product, $event->getSalesChannelContext()->getContext());
            }
        }

    }

    public function enrichProduct(SalesChannelProductEntity $product, Context $context)
    {
        $mediaId = $product->getCustomFields()['ec_product_data_pdf'];
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $mediaId));

        /** @var MediaEntity $result */
        $result = $this->mediaRepository->search($criteria, $context)->first();

        $customFields = $product->getCustomFields();
        $customFields['dataSheetURl'] = $result->getUrl();
        $product->setCustomFields($customFields);
    }

}