<?php

namespace App\Service;

use App\Constants\FilePathConstants;
use App\Entity\Configuration;
use App\Enum\ModeEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfigurationService
{
    public const FIELD_NAME = 'metaValue';

    private array $configurationStructure = [];

    public function __construct(protected EntityManagerInterface $entityManager)
    {
        $this->buildConfigurationStructure();
    }

    /**
     * Get configuration instance by metakey
     *
     * @param string $metaKey - The meta key to find
     */
    public function getConfigurationInstance(string $metaKey): ?Configuration
    {
        return $this->entityManager->getRepository(Configuration::class)->findOneBy(['metaKey' => $metaKey]);
    }

    /**
     * Get configuration value (as string) by metakey
     *
     * @param string $metaKey - The meta key to find
     */
    public function getConfigurationValue(string $metaKey, ?string $default = null): ?string
    {
        return $this->getConfigurationInstance($metaKey)?->getMetaValueAsString() ?? $default;
    }

    /**
     * Get the configuration structure (or context if metaKey is defined)
     *
     * @param null|string $metaKey - The meta key to find for single context
     */
    public function getConfigurationStructure(?string $metaKey = null): ?array
    {
        return $metaKey === null ? $this->configurationStructure : ($this->configurationStructure[$metaKey] ?? null);
    }

    /**
     * > For internal Use
     *
     * Add a new configuration context to the configuration structure
     *
     * @param string $metaKey - The meta key to add
     * @param array $context - The context to add
     */
    final public function addConfigurationContext(string $metaKey, array $context): void
    {
        $this->configurationStructure[$metaKey] = $this->regulateConfigurationContext($metaKey, $context);
    }

    /**
     * > For internal Use
     *
     * Normalize the context that should be added to the config list
     */
    final public function regulateConfigurationContext(string $name, array $context): array
    {
        if(empty($context['field'])) {
            $context['field'] = TextField::new(self::FIELD_NAME);
        }

        if(!is_integer($context['mode'] ?? null)) {
            $context['mode'] = ModeEnum::READ->value | ModeEnum::WRITE->value;
        }

        if(empty($context['label'])) {
            $context['label'] = ucwords(preg_replace('/[._]/', " ", $name));
        }

        $context['field']->setLabel($context['label']);

        return $context;
    }

    /*
    public function removeConfigurationContext(string $name): void
    {
        if(array_key_exists($name, $this->configurationStructure)) {
            unset($this->configurationStructure[$name]);
            $this->configurationStructure = array_values($this->configurationStructure);
        }
    }
    */

    private function buildConfigurationStructure(): void
    {
        $this->addConfigurationContext('app.name', [
            'value' => 'User Synthetics',
            'field' => TextField::new(self::FIELD_NAME)
                ->setRequired(true)
                ->setFormTypeOption('constraints', new NotBlank())
            ,
        ]);

        $this->addConfigurationContext('app.logo', [
            'value' => 'http://ucscode.com/common/images/origin.png',
            'field' => ImageField::new(self::FIELD_NAME)
                ->setUploadDir(FilePathConstants::SYSTEM_IMAGE_UPLOAD_DIR)
                ->setBasePath(FilePathConstants::SYSTEM_IMAGE_BASE_PATH),
        ]);

        $this->addConfigurationContext('app.slogan', [
            'value' => 'Your premier destination for creating stunning and effective websites.',
            'field' => TextareaField::new(self::FIELD_NAME),
        ]);

        $this->addConfigurationContext('app.description', [
            'value' => 'Our comprehensive suite of services covers every aspect of website creation, from concept and design 
            to development and launch. We work closely with our clients to understand their goals, audience, and brand identity, 
            ensuring that every website we create reflects their vision and objectives.',
            'field' => TextareaField::new(self::FIELD_NAME),
        ]);

        $this->addConfigurationContext('office.email', [
            'value' => 'office@example.com',
            'field' => EmailField::new(self::FIELD_NAME),
        ]);

        $this->addConfigurationContext('office.phone', [
            'value' => '+1 212-555-0123',
            'field' => TelephoneField::new(self::FIELD_NAME),
        ]);

        $this->addConfigurationContext('office.address', [
            'value' => "123 Main Street \nAnytown, CA 12345 \nUnited States",
            'field' => TextareaField::new(self::FIELD_NAME),
        ]);

        $this->addConfigurationContext('test.key', [
            'value' => false,
            'field' => BooleanField::new(self::FIELD_NAME),
        ]);
    }
}
