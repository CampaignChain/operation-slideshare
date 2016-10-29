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

use CampaignChain\CoreBundle\Entity\Action;
use Doctrine\Common\Persistence\ManagerRegistry;
use CampaignChain\CoreBundle\Entity\Medium;
use CampaignChain\CoreBundle\Job\JobActionInterface;
use Symfony\Component\HttpFoundation\Response;

class PublishSlideshow implements JobActionInterface
{
    protected $em;
    protected $container;
    protected $message;
    protected $operation;
    protected $url;

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
    }

    public function execute($operationId)
    {
        $slideshow = $this->em
          ->getRepository('CampaignChainOperationSlideShareBundle:Slideshow')
          ->findOneByOperation($operationId);

        if (!$slideshow) {
            throw new \Exception(
                'No slideshow found for an operation with ID: '.$operationId
            );
        }    
        
        $client = $this->container->get('campaignchain.channel.slideshare.rest.client');
        $connection = $client->connectByActivity(
            $slideshow->getOperation()->getActivity()
        );    
        $xml = $connection->publishUserSlideshow($slideshow->getIdentifier());

        if ($xml->SlideShowID == $slideshow->getIdentifier()) {
            $slideshow->getOperation()->setStatus(Action::STATUS_CLOSED);

            // Schedule data collection for report
            $report = $this->container->get('campaignchain.job.report.slideshare.publish_slideshow');
            $report->schedule($slideshow->getOperation());

            $this->em->flush();

            $this->message = 'The slideshow with the URL "'.
              $slideshow->getUrl().'" has been made public on SlideShare. See it on SlideShare:
              <a href="'.$slideshow->getUrl().'">'.$slideshow->getUrl().'</a>';

            return self::STATUS_OK;     
        } else {
            // TODO: fix to handle various errors shown at http://www.slideshare.net/developers/documentation
            if (strtolower($xml->Message) == 'slideshow not found') {
            
            } else if (strtolower($xml->Message) == 'failed api validation') {
            
            } else {
            
            }            
        }
        
    }

}