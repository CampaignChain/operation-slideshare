<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Operation\SlideShareBundle\EntityService;

use Doctrine\ORM\EntityManager;

class Slideshow
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getSlideshowByOperation($id){
        $slideshow = $this->em->getRepository('CampaignChainOperationSlideShareBundle:Slideshow')
            ->findOneByOperation($id);

        if (!$slideshow) {
            throw new \Exception(
                'No slideshow found by operation id '.$id
            );
        }

        return $slideshow;
    }
}