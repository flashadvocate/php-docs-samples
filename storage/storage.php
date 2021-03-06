<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Cloud\Samples\Storage;

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$application = new Application();

// Create Bucket ACL command
$application->add(new Command('bucket-acl'))
    ->setDescription('Manage the ACL for Cloud Storage buckets.')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage ACL.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Add or filter by a user')
    ->addOption('role', null, InputOption::VALUE_REQUIRED, 'One of OWNER, READER, or WRITER', 'READER')
    ->addOption('create', null, InputOption::VALUE_NONE, 'Create an ACL for the supplied user')
    ->addOption('delete', null, InputOption::VALUE_NONE, 'Remove a user from the ACL')
    ->setCode(function ($input, $output) {
        $bucketName = $input->getArgument('bucket');
        $entity = $input->getOption('entity');
        $role = $input->getOption('role');
        if ($entity) {
            if ($input->getOption('create')) {
                add_bucket_acl($bucketName, $entity, $role);
            } elseif ($input->getOption('delete')) {
                delete_bucket_acl($bucketName, $entity);
            } else {
                get_bucket_acl_for_entity($bucketName, $entity);
            }
        } else {
            get_bucket_acl($bucketName);
        }
    });

// Create Bucket Default ACL command
$application->add(new Command('bucket-default-acl'))
    ->setDescription('Manage the default ACL for Cloud Storage buckets.')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage ACL.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Add or filter by a user')
    ->addOption('role', null, InputOption::VALUE_REQUIRED, 'One of OWNER, READER, or WRITER', 'READER')
    ->addOption('create', null, InputOption::VALUE_NONE, 'Create an ACL for the supplied user')
    ->addOption('delete', null, InputOption::VALUE_NONE, 'Remove a user from the ACL')
    ->setCode(function ($input, $output) {
        $bucketName = $input->getArgument('bucket');
        $entity = $input->getOption('entity');
        $role = $input->getOption('role');
        if ($entity) {
            if ($input->getOption('create')) {
                add_bucket_default_acl($bucketName, $entity, $role);
            } elseif ($input->getOption('delete')) {
                delete_bucket_default_acl($bucketName, $entity);
            } else {
                get_bucket_default_acl_for_entity($bucketName, $entity);
            }
        } else {
            get_bucket_default_acl($bucketName);
        }
    });

// Create Bucket Labels command
$application->add(new Command('bucket-labels'))
    ->setDescription('Manage Cloud Storage bucket labels')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage Bucket labels.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addArgument('label', InputArgument::OPTIONAL, 'The Cloud Storage label')
    ->addOption('value', null, InputOption::VALUE_REQUIRED, 'Set the value of the label')
    ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove the buckets label')
    ->setCode(function ($input, $output) {
        $bucketName = $input->getArgument('bucket');
        if ($label = $input->getArgument('label')) {
            if ($value = $input->getOption('value')) {
                add_bucket_label($bucketName, $label, $value);
            } elseif ($input->getOption('remove')) {
                remove_bucket_label($bucketName, $label);
            } else {
                throw new \Exception('You must provide --value or --remove '
                    . 'when including a label name.');
            }
        } else {
            get_bucket_labels($bucketName);
        }
    });

// Create Buckets command
$application->add(new Command('buckets'))
    ->setDescription('Manage Cloud Storage buckets')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage ACL.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::OPTIONAL, 'The Cloud Storage bucket name')
    ->addOption('create', null, InputOption::VALUE_NONE, 'Create the bucket')
    ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete the bucket')
    ->setCode(function ($input, $output) {
        if ($bucketName = $input->getArgument('bucket')) {
            if ($input->getOption('create')) {
                create_bucket($bucketName);
            } elseif ($input->getOption('delete')) {
                delete_bucket($bucketName);
            } else {
                throw new \Exception('Supply --create or --delete with bucket name');
            }
        } else {
            list_buckets();
        }
    });

