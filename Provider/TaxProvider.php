<?php

namespace Oro\Bundle\PayPalExpressBundle\Provider;

use Oro\Bundle\TaxBundle\Manager\TaxManager;
use Oro\Bundle\TaxBundle\Provider\TaxationSettingsProvider;
use Psr\Log\LoggerInterface;

/**
 * Responsible for providing tax amount for payment information.
 *
 * @see \Oro\Bundle\PayPalExpressBundle\Method\Translator\PaymentTransactionTranslator::getPaymentInfo
 */
class TaxProvider
{
    /**
     * @var TaxManager
     */
    protected $taxManager;

    /**
     * @var TaxationSettingsProvider
     */
    protected $taxationSettingsProvider;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TaxManager      $taxManager
     * @param LoggerInterface $logger
     */
    public function __construct(TaxManager $taxManager, LoggerInterface $logger)
    {
        $this->taxManager = $taxManager;
        $this->logger     = $logger;
    }

    /**
     * @param TaxationSettingsProvider $taxationSettingsProvider
     */
    public function setTaxationSettingsProvider(TaxationSettingsProvider $taxationSettingsProvider)
    {
        $this->taxationSettingsProvider = $taxationSettingsProvider;
    }

    /**
     * Return tax if possible, return null if not
     *
     * @param object $entity
     *
     * @return null|int
     */
    public function getTax($entity)
    {
        try {
            /**
             * NOTE: The code below has been modified to resolve an issue where no
             * tax was passed to PayPal, causing the total amount to be invalid
             * eg $11 items (inc tax) + $100 shipping (ex tax) = $121 total (inc tax)
             * If the Products already include Tax, we need to obtain the Shipping Tax
             * and include that
             *
             * This code *may* be deprecated once the Tax improvements in Oro 3.1.0-LTS are merged
             */
            if ($this->taxationSettingsProvider->isProductPricesIncludeTax()) {
                // Product Prices already include Tax, so we only want the Shipping Tax
                return $this->taxManager->loadTax($entity)->getShipping()->getTaxAmount();
            }

            // Return the Total Tax (Products + Shipping)
            return $this->taxManager->loadTax($entity)->getTotal()->getTaxAmount();
        } catch (\Throwable $exception) {
            $this->logger->info(
                'Could not load tax amount for entity',
                ['exception' => $exception, 'entity_class' => get_class($entity), 'entity_id' => $entity->getId()]
            );

            return null;
        }
    }
}
