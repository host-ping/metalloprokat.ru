<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\AgreementTemplate;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140415155442 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $contactContent = <<<HTML
<div class="contact-wrapper clearfix">
    <div class="contact-left float-left">
        <p class="big-phone">+7 495 984-06-52</p>
        <ul class="contacts clearfix">
            <li class="item first float-left">
                <div class="head-title">
                    <strong>Наш адрес</strong>
                </div>
                <p>Москва, Большая Никитская, 22/2</p>
            </li>
            <li class="item float-left">
                <div class="head-title">
                    <strong>Адрес для бухгалтерии</strong>
                </div>
                <p>123308, г.Москва, ОПС № 308, а/я 29</p>
            </li>
            <li class="item float-left">
                <div class="head-title">
                    <strong>Электронная почта</strong>
                </div>
                <a class="mail-link" href="mailto:info@metalloprokat.ru">info@metalloprokat.ru</a>
            </li>
        </ul>
        <div class="btn-wrapper">
            <a class="js-popup-opener send-button button red-bg ie-radius ie-radius" href="#" data-popup="#feedback-popup">Написать письмо</a>
        </div>
    </div>
    <div class="map-wrapper is-bordered float-right">
        <div class="heading clearfix">
            <a class="link float-right" href="#">Открыть в Яндекс.Картах</a>
        </div>
        <div class="map">
            <span class="red-point map-point" style="top: 50px; left: 150px;"></span>
            <img alt="image description" src="{{ asset('bundles/metalproject/pic/mini-site-map.jpg') }}">
        </div>
    </div>
</div>
HTML;

        $companyContent = <<<HTML
<div class="company-img-wrapper is-bordered float-left"></div>
<div class="about-text float-left">
    <h1 class="title">О компании</h1>

    <div id="default-styles">
        <h1>Заголовок 1 уровня</h1>

        <h2>Заголовок 2 уровня</h2>

        <h3>Заголовок 3 уровня</h3>
        <h4>Заголовок 4 уровня</h4>
        <h5>Заголовок 5 уровня</h5>
        <h6>Заголовок 6 уровня</h6>

        <ul>
            <li>ненумерованный список</li>
            <li>ненумерованный список</li>
            <li>ненумерованный список
                <ul>
                    <li>ненумерованный список</li>
                    <li>ненумерованный список</li>
                    <li>ненумерованный список</li>
                </ul>
            </li>
        </ul>

        <ol>
            <li>нумерованный список</li>
            <li>нумерованный список</li>
            <li>нумерованный список
                <ol>
                    <li>нумерованный список</li>
                    <li>нумерованный список</li>
                    <li>нумерованный список</li>
                </ol>
            </li>
        </ol>
        <strong>Жирный текст</strong> <br/>
        <em>курсив</em>

        <p>абзац</p>
        <hr/>

        Таблица
        <table>
            <thead>
            <tr>
                <td>заголовок</td>
                <td>заголовок</td>
                <td>заголовок</td>
                <td>заголовок</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>текст</td>
                <td>текст</td>
                <td>текст</td>
                <td>текст</td>
            </tr>
            </tbody>
        </table>

        <p class="text">Компания <strong>«Металлопрокат.ру»</strong> была образована в 2000 году и на
            сегодняшний день является одной
            из ведущих компаний, предоставляющих информационные услуги в области металлургии.</p>

        <p class="text">Миссия компании заключается в обеспечении оперативного взаимодействия между потребителем
            и
            поставщиком металла за счет информационных продуктов и услуг, основанных на современных, удобных и
            доступных технологиях.</p>

        <p class="text">Основой компании является интернет-портал Металлопрокат.ру — один из старейших и
            узнаваемых
            онлайн-проектов металлургической отрасли. Ежедневно Портал посещает порядка 5000 пользователей, в
            разделах Портала зарегистрировано более 30.000 компаний — производителей, трейдеров, потребителей и
            других представителей отрасли.</p>

        <p class="text">Каждый из разделов портала — это полноценный сервис для участников металлургического
            рынка.</p>

        <p class="text">Подробнее об информационных продуктах и услугах Компании Вы можете ознакомиться в
            разделе
            «Продукты и услуги».</p>
    </div>
</div>

HTML;
        $this->addSql('UPDATE agreement_template SET `content` = :content WHERE id = :id', array('content' => $contactContent, 'id' => AgreementTemplate::CORP_CONTACTS));
        $this->addSql('UPDATE agreement_template SET `content` = :content WHERE id = :id', array('content' => $companyContent, 'id' => AgreementTemplate::CORP_ABOUT_COMPANY));

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