// Create Encryption command
$application->add(new Command('encryption'))
    ->setDescription('Upload and download Cloud Storage objects with encryption')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage ACL.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::OPTIONAL, 'The Cloud Storage bucket name')
    ->addArgument('object', InputArgument::OPTIONAL, 'The Cloud Storage object name')
    ->addOption('upload-from', null, InputOption::VALUE_REQUIRED, 'Path to the file to upload')
    ->addOption('download-to', null, InputOption::VALUE_REQUIRED, 'Path to store the dowloaded file')
    ->addOption('key', null, InputOption::VALUE_REQUIRED, 'Supply your encryption key')
    ->addOption('rotate-key', null, InputOption::VALUE_REQUIRED, 'Supply a new encryption key')
    ->addOption('generate-key', null, InputOption::VALUE_NONE, 'Generates an encryption key')
    ->setCode(function ($input, $output) {
        if ($input->getOption('generate-key')) {
            generate_encryption_key();
        } else {
            $bucketName = $input->getArgument('bucket');
            $objectName = $input->getArgument('object');
            $encryptionKey = $input->getOption('key');
            if ($bucketName && $objectName) {
                if ($source = $input->getOption('upload-from')) {
                    upload_encrypted_object($bucketName, $objectName, $source, $encryptionKey);
                } elseif ($destination = $input->getOption('download-to')) {
                    download_encrypted_object($bucketName, $objectName, $destination, $encryptionKey);
                } elseif ($rotateKey = $input->getOption('rotate-key')) {
                    if (is_null($encryptionKey)) {
                        throw new \Exception('--key is required when using --rotate-key');
                    }
                    rotate_encryption_key($bucketName, $objectName, $encryptionKey, $rotateKey);
                } else {
                    throw new \Exception('Supply --rotate-key, --upload-from or --download-to');
                }
            } else {
                throw new \Exception('Supply a bucket and object OR --generate-key');
            }
        }
    });

$application->add(new Command('iam'))
    ->setDescription('Manage IAM for Storage')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Storage IAM policies.

<info>php %command.full_name% my-bucket</info>

<info>php %command.full_name% my-bucket --role my-role --add-member user/test@email.com</info>

<info>php %command.full_name% my-bucket --role my-role --remove-member user/test@email.com</info>

EOF
    )
    ->addArgument('bucket', InputArgument::REQUIRED, 'The bucket that you want to change IAM for. ')
    ->addOption('role', null, InputOption::VALUE_REQUIRED, 'The new role to add to a bucket. ')
    ->addOption('add-member', null, InputOption::VALUE_REQUIRED, 'The new member to add with the new role to the bucket. ')
    ->addOption('remove-member', null, InputOption::VALUE_REQUIRED, 'The member to remove from a role for a bucket. ')
    ->setCode(function ($input, $output) {
        $bucketName = $input->getArgument('bucket');
        $role = $input->getOption('role');
        $addMember = $input->getOption('add-member');
        $removeMember = $input->getOption('remove-member');
        if ($addMember) {
            if (!$role) {
                throw new InvalidArgumentException('Must provide role as an option.');
            }
            add_bucket_iam_member($bucketName, $role, $addMember);
        } elseif ($removeMember) {
            if (!$role) {
                throw new InvalidArgumentException('Must provide role as an option.');
            }
            remove_bucket_iam_member($bucketName, $role, $removeMember);
        } else {
            view_bucket_iam_members($bucketName);
        }
    });

$application->add(new Command('object-acl'))
    ->setDescription('Manage the ACL for Cloud Storage objects')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage ACL.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addArgument('object', InputArgument::REQUIRED, 'The Cloud Storage object name')
    ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Add or filter by a user')
    ->addOption('role', null, InputOption::VALUE_REQUIRED, 'One of OWNER, READER, or WRITER','READER')
    ->addOption('create', null, InputOption::VALUE_NONE, 'Create an ACL for the supplied user')
    ->addOption('delete', null, InputOption::VALUE_NONE, 'Remove a user from the ACL')
    ->setCode(function ($input, $output) {
        $bucketName = $input->getArgument('bucket');
        $entity = $input->getOption('entity');
        $role = $input->getOption('role');
        $objectName = $input->getArgument('object');
        if ($entity) {
            if ($input->getOption('create')) {
                add_object_acl($bucketName, $objectName, $entity, $role);
            } elseif ($input->getOption('delete')) {
                delete_object_acl($bucketName, $objectName, $entity);
            } else {
                get_object_acl_for_entity($bucketName, $objectName, $entity);
            }
        } else {
            get_object_acl($bucketName, $objectName);
        }
    });

