<?php
namespace Api\Product\Model;

class ProductBy
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductByUrl($urlKey)
    {
        $filter = $this->filterBuilder
            ->setField('url_key')
            ->setConditionType('eq')
            ->setValue($urlKey)
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter]);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $products = $this->productRepository->getList($searchCriteria)->getItems();
        if (count($products) == 0) {
            return null;
        }

        foreach ($products as $product) {
            return $product;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductBySku($sku)
    {
        return $this->productRepository->get($sku);
    }
}
