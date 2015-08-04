<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Operation\SlideShareBundle\Job;

use CampaignChain\CoreBundle\Entity\SchedulerReportOperation;
use CampaignChain\CoreBundle\Job\JobReportInterface;
use Doctrine\ORM\EntityManager;

class ReportPublishSlideshow implements JobReportInterface
{
    const OPERATION_BUNDLE_NAME = 'campaignchain/operation-slideshare';
    const METRIC_VIEWS = 'Views';
    const METRIC_FAVORITES = 'Favorites';
    const METRIC_DOWNLOADS = 'Downloads';
    const METRIC_COMMENTS = 'Comments';

    protected $em;
    protected $container;
    protected $message;
    protected $operation;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getMessage(){
        return $this->message;
    }
    
    public function schedule($operation, $facts = null)
    {
        $scheduler = new SchedulerReportOperation();
        $scheduler->setOperation($operation);
        $scheduler->setInterval('1 hour');
        $scheduler->setEndAction($operation->getActivity()->getCampaign());
        $this->em->persist($scheduler);

        $facts[self::METRIC_VIEWS] = 0;
        $facts[self::METRIC_FAVORITES] = 0;
        $facts[self::METRIC_DOWNLOADS] = 0;
        $facts[self::METRIC_COMMENTS] = 0;

        $factService = $this->container->get('campaignchain.core.fact');
        $factService->addFacts('activity', self::OPERATION_BUNDLE_NAME, $operation, $facts);
    }

    public function execute($operationId)
    {
        $operationService = $this->container->get('campaignchain.core.operation');
        $operation = $operationService->getOperation($operationId);

        $client = $this->container->get('campaignchain.channel.slideshare.rest.client');
        $connection = $client->connectByActivity($operation->getActivity());
        $xml = $connection->getSlideshowById($operation->getLocations()[0]->getIdentifier());

        // Add report data.
        $facts[self::METRIC_VIEWS] = $xml->NumViews;
        $facts[self::METRIC_FAVORITES] = $xml->NumFavorites;
        $facts[self::METRIC_DOWNLOADS] = $xml->NumDownloads;
        $facts[self::METRIC_COMMENTS] = $xml->NumComments;

        $factService = $this->container->get('campaignchain.core.fact');
        $factService->addFacts('activity', self::OPERATION_BUNDLE_NAME, $operation, $facts);

        $this->message = 'Added to report: views = '.$xml->NumViews.', favorites = '.$xml->NumFavorites.', downloads = '.$xml->NumDownloads.', comments = '.$xml->NumComments.'.';

        return self::STATUS_OK;
    }

}