$application->add(new Command('objects'))
    ->setDescription('Manage Cloud Storage objects')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage objects.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addArgument('object', InputArgument::OPTIONAL, 'The Cloud Storage object name')
    ->addOption('upload-from', null, InputOption::VALUE_REQUIRED, 'Path to the file to upload')
    ->addOption('download-to', null, InputOption::VALUE_REQUIRED, 'Path to store the dowloaded file')
    ->addOption('move-to', null, InputOption::VALUE_REQUIRED, 'new name for the object')
    ->addOption('copy-to', null, InputOption::VALUE_REQUIRED, 'copy path for the object')
    ->addOption('make-public', null, InputOption::VALUE_NONE, 'makes the supplied object public')
    ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete the bucket')
    ->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'List objects matching a prefix')
    ->setCode(function ($input, $output) {
        $bucketName = $input->getArgument('bucket');
        if ($objectName = $input->getArgument('object')) {
            if ($source = $input->getOption('upload-from')) {
                upload_object($bucketName, $objectName, $source);
            } elseif ($destination = $input->getOption('download-to')) {
                download_object($bucketName, $objectName, $destination);
            } elseif ($newObjectName = $input->getOption('move-to')) {
                move_object($bucketName, $objectName, $bucketName, $newObjectName);
            } elseif ($newObjectName = $input->getOption('copy-to')) {
                copy_object($bucketName, $objectName, $bucketName, $newObjectName);
            } elseif ($input->getOption('make-public')) {
                make_public($bucketName, $objectName);
            } elseif ($input->getOption('delete')) {
                delete_object($bucketName, $objectName);
            } else {
                object_metadata($bucketName, $objectName);
            }
        } else {
            if ($prefix = $input->getOption('prefix')) {
                list_objects_with_prefix($bucketName, $prefix);
            } else {
                list_objects($bucketName);
            }
        }
    });

$application->add(new Command('requester-pays'))
    ->setDescription('Manage Cloud Storage requester pays buckets.')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command manages Cloud Storage requester pays buckets.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('project', InputArgument::REQUIRED, 'Your billable Google Cloud Project ID')
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage requester pays bucket name')
    ->addArgument('object', InputArgument::OPTIONAL, 'The Cloud Storage requester pays object name')
    ->addArgument('download-to', null, InputArgument::OPTIONAL, 'Path to store the dowloaded file')
    ->addOption('enable', null, InputOption::VALUE_NONE, 'Enable requester pays on a Cloud Storage bucket')
    ->addOption('disable', null, InputOption::VALUE_NONE, 'Disable requester pays on a Cloud Storage bucket')
    ->addOption('check-status', null, InputOption::VALUE_NONE, 'Check requester pays status on a Cloud Storage bucekt')
    ->setCode(function ($input, $output) {
        $projectId = $input->getArgument('project');
        $bucketName = $input->getArgument('bucket');
        if ($objectName = $input->getArgument('object')) {
            if ($destination = $input->getArgument('download-to')) {
                download_file_requester_pays($projectId, $bucketName, $objectName, $destination);
            }
        } elseif ($input->getOption('enable')) {
            enable_requester_pays($projectId, $bucketName);
        } elseif ($input->getOption('disable')) {
            disable_requester_pays($projectId, $bucketName);
        } elseif ($input->getOption('check-status')) {
            get_requester_pays_status($projectId, $bucketName);
        }
    });

$application->add(new Command('enable-default-kms-key'))
    ->setDescription('Enable default KMS encryption for a bucket.')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command enables default KMS encryption for bucket.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('project', InputArgument::REQUIRED, 'Your billable Google Cloud Project ID')
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addArgument('kms-key-name', InputArgument::REQUIRED, 'KMS key ID to use as the default KMS key.')
    ->setCode(function ($input, $output) {
        $projectId = $input->getArgument('project');
        $bucketName = $input->getArgument('bucket');
        $kmsKeyName = $input->getArgument('kms-key-name');
        enable_default_kms_key($projectId, $bucketName, $kmsKeyName);
    });

$application->add(new Command('upload-with-kms-key'))
    ->setDescription('Upload a file using KMS encryption.')
    ->setHelp(<<<EOF
The <info>%command.name%</info> command uploads a file using KMS encryption.

<info>php %command.full_name% --help</info>

EOF
    )
    ->addArgument('project', InputArgument::REQUIRED, 'Your billable Google Cloud Project ID')
    ->addArgument('bucket', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addArgument('object', InputArgument::REQUIRED, 'The Cloud Storage bucket name')
    ->addArgument('upload-from', InputArgument::REQUIRED, 'Path to the file to upload')
    ->addArgument('kms-key-name', InputArgument::REQUIRED, 'KMS key ID used to encrypt objects server side.')
    ->setCode(function ($input, $output) {
        $projectId = $input->getArgument('project');
        $bucketName = $input->getArgument('bucket');
        $objectName = $input->getArgument('object');
        $uploadFrom = $input->getArgument('upload-from');
        $kmsKeyName = $input->getArgument('kms-key-name');
        upload_with_kms_key($projectId, $bucketName, $objectName, $uploadFrom, $kmsKeyName);
    });

// for testing
if (getenv('PHPUNIT_TESTS') === '1') {
    return $application;
}

$application->run();
