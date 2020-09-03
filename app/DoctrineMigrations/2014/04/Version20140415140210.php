<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\AgreementTemplate;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140415140210 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $content = <<<HTML
<div class="banner-wrapper">
    <h1 class="title">Представляем вашему вниманию распрекрасный баннер</h1>
    <img src="{{ asset('bundles/metalproject/pic/media-banner.png') }}" alt="image description"/>
</div>
<div class="media-wrapper clearfix">
    <div class="banner-privileges float-left">
        <ol class="list">
            <li class="item">
                <div class="title">Всегда на виду</div>
                <p class="text">Функционально готовый сайт, наполненный тестовым содержанием. <a class="link"
                                                                                                 href="#">Прототип сайта</a> допускает наличие ошибок, выявление которых затруднительно до размещения реального
                    содержания Сайта. </p>
            </li>
            <li class="item">
                <div class="title">Большой размер</div>
                <p class="text">Первая итерация сайта, когда с 1С-Битрикс интегрированы шаблоны сайта,
                    <a class="link" href="#">настроена структура</a> сайта (разделы и подразделы) и
                    настроены системные страницы. </p>
            </li>
            <li class="item">
                <div class="title">Оплата за результат</div>
                <p class="text"><a class="link" href="#">Работа сотрудников</a> Заказчика осуществляется в стандартном административном
                    интерфейсе 1С-Битрикс без разработки для них дополнительных Интерфейсов</p>
            </li>
        </ol>
    </div>
</div>

<div class="button-block outline">
    <ul class="list">
        <li class="item">
            <a class="buy-btn button green-bg ie-radius" href="#">Купить немедленно</a>
            <a class="link" href="#">Требования к баннеру</a>
        </li>
    </ul>
</div>
HTML;

        $this->addSql('UPDATE agreement_template SET `content` = :content WHERE id = :id', array('content' => $content, 'id' => AgreementTemplate::MEDIA_RECLAME));

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
