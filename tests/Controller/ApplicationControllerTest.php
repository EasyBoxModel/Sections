<?php

namespace EBM\Controller;

use PHPUnit\Framework\TestCase;
use EBM\UIApplication\Factory;
use EBM\Model\User;

class ApplicationControllerTest extends TestCase
{
    private $uiApplication;

    public function setUp()
    {
        $this->uiApplication = Factory::get();
    }

    public function testSectionGetsUserModelInstance()
    {
        $this->uiApplication->registerInstance('user', new User);

        $section = $this->uiApplication
                    ->getSectionBySlug('example-section')
                    ->setFields();

        $user = $section
                ->getUIApplication()
                ->getInstance('user');

        $this->assertInstanceOf(\EBM\Model\User::class, $user);
    }
}
