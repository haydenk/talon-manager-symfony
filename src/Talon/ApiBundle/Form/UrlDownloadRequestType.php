<?php

namespace Talon\ApiBundle\Form;

use Symfony\Component\Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Talon\ApiBundle\Entity\UrlDownloadRequest;

class UrlDownloadRequestType extends Form\AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        if (strtoupper($options['method']) === 'DELETE') {
            return;
        }

        $builder
            ->add('url')
            ->add('status')
        ;

        $builder
            ->addEventListener(Form\FormEvents::PRE_SUBMIT, function(Form\FormEvent $event) {
                $data = $event->getData();

                if (false === array_key_exists('status', $data) || trim($data['status']) === '') {
                    $data['status'] = 'Pending';
                }

                $event->setData($data);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UrlDownloadRequest::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }

}
