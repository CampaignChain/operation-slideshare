<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
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
    public function removeOperation($id){
        try {
            $operation = $this->getSlideshowByOperation($id);
            $this->em->remove($operation);
            $this->em->flush();
        } catch (\Exception $e) {

        }
    }
}