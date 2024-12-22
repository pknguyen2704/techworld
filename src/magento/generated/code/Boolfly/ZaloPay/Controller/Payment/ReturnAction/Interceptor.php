<?php
namespace Boolfly\ZaloPay\Controller\Payment\ReturnAction;

/**
 * Interceptor class for @see \Boolfly\ZaloPay\Controller\Payment\ReturnAction
 */
class Interceptor extends \Boolfly\ZaloPay\Controller\Payment\ReturnAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Payment\Model\MethodInterface $method, \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool)
    {
        $this->___init();
        parent::__construct($context, $checkoutSession, $method, $paymentDataObjectFactory, $orderRepository, $commandPool);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
