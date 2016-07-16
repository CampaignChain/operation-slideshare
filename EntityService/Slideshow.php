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
