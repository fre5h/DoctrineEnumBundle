<?php
/*
 * This file is part of the FreshDoctrineEnumBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Command;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Fresh\DoctrineEnumBundle\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * EnumDropCommentCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumDropCommentCommand extends Command
{
    protected static $defaultName = 'doctrine:enum:drop-comment';

    /** @var EntityManagerInterface */
    private $em;

    /** @var string[] */
    private $registeredEnumTypes = [];

    /** @var string */
    private $enumType;

    /**
     * @param EntityManagerInterface $em
     * @param mixed[]                $registeredTypes
     * @param string|null            $name
     */
    public function __construct(EntityManagerInterface $em, array $registeredTypes, ?string $name = null)
    {
        parent::__construct($name);

        $this->em = $em;

        foreach ($registeredTypes as $type => $details) {
            $registeredEnumTypeFQCN = $details['class'];

            if (!\class_exists($registeredEnumTypeFQCN)) {
                $exceptionMessage = \sprintf(
                    'ENUM type "%s" is registered as "%s", but that class does not exist',
                    $type,
                    $registeredEnumTypeFQCN
                );

                throw new EnumTypeIsRegisteredButClassDoesNotExistException($exceptionMessage);
            }

            if (\is_subclass_of($registeredEnumTypeFQCN, AbstractEnumType::class)) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Drop comment in DB for the column of registered ENUM type')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('enumType', InputArgument::REQUIRED, 'Registered ENUM type'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to drop comment in DB for the column of registered ENUM type:

<info>%command.full_name%</info> <comment>CustomType</comment>

Read more at https://github.com/fre5h/DoctrineEnumBundle/blob/main/Resources/docs/hook_for_doctrine_migrations.md
HELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $enumType = $input->getArgument('enumType');

        // @todo Allow to select entity manager

        if (!\is_string($enumType)) {
            throw new InvalidArgumentException('Argument "enumType" is not a string.');
        }

        try {
            if (!isset($this->registeredEnumTypes[$enumType])) {
                throw new InvalidArgumentException('Argument "enumType" is not a registered ENUM type.');
            }

            $this->enumType = $enumType;
        } catch (\Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->title(\sprintf('Dropping comments for <info>%s</info> type...', $this->enumType));

            $connection = $this->em->getConnection();

            $platform = $connection->getDatabasePlatform();
            if (!$platform instanceof AbstractPlatform) {
                throw new RuntimeException('Missing database platform for connection');
            }

            /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $allMetadata */
            $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();

            if (!empty($allMetadata)) {
                $count = 0;

                foreach ($allMetadata as $entityMetadata) {
                    $entityName = $entityMetadata->getName();
                    $tableName = $entityMetadata->getTableName();

                    foreach ($entityMetadata->getFieldNames() as $fieldName) {
                        if ($entityMetadata->getTypeOfField($fieldName) === $this->enumType) {
                            $fieldMappingDetails = $entityMetadata->getFieldMapping($fieldName);

                            $sql = $platform->getCommentOnColumnSQL($tableName, $fieldMappingDetails['columnName'], null);
                            $connection->executeQuery($sql);

                            $io->text(\sprintf(' * %s::$%s   <info>Dropped</info>', $entityName, $fieldName));

                            ++$count;
                        }
                    }
                }

                $io->newLine();
                $io->text(\sprintf('<info>TOTAL</info>: %d', $count));
                $io->success('DONE');
            } else {
                $io->success('NO METADATA FOUND');
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return 0;
    }
}
