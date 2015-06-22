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
use CampaignChain\CoreBundle\Job\JobServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class PublishSlideshow implements JobServiceInterface
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
        $url = $slideshow->getUrl();
        $response = $connection->getSlideshowByUrl($url);
        $id = $response->{'ID'};
        $connection = $client->publishSlideshow($id);    
        
        $slideshow->getOperation()->setStatus(Action::STATUS_CLOSED);

        $location = $slideshow->getOperation()->getLocations()[0];
        $location->setIdentifier($id);
        $location->setUrl($url);
        $location->setStatus(Medium::STATUS_ACTIVE);

        $this->em->flush();

        $this->message = 'The slideshow with the URL "'.
          $url.'" has been made public on SlideShare. See it on SlideShare:
          <a href="'.$url.'">'.$url.'</a>';

        return self::STATUS_OK;        
    }

}