<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CampaignChain\Operation\SlideShareBundle\Job;

use CampaignChain\CoreBundle\Entity\SchedulerReportOperation;
use CampaignChain\CoreBundle\Job\JobReportInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function __construct(ManagerRegistry $managerRegistry, $container)
    {
        $this->em = $managerRegistry->getManager();
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