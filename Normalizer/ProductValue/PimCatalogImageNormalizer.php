<?php

namespace Actualys\Bundle\DrupalCommerceConnectorBundle\Normalizer\ProductValue;

use Pim\Bundle\CatalogBundle\Manager\MediaManager;
use Pim\Bundle\CatalogBundle\Model\ProductValue;

/**
 * Class PimCatalogImageNormalizer
 *
 * @package Actualys\Bundle\DrupalCommerceConnectorBundle\Normalizer
 */
class PimCatalogImageNormalizer extends AbstractMediaNormalizer
{
    /**
     * @param string $rootDir
     */
    public function __construct(
      $rootDir,
      MediaManager $mediaManager,
      $webservice_servername
    ) {
        $this->rootDir               = $rootDir;
        $this->mediaManager          = $mediaManager;
        $this->webservice_servername = $webservice_servername;
    }

    /**
     * @param  array                                                          $drupalProduct
     * @param  ProductValue                                                   $productValue
     * @param  string                                                         $field
     * @param  array                                                          $context
     * @throws \Pim\Bundle\CatalogBundle\Exception\MissingIdentifierException
     */
    public function normalize(
      array &$drupalProduct,
      $productValue,
      $field,
      array $context = array()
    ) {
        $media = $productValue->getMedia();
        if ($media && null !== $media->getFilename()) {
            if (preg_match(
                '/_[0-9]+$/',
                $field
              ) && $context['configuration']['mergeImages']
            ) {
                $field = preg_replace('/([_0-9]+)$/', '', $field);
            }

            $drupalProduct['values'][$field][$context['locale']][] = [
              'type'              => 'pim_catalog_image',
              'filename_original' => $media->getOriginalFilename(),
              'filename'          => $media->getFilename(),
              'mimetype'          => $media->getMimeType(),
              'length'            => filesize($media->getFilePath()),
              'absolute_path'     => $media->getFilePath(),
              'attribute_id'      => $media->getValue()->getAttribute()->getId(
              ),
              'media_id'          => $media->getId(),
              'rest_url'          => $this->webservice_servername.'/'.
                $productValue->getEntity()->getIdentifier().'/'.
                $productValue->getAttribute()->getId()
                ,
            ];
        }
    }
}
