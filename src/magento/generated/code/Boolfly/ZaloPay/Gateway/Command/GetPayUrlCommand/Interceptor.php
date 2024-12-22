<?php
namespace Boolfly\ZaloPay\Gateway\Command\GetPayUrlCommand;

/**
 * Interceptor class for @see \Boolfly\ZaloPay\Gateway\Command\GetPayUrlCommand
 */
class Interceptor extends \Boolfly\ZaloPay\Gateway\Command\GetPayUrlCommand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Payment\Gateway\Request\BuilderInterface $requestBuilder, \Magento\Payment\Gateway\Http\TransferFactoryInterface $transferFactory, \Magento\Payment\Gateway\Http\ClientInterface $client, \Magento\Payment\Gateway\Command\Result\ArrayResultFactory $resultFactory, \Magento\Payment\Gateway\Validator\ValidatorInterface $validator)
    {
        $this->___init();
        parent::__construct($requestBuilder, $transferFactory, $client, $resultFactory, $validator);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRequestData(array $commandSubject)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'buildRequestData');
        return $pluginInfo ? $this->___callPlugins('buildRequestData', func_get_args(), $pluginInfo) : parent::buildRequestData($commandSubject);
    }
}
