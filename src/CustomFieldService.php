<?php declare(strict_types=1);

namespace EventCandyUtils;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $customFieldSetRepository;


    public function __construct(EntityRepositoryInterface $customFieldSetRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function createCustomFields(Context $context)
    {

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'ec_product_data'));

        /** @var IdSearchResult $ids */
        $ids = $this->customFieldSetRepository->searchIds($criteria, $context);
        if ($ids->getTotal() !== 0) {
            return;
        }
        $this->customFieldSetRepository->create( [
            [
                'name' => 'ec_product_data',
                'global' => true,
                'config' => [
                    'label' => [
                        'en-GB' => 'Product Data Sheet',
                        'de-DE' => 'Produktdatenblatt'
                    ]
                ],
                'customFields' => [
                    [
                        'name' => 'ec_product_data_pdf',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'componentName' => 'sw-media-field',
                            'customFieldType' => 'media',
                            'customFieldPosition' => 2,
                            'label' => [
                                'en-GB' => 'Document',
                                'de-DE' => 'Dokument',
                            ]
                        ],
                        'active' => true
                    ],
                ],
                'relations' => [
                    [
                        'entityName' => 'product'
                    ]
                ]
            ]
        ], $context);
    }

    public function deleteCustomFields(Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'ec_product_data'));

        /** @var IdSearchResult $ids */
        $ids = $this->customFieldSetRepository->searchIds($criteria, $context);

        if ($ids->getTotal() == 0) {
            return;
        }
        $this->customFieldSetRepository->delete([['id' => $ids->getIds()[0]]], $context);
    }

}
