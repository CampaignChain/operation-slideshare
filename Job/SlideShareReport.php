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

class SlideShareReport implements JobReportInterface
{
    const OPERATION_BUNDLE_NAME = 'campaignchain/operation-slideshare';
    const METRIC_VIEWS = 'Views';
    const METRIC_LIKES = 'Likes';
    const METRIC_DOWNLOADS = 'Downloads';

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
    }

    public function execute($operationId)
    {
    }

}