<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160119132100 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ($this->container->getParameter('project.family') != 'metalloprokat') {
            $this->addSql(
                "DROP TABLE IF EXISTS
                    Auth_Settings,
                    Auth_UserRelation,
                    Banner_StatsReferer,
                    Cache_Settings,
                    Calendar_Settings,
                    Captchas,
                    Catalogue,
                    Category_par_list,
                    Class,
                    Classificator,
                    Classificator_BannerSize,
                    Classificator_BannerType,
                    Classificator_BlogCommentsAccess,
                    Classificator_BlogMessagesAccess,
                    Classificator_BlogPermissionGroup,
                    Classificator_Currency,
                    Classificator_Gost_Mark,
                    Classificator_Import_Type,
                    Classificator_LME,
                    Classificator_Manufacturer,
                    Classificator_NeedType,
                    Classificator_Pts,
                    Classificator_RUS,
                    Classificator_Region_new,
                    Classificator_Regions_new,
                    Classificator_Sex,
                    Classificator_StatusMod,
                    Classificator_StatusModEdit,
                    Classificator_SteelType,
                    Classificator_TypeOfData,
                    Classificator_TypeOfEdit,
                    Classificator_TypeOfModeration,
                    Classificator_TypeOfRight,
                    Classificator_UserGroup,
                    Classificator_comp_type,
                    Classificator_occupation,
                    Classificator_payMethod,
                    Field,
                    LeftMenuRSPM,
                    LeftMenuTrade,
                    Links_Settings,
                    Message1,
                    Message143,
                    Message187,
                    Message187_Section,
                    Message195,
                    Message2,
                    Message3,
                    Message38,
                    Message61,
                    Message64,
                    Message73_Section,
                    Message75_old,
                    Message75_old_log,
                    Message83,
                    Message96,
                    Message96_autoupdate,
                    Module,
                    Objava_Stat,
                    Objava_Stat_Total,
                    Patch,
                    Permission,
                    PermissionGroup,
                    SQLQueries,
                    Session,
                    Settings,
                    Sub_Class,
                    Subdivision,
                    Subscriber,
                    SystemMessage,
                    System_Table,
                    Tags_Data,
                    Tags_Message,
                    Tags_Weight,
                    Template,
                    User_Group,
                    User_OpenID,
                    User_dead,
                    objava,
                    sprosCity,
                    tradeCategory,
                    trade_autoupd,
                    zaiv_pokup
                "
            );
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
