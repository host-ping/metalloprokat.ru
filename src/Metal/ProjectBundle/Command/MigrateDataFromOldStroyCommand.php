<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Metal\ContentBundle\Entity\Category;
use Metal\ProjectBundle\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

class MigrateDataFromOldStroyCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $oldStroyConn;

    /**
     * @var EntityManagerInterface
     */
    private $oldStroyEm;

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    private $tags = array();

    private $categoriesIdsToMove = array(83 => 83, 84 => 84, 85 => 85);

    protected function configure()
    {
        $this->setName('metal:project:migrate-from-old-stroy');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);

        $this->conn = $em->getConnection();
        /* @var $conn Connection */
        $this->conn->getConfiguration()->setSQLLogger(null);

        $parametersOldStroy = $this->getContainer()->getParameter('database_old_stroy');
        $this->oldStroyConn = $this->getContainer()->get('doctrine.dbal.connection_factory')->createConnection($parametersOldStroy);
        $this->oldStroyEm = EntityManager::create($this->oldStroyConn, $em->getConfiguration());

//        $this->conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0');

        $this->moveCategories();
        $this->moveUsers();
        $this->moveUserAvatar();
        $this->moveAlbums();
        $this->moveUserImages('/188/372/', 1157);
//        $this->moveUserImages('/18/10/', 711);
        $this->moveTags();
        $this->moveTopics();
        $this->moveQuestion();
        $this->moveAnswers();
        $this->moveUsersCategories();

