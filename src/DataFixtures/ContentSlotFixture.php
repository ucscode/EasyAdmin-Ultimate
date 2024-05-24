<?php

namespace App\DataFixtures;

use App\Entity\ContentSlot;
use App\Utils\ContentSlotUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContentSlotFixture extends Fixture
{
    public const SCRIPT_CONTENT = [
        [
            'title' => 'Example 1',
            'slot' => ContentSlotUtils::SLOT_HEADER,
            'targets' => [
                ContentSlotUtils::TARGET_ADMIN,
                ContentSlotUtils::TARGET_USER,
            ],
            'sort' => 1,
            'enabled' => true,
        ],
        [
            'title' => 'Example 2',
            'slot' => ContentSlotUtils::SLOT_HEADER,
            'targets' => [
                ContentSlotUtils::TARGET_OTHERS,
            ],
            'sort' => 0,
            'enabled' => true,
        ],
        [
            'title' => 'Example 3',
            'slot' => ContentSlotUtils::SLOT_FOOTER,
            'targets' => [
                ContentSlotUtils::TARGET_ADMIN,
                ContentSlotUtils::TARGET_USER,
                ContentSlotUtils::TARGET_SECURITY,
                ContentSlotUtils::TARGET_OTHERS,
            ],
            'sort' => 1,
            'enabled' => true,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach(self::SCRIPT_CONTENT as $key => $slotinfo) {
            $contentSlot = (new ContentSlot())
                ->setTitle($slotinfo['title'])
                ->setSlot($slotinfo['slot'])
                ->setTargets($slotinfo['targets'])
                ->setSort($slotinfo['sort'])
                ->setEnabled($slotinfo['enabled'])
                ->setContent($this->getContent($key))
            ;

            $manager->persist($contentSlot);
        }

        $manager->flush();
    }

    protected function getContent(int $key): string
    {
        $codes = [
            "<script src='path/to/a/file.js'></script>",
            "<style>background-color: blue;</style>",
            "<script>alert('This is a working example of content slot');</script>",
            "<script>console.log('Thank you for using ucscode/easyadmin-ultimate package');</script>"
        ];

        return $codes[$key];
    }
}