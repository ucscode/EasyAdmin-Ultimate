<?php

namespace App\DataFixtures;

use App\Entity\Slot\Slot;
use App\Entity\Slot\SlotInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SlotFixture extends Fixture
{
    public const SCRIPT_CONTENT = [
        [
            'title' => 'Example 1',
            'slots' => [SlotInterface::POSITION_HEADER],
            'targets' => [
                'TARGET_ADMIN',
                'TARGET_USER',
            ],
            'sort' => 1,
            'enabled' => true,
        ],
        [
            'title' => 'Example 2',
            'slots' => [SlotInterface::POSITION_HEADER],
            'targets' => [
                'TARGET_OTHERS',
            ],
            'sort' => 0,
            'enabled' => true,
        ],
        [
            'title' => 'Example 3',
            'slots' => [SlotInterface::POSITION_FOOTER],
            'targets' => [
                'TARGET_ADMIN',
                'TARGET_USER',
                'TARGET_SECURITY',
                'TARGET_OTHERS',
            ],
            'sort' => 1,
            'enabled' => true,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach(self::SCRIPT_CONTENT as $key => $slotInfo) {
            $contentSlot = (new Slot())
                ->setTitle($slotInfo['title'])
                ->setPositions($slotInfo['slots'])
                ->setTargets($slotInfo['targets'])
                ->setSort($slotInfo['sort'])
                ->setEnabled($slotInfo['enabled'])
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
