<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Operation\SlideShareBundle\Form\Type;

use CampaignChain\CoreBundle\Form\Type\OperationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SlideShareOperationType extends OperationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('slideshow', 'choice', array(
            'choices'   => $this->operationDetail,
            'required'  => true,
            'label' => false,
            'attr' => array(
                'placeholder' => 'Select a slideshow',
                'help_text' => 'Only slideshows that are private are being listed.',
            ),
        ));     
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $defaults = array();
        $resolver->setDefaults($defaults);
    }

    public function getName()
    {
        return 'campaignchain_operation_slideshare_slideshow';
    }
}