//        $this->conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
        $this->output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    // 1084_2391.jpg формат картинок для топиков.1084 - Field, 2391 id топика

    private function moveUsers()
    {
        $oldUsers = $this->oldStroyConn->fetchAll('
            SELECT User_ID, Password, PermissionGroup_ID, Checked,
                Created, LastUpdated, Email, Confirmed, RegistrationCode, Keyword, ForumName, ForumAvatar, FullName,
                Catalogue_ID, Language, ForumSignature, InsideAdminAccess, ICQ, Auth_Hash, newEmail,
                Login, Profession, About, Created, LastUpdated
            FROM User
            ');

        foreach ($oldUsers as $oldUserRow) {
            $oldUserRow['Icq'] = $oldUserRow['ICQ'];
            $oldUserRow['Job'] = $oldUserRow['Profession'];
            $oldUserRow['UserIfno'] = $oldUserRow['About'];
            $oldUserRow['ForumName'] = $oldUserRow['Login'];
            unset($oldUserRow['ICQ']);
            unset($oldUserRow['Profession']);
            unset($oldUserRow['About']);

            $oldUserRow['ForumName'] = $oldUserRow['ForumName'] ? $oldUserRow['ForumName'] : '';

            $this->conn->insert('User', $oldUserRow);
            $this->output->writeln(sprintf(' Insert User id "%d"', $oldUserRow['User_ID']));

            $subscriberData = array(
                'user_id' => $oldUserRow['User_ID'],
                'Email' => $oldUserRow['Email'],
                'subscribed_for_demands' => false,
                'Created' => $oldUserRow['Created'],
            );

            $this->conn->insert('UserSend', $subscriberData);
            $this->output->writeln('Insert Subscriber From User');

        }

        $this->conn->executeUpdate("UPDATE User SET additional_role_id = 5 WHERE PermissionGroup_ID = 1");
        $this->conn->executeUpdate("UPDATE User SET additional_role_id = 9 WHERE Email = 'koc-dp@yandex.ru' OR Email = 'tashka7@gmail.com'");
        $this->conn->executeUpdate("UPDATE User SET Password = PASSWORD('ololoev777') WHERE Email = 'koc-dp@yandex.ru'");

        $synchronizeUserCountersCommand = $this->getApplication()->find('metal:users:synchronize-counters');
        $synchronizeUserCountersCommand->run($this->input, $this->output);
    }

    private function moveUserAvatar()
    {
        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var  $imageHelper ImageHelper */

        $avatars = $this->oldStroyConn->fetchAll('
            SELECT *
            FROM Filetable WHERE Field_ID = 411
        ');

        $avatarsDir = $this->getContainer()->getParameter('original_user_avatar_dir');

        $guesser = MimeTypeGuesser::getInstance();
        $extensionGuesser = ExtensionGuesser::getInstance();

        foreach ($avatars as $i => $avatar) {
            unset($avatar['Download']);

            // переопределяем mime и генерим новое имя
            $extension = $imageHelper->getExtensionByMimeType($avatar['File_Type']);
            $avatarName = '1121_'.$avatar['Message_ID'].'.'.$extension;
            $originalFileWithExtension = $avatarsDir.'/'.$avatarName;
            if (!file_exists($originalFileWithExtension) && 'jpeg' === $extension) {
                $avatarName = '1121_'.$avatar['Message_ID'].'.'.'jpg';
                $originalFileWithExtension = $avatarsDir.'/'.$avatarName;
            }

            if (file_exists($originalFileWithExtension)) {
                $mime = $guesser->guess($originalFileWithExtension);
                $name = sha1(microtime(true).mt_rand(0, 9999)).'.'.$extensionGuesser->guess($mime);

                $avatar['Virt_Name'] = $name;
                $avatar['File_Type'] = $mime;
                $avatar['File_Path'] = substr($name, 0, 2);

                $this->output->writeln(sprintf('Move avatar image %d"', $avatar['ID']));

                // imitation $productImage->getWebPath()
                $webPath = '/users/'.substr($name, 0, 2).'/'.$name;
                $newFilePath = $this->getContainer()->getParameter('upload_dir').$webPath;

                if (!is_dir($dir = dirname($newFilePath))) {
                    mkdir($dir, 0777, true);
                }

                copy($originalFileWithExtension, $newFilePath);
                $this->conn->insert('Filetable', $avatar);

                $this->output->writeln(sprintf('Move avatar file - %d', $avatar['ID']));

            } else {
                $this->output->writeln(sprintf('Not moved avatar image - %d"', $avatar['ID']));
            }

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert avatar rows - %d', $i));
            }
        }
    }

    private function moveAlbums()
    {
        $albums = $this->oldStroyConn->fetchAll('
            SELECT a.Message_ID, a.User_ID, a.Priority, a.Checked, a.Created, a.LastUpdated, a.albumName, a.Rubric
            FROM Message89 AS a
            JOIN Subdivision AS c
             ON a.Rubric =c.Subdivision_ID AND (c.Subdivision_ID IN (:categories_ids) OR c.Parent_Sub_ID IN (:categories_ids))
        ', array('categories_ids' => $this->categoriesIdsToMove), array('categories_ids' => Connection::PARAM_INT_ARRAY));

        foreach ($albums as $i => $album) {
            $albumData = array(
                'id' => $album['Message_ID'],
                'user_id' => $album['User_ID'],
                'category_id' => $album['Rubric'],
                'priority' => $album['Priority'],
                'checked' => $album['Checked'],
                'created_at' => $album['Created'],
                'updated_at' => $album['LastUpdated'],
                'title' => $album['albumName'],
            );

            $this->conn->insert('content_image_album', $albumData);

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert image rows - %d', $i));
            }
        }
    }

    private function moveUserImages($callbackPath, $fieldId)
    {
        $images = $this->oldStroyConn->fetchAll('
            SELECT i.Message_ID, i.User_ID, i.Priority, i.Checked, i.Created, i.LastUpdated, i.photoUrl, i.album, f.Virt_Name, f.File_Type,
            i.photoName, f.Real_Name
            FROM Message90 AS i
            JOIN Filetable AS f
             ON i.Message_ID =f.Message_ID
             AND f.Field_ID = :field
             AND i.Subdivision_ID = 188
             AND i.Sub_Class_ID = 372
             AND  EXISTS (SELECT 1 FROM Message89 AS a WHERE i.album = a.Message_ID)
        ', array('field' => $fieldId));

        $imagesDir = $this->getContainer()->getParameter('original_image_dir').$callbackPath;
        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var  $imageHelper ImageHelper */

        $guesser = MimeTypeGuesser::getInstance();

        foreach ($images as $i => $image) {
            $imageData = array(
                'id' => $image['Message_ID'],
                'user_id' => $image['User_ID'],
                'album_id' => $image['album'],
                'priority' => $image['Priority'],
                'checked' => $image['Checked'],
                'created_at' => $image['Created'],
                'updated_at' => $image['LastUpdated'],
                'mime_type' => $image['File_Type'],
//                'file_size' => $image['Text'],
                'file_name' => $image['Virt_Name'],
                'file_original_name' => $image['photoName'] ? $image['photoName'] : $image['Real_Name']
            );

            $imageExtension = explode(':', $image['photoUrl']);
            $extension = 'gif';
            if ($image['File_Type']) {
                $extension = $imageHelper->getExtensionByMimeType($image['File_Type']);
            } elseif ($imageExtension[1]) {
                $extension = $imageHelper->getExtensionByMimeType($imageExtension[1]);
                $imageData['mime_type'] = $imageExtension[1];
            }

            $originalFile = $imagesDir.$image['Virt_Name'];
            $originalFileWithExtension = $imagesDir.$image['Virt_Name'].'.'.$extension;

            if (file_exists($originalFile)) {
                $imageData['file_size'] = filesize($originalFile);

                $this->output->writeln(sprintf('Move image %d"', $image['Message_ID']));

                // imitation $productImage->getWebPath()
                $webPath = '/netcat_files'.$callbackPath.'h_'.$image['Virt_Name'];
                $newFilePath = $this->getContainer()->getParameter('web_dir').$webPath;

                if (!is_dir($dir = dirname($newFilePath))) {
                    mkdir($dir, 0777, true);
                }

                copy($originalFile, $newFilePath);
                $this->conn->insert('content_image', $imageData);

                $this->output->writeln(sprintf('Move image file - %d', $image['Message_ID']));

            } elseif (file_exists($originalFileWithExtension)) {

                $mime = $guesser->guess($originalFileWithExtension);
                $imageData['mime_type'] = $mime;
                $imageData['file_size'] = filesize($originalFileWithExtension);

                // imitation $productImage->getWebPath()
                $webPath = '/netcat_files'.$callbackPath.'h_'.$image['Virt_Name'];
                $newFilePath = $this->getContainer()->getParameter('web_dir').$webPath;

                if (!is_dir($dir = dirname($newFilePath))) {
                    mkdir($dir, 0777, true);
                }

                copy($originalFileWithExtension, $newFilePath);
                $this->conn->insert('content_image', $imageData);

                $this->output->writeln(sprintf('Move image file - %d', $image['Message_ID']));
            } else {
                $this->output->writeln(sprintf('Not moved image - %d"', $image['Message_ID']));
            }

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert image rows - %d', $i));
            }
        }
    }

    private function moveCategories()
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */

        $categoriesIdsToMove = $this->categoriesIdsToMove;
        //FIXME: Created, LastUpdated renamed , change this ?
        $categories = $this->oldStroyConn->fetchAll('
            SELECT Subdivision_ID, Parent_Sub_ID, EnglishName, Hidden_URL, Subdivision_Name, Created, LastUpdated, subTitle, Description, Keywords FROM Subdivision
            WHERE (Subdivision_ID IN (:categories_ids) OR Parent_Sub_ID IN (:categories_ids))
            ORDER BY Subdivision_ID ASC',
            array('categories_ids' => $categoriesIdsToMove), array('categories_ids' => Connection::PARAM_INT_ARRAY)
        );

        $reflectionProperty = new \ReflectionProperty(Category::class, 'id');
        $reflectionProperty->setAccessible(true);
        $metadata = $em->getClassMetadata(Category::class);
        $metadata->setIdGeneratorType($metadata::GENERATOR_TYPE_NONE);

        foreach ($categories as $oldCategory) {
            $category = new Category();
            $reflectionProperty->setValue($category, $oldCategory['Subdivision_ID']);
            $category->setSlug($oldCategory['EnglishName']);
            $category->setSlugCombined($oldCategory['Hidden_URL']);
            $category->setTitle($oldCategory['Subdivision_Name']);
            $category->setCreatedAt(new \DateTime($oldCategory['Created']));
            $category->setUpdatedAt(new \DateTime($oldCategory['LastUpdated']));
            $category->getMetadata()->setDescription($oldCategory['Description']);
            $category->getMetadata()->setKeywords($oldCategory['Keywords']);
            $category->getMetadata()->setTitle($oldCategory['subTitle']);
            if (in_array($oldCategory['Parent_Sub_ID'], array_keys($categoriesIdsToMove))) {
                $category->setParent($categoriesIdsToMove[$oldCategory['Parent_Sub_ID']]);
                $this->output->writeln(sprintf(' Set Parent "%d" to category id "%d"',$oldCategory['Parent_Sub_ID'], $oldCategory['Subdivision_ID']));
            }

            $em->persist($category);
            $em->flush();

            if (in_array($oldCategory['Subdivision_ID'], $categoriesIdsToMove)) {
                $categoriesIdsToMove[$oldCategory['Subdivision_ID']] = $category;
            }

            $this->output->writeln(sprintf(' Insert Category id "%d"', $oldCategory['Subdivision_ID']));
        }

//        $populateMenuItemsCommand = $this->getApplication()->find('metal:categories:populate-menu-items');
//        $populateMenuItemsCommand->run($this->input, $this->output);

        $refreshCategoriesCommand = $this->getApplication()->find('metal:categories:refresh');
        $refreshCategoriesCommand->run($this->input, $this->output);

        $compileUrlRewritesCommand = $this->getApplication()->find('metal:project:compile-url-rewrites');
        $compileUrlRewritesCommand->run(
            new ArrayInput(
                array(
                    'command' => 'metal:project:compile-url-rewrites',
                    '--truncate' => true
                )),
            $this->output
        );
    }

    private function moveTags()
    {
        $tags = $this->oldStroyConn->fetchAll('SELECT Message_ID, Caption, Status, Created, LastUpdated FROM Message75');
        foreach ($tags as $i => $tag) {
            $this->tags[] = $tag['Message_ID'];
            $tagData = array(
                'id' => $tag['Message_ID'],
                'title' => $tag['Caption'],
                'status_type_id' => $tag['Status'],
                'created_at' => $tag['Created'],
                'updated_at' => $tag['LastUpdated'],
            );

            $this->conn->insert('content_tag', $tagData);

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert content_tag rows - %d', $i));
            }
        }
    }

    private function moveTopics()
    {
        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var  $imageHelper ImageHelper */
        $imagesDir = $this->getContainer()->getParameter('original_image_dir');

        $topics = $this->oldStroyConn->fetchAll('
            SELECT Message_ID, User_ID, RubricMain, RubricSecond, Caption, Created, LastUpdated,
                ModerateStatus, Text, Teaser, pageTitle, notify, Tags, Subject, Image
            FROM Message71
        ');

        $guesser = MimeTypeGuesser::getInstance();
        $extensionGuesser = ExtensionGuesser::getInstance();
        $urls = array();
        foreach ($topics as $i => $topic) {

            $topicData = array(
                'id' => $topic['Message_ID'],
                'user_id' => $topic['User_ID'],
                'category_id' => $topic['RubricMain'],
                'category_secondary_id' => $topic['RubricSecond'] ? $topic['RubricSecond'] : null,
                'title' => $topic['Caption'],
                'created_at' => $topic['Created'],
                'updated_at' => $topic['LastUpdated'],
                'status_type_id' => $topic['ModerateStatus'],
                'description' => $topic['Text'],
                'short_description' => $topic['Teaser'],
                'page_title' => $topic['pageTitle'],
                'notify' => $topic['notify'],
                'subject_type_id' => $topic['Subject'],
                'entry_type' => 1
            );

            preg_match_all('/<img[^>]+src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $topic['Text'], $matches);
            $urls[$topic['Message_ID']] = $matches[2];

            if (null !== $topic['Image'] && $topic['Image'] !== '') {
                $imageData = explode(':', $topic['Image']);
                $extension = isset($imageData[1]) ? $imageHelper->getExtensionByMimeType($imageData[1]) : 'gif';

                $imageName = '1084_'.$topic['Message_ID'].'.'.$extension;
                $originalFileWithExtension = $imagesDir.'/'.$imageName;
                if (!file_exists($originalFileWithExtension) && 'jpeg' === $extension) {
                    $imageName = '1084_'.$topic['Message_ID'].'.'.'jpg';
                    $originalFileWithExtension = $imagesDir.'/'.$imageName;
                }

                if (file_exists($originalFileWithExtension)) {
                    $this->output->writeln(sprintf('Move topic image %d"', $topic['Message_ID']));

                    $mime = $guesser->guess($originalFileWithExtension);
                    $name = sha1(microtime(true).mt_rand(0, 9999)).'.'.$extensionGuesser->guess($mime);

                    $topicData['mime_type'] = $mime;
                    $topicData['file_size'] = filesize($originalFileWithExtension);
                    $topicData['file_original_name'] = $imageData[0];
                    $topicData['file_name'] = $name;

                    // imitation $productImage->getWebPath()
                    $webPath = '/topics/'.substr($name, 0, 2).'/'.$name;
                    $newFilePath = $this->getContainer()->getParameter('upload_dir').$webPath;

                    if (!is_dir($dir = dirname($newFilePath))) {
                        mkdir($dir, 0777, true);
                    }

                    copy($originalFileWithExtension, $newFilePath);
                } else {
                    $this->output->writeln(sprintf('Not move image topic - %d"', $topic['Message_ID']));
                }
            }

            $this->conn->insert('content_entry', $topicData);

            $contentEntryId = $this->conn->fetchColumn('SELECT content_entry_id FROM content_entry WHERE id = :topic_id AND entry_type = 1',
                array('topic_id' => $topic['Message_ID'])
            );

            if ($topic['Tags'] !== "") {
                $topicTags = array_unique(array_map('trim', explode(',', $topic['Tags'])));
                $matchTags = array_intersect($this->tags, $topicTags);
                foreach ($matchTags as $topicTag) {
                    $this->conn->insert('content_entry_tag', array(
                        'content_entry_id' => $contentEntryId,
                        'tag_id' => (int)$topicTag,
                        'created_at' => $topic['Created']
                    ));
                }
            }

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert content_topic rows - %d', $i));
            }
        }
        file_put_contents($this->getContainer()->getParameter('kernel.logs_dir').'/images_path.txt', print_r(compact('urls'), true), FILE_APPEND | LOCK_EX);
    }

// 1084_2391.jpg формат картинок для топиков.1084 - Field, 2391 id топика

    private function moveQuestion()
    {
        $questions = $this->oldStroyConn->fetchAll('
            SELECT Message_ID, User_ID, RubricMain, RubricSecond, Caption, Created, LastUpdated,
                ModerateStatus, Text, Teaser, pageTitle, notify, Tags, usrName, usrEmail, Subject
            FROM Message72
        ');
        $urls = array();
        foreach ($questions as $i => $question) {

            $questionData = array(
                'id' => $question['Message_ID'],
                'user_id' => $question['User_ID'] ? $question['User_ID'] : null,
                'category_id' => $question['RubricMain'],
                'category_secondary_id' => $question['RubricSecond'] ? $question['RubricSecond'] : null,
                'title' => $question['Caption'],
                'created_at' => $question['Created'],
                'updated_at' => $question['LastUpdated'],
                'status_type_id' => $question['ModerateStatus'],
                'description' => $question['Text'],
                'short_description' => $question['Teaser'],
                'page_title' => $question['pageTitle'],
                'notify' => $question['notify'],
                'email' => $question['usrEmail'],
                'name' => $question['usrName'],
                'subject_type_id' => $question['Subject'],
                'entry_type' => 2
            );
            preg_match_all('/<img[^>]+src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $question['Text'], $matches);
            $urls[$question['Message_ID']] = $matches[2];
            $this->conn->insert('content_entry', $questionData);

            if ( $question['Tags'] !== "") {
                $questionTags = array_unique(array_map('trim', explode(',', $question['Tags'])));
                $matchTags = array_intersect($this->tags, $questionTags);
                foreach ($matchTags as $questionTag) {
                    $contentEntryId = $this->conn->fetchColumn('SELECT content_entry_id FROM content_entry WHERE id = :question_id AND entry_type = 2',
                        array('question_id' => $question['Message_ID'])
                    );

                    $this->conn->insert('content_entry_tag', array(
                        'content_entry_id' => $contentEntryId,
                        'tag_id' => (int)$questionTag,
                        'created_at' => $question['Created']
                    ));
                }
            }

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert content_question rows - %d', $i));
            }
        }
        file_put_contents($this->getContainer()->getParameter('kernel.logs_dir').'/images_question_path.txt', print_r(compact('urls'), true), FILE_APPEND | LOCK_EX);
    }

    private function moveAnswers()
    {
        // 6742 - id дочернего ответа, у которого удалили родителя
        // 2299 - id пользователя, которого нет
        $answers = $this->oldStroyConn->fetchAll('
            SELECT Message_ID, User_ID, Created, LastUpdated,
                 Text, notify, usrName, usrEmail, TableID, PubID, ParentID, ModerateStatus
            FROM Message76 WHERE Message_ID != 6742 AND User_ID != 2299 ORDER BY Message_ID ASC
        ');

        foreach ($answers as $i => $answer) {

            $answerData = array(
                'id' => $answer['Message_ID'],
                'user_id' => $answer['User_ID'] ? $answer['User_ID'] : null,
                'created_at' => $answer['Created'],
                'updated_at' => $answer['LastUpdated'],
                'description' => trim(stripslashes(preg_replace('#<br[ /]*?>#i', '', $answer['Text']))),
                'notify' => $answer['notify'],
                'email' => $answer['usrEmail'],
                'name' => $answer['usrName'],
                'parent_id' => $answer['ParentID'] ? $answer['ParentID'] : null,
                'status_type_id' => $answer['ModerateStatus']
            );

            // see comment in class Answer
            $answerData['content_entry_id'] = null;
            if ($answer['TableID'] == 71) {
                $contentEntryId = $this->conn->fetchColumn('SELECT content_entry_id FROM content_entry WHERE id = :topic_id AND entry_type = 1',
                    array('topic_id' => $answer['PubID'])
                );
                $answerData['content_entry_id'] = $contentEntryId;
            } elseif ($answer['TableID'] == 72) {
                $contentEntryId = $this->conn->fetchColumn('SELECT content_entry_id FROM content_entry WHERE id = :question_id AND entry_type = 2',
                    array('question_id' => $answer['PubID'])
                );
                $answerData['content_entry_id'] = $contentEntryId;
            }

            if ($answerData['content_entry_id']) {
                $this->conn->insert('content_comment', $answerData);
            }

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert content_answer rows - %d', $i));
            }
        }
    }

    private function moveUsersCategories()
    {
        $usersCategories = $this->oldStroyConn->fetchAll('SELECT Message_ID, user_link, sub_link, Created, LastUpdated FROM Message91');
        foreach ($usersCategories as $i => $userCategory) {
            $this->conn->executeQuery('
                INSERT IGNORE INTO content_user_category
                (id, user_id, category_id, created_at, updated_at)
                VALUES (:id, :user_id, :category_id, :created_at, :updated_at)
                ',
                array(
                    'id' => $userCategory['Message_ID'],
                    'user_id' => $userCategory['user_link'],
                    'category_id' => $userCategory['sub_link'],
                    'created_at' => $userCategory['Created'],
                    'updated_at' => $userCategory['LastUpdated'],
                )
            );

            if ($i % 50 == 0) {
                $this->output->writeln(sprintf('Insert content_user_category rows - %d', $i));
            }
        }
    }
// старая таблица Subdivision чего не хватает
//Description - description страницы

// старая таблица User чего не хватает
//`HideName` tinyint(4) NOT NULL DEFAULT '1',
//`Photo` char(255) DEFAULT NULL,
//`Rating` int(11) NOT NULL,
//`MsgCount` int(11) NOT NULL,
//`MsgLast` datetime DEFAULT NULL,
//`UserType` enum('normal','pseudo') NOT NULL DEFAULT 'normal',
//`occupation` int(11) DEFAULT NULL,
//`newPassword` char(255) DEFAULT NULL,
//`passHash` char(255) DEFAULT NULL,
//`ModerateStatus` int(11) NOT NULL DEFAULT '2',
}
