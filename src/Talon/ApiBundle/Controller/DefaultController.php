<?php

namespace Talon\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends Controller
{
    /**
     * @Rest\View
     * @return array
     * @throws \Exception
     */
    public function indexAction()
    {
        return [
            'hello' => 'world',
            'datetime' => new \DateTimeImmutable(),
        ];
    }
}
