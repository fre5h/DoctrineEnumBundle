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
use Doctrine\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Fresh\DoctrineEnumBundle\Exception\RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * EnumDropCommentCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'doctrine:enum:drop-comment', description: 'Drop comment in DB for the column of registered ENUM type')]
final class EnumDropCommentCommand extends Command
{
    private EntityManagerInterface $em;

    /** @var string[] */
    private array $registeredEnumTypes = [];

    /** @var string|mixed */
    private mixed $enumType;

    /**
     * @param ManagerRegistry                      $registry
     * @param array<string, array<string, string>> $registeredTypes
     * @param string|null                          $name
     *
     * @throws EnumTypeIsRegisteredButClassDoesNotExistException
     */
    public function __construct(private readonly ManagerRegistry $registry, array $registeredTypes, ?string $name = null)
    {
        parent::__construct($name);

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

            // Filter only ENUM types
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
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('enumType', InputArgument::REQUIRED, 'Registered ENUM type'),
                    new InputOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to drop comment in DB for the column of registered ENUM type:

<info>%command.full_name%</info> <comment>CustomType</comment>

You can also set different name of <fg=cyan>entity manager</>, if you have more than one in your project:

<info>%command.full_name%</info> <comment>CustomType</comment> --em=<fg=cyan>custom</>

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

        $this->enumType = $input->getArgument('enumType');

        $emName = null;
        $emOption = $input->getOption('em');
        if (\is_string($emOption)) {
            $emName = $emOption;
        }

        $em = $this->registry->getManager($emName);
        if ($em instanceof EntityManagerInterface) {
            $this->em = $em;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if (!\is_string($this->enumType)) {
                throw new InvalidArgumentException('Argument "enumType" is not a string.', 1);
            }

            if (!isset($this->registeredEnumTypes[$this->enumType])) {
                throw new InvalidArgumentException('Argument "enumType" is not a registered ENUM type.', 2);
            }

            $connection = $this->em->getConnection();

            $platform = $connection->getDatabasePlatform();
            if (!$platform instanceof AbstractPlatform) {
                throw new RuntimeException('Missing database platform for connection.', 3);
            }

            $io->title(\sprintf('Dropping comments for <info>%s</info> type...', $this->enumType));

            /** @var \Doctrine\ORM\Mapping\ClassMetadata<object>[] $allMetadata */
            $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();

            if (!empty($allMetadata)) {
                $count = 0;

                foreach ($allMetadata as $metadata) {
                    $entityName = $metadata->getName();
                    if (!empty($metadata->getSchemaName())) {
                        $tableName = $metadata->getSchemaName().'.'.$metadata->getTableName();
                    } else {
                        $tableName = $metadata->getTableName();
                    }

                    foreach ($metadata->getFieldNames() as $fieldName) {
                        if ($metadata->getTypeOfField($fieldName) === $this->enumType) {
                            /** @var array{columnName: string} $fieldMappingDetails */
                            $fieldMappingDetails = $metadata->getFieldMapping($fieldName);
                            $sql = $platform->getCommentOnColumnSQL($tableName, $fieldMappingDetails['columnName'], null);
                            $connection->executeQuery($sql);

                            $io->text(\sprintf(' * %s::$%s   <info>Dropped ✔</info>', $entityName, $fieldName));

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
            $code = $e->getCode();

            return \is_int($code) ? $code : self::FAILURE;
        }

        return self::SUCCESS;
    }
}
