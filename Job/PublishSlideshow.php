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

use CampaignChain\CoreBundle\Entity\Action;
use Doctrine\ORM\EntityManager;
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