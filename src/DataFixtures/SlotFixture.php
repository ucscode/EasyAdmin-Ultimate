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
            'positions' => [SlotInterface::POSITION_HEADER],
            'targets' => [
                'TARGET_ADMIN',
                'TARGET_USER',
            ],
            'sort' => 1,
            'enabled' => true,
            'content' => "console.log('slot in header');"
        ],
        [
            'title' => 'Example 2',
            'positions' => [SlotInterface::POSITION_FOOTER],
            'targets' => [
                'TARGET_OTHERS',
            ],
            'sort' => 0,
            'enabled' => true,
            'content' => "console.log('slot in footer');"
        ],
        [
            'title' => 'Example 3',
            'positions' => [
                SlotInterface::POSITION_FOOTER,
                SlotInterface::POSITION_HEADER
            ],
            'targets' => [
                'TARGET_ADMIN',
                'TARGET_USER',
                'TARGET_SECURITY',
                'TARGET_OTHERS',
            ],
            'sort' => 1,
            'enabled' => true,
            'content' => "console.log('slot in header & footer');"
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach(self::SCRIPT_CONTENT as $key => $slotinfo) {
            $contentSlot = (new Slot())
                ->setTitle($slotinfo['title'])
                ->setPositions($slotinfo['positions'])
                ->setTargets($slotinfo['targets'])
                ->setSort($slotinfo['sort'])
                ->setEnabled($slotinfo['enabled'])
                ->setContent(sprintf("<script>%s</script>", $slotinfo['content']))
            ;

            $manager->persist($contentSlot);
        }

        $manager->flush();
    }
}
