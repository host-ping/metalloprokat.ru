<?php

namespace Metal\ProjectBundle\Controller;

use Buzz\Bundle\BuzzBundle\Exception\ResponseException;
use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Entity\Site;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;

class SiteAdminController extends CRUDController
{
    public function batchActionSendGoogle(ProxyQueryInterface $selectedModelQuery)
    {
        $browser = $this->get('buzz')->getBrowser('api.google');
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();

        $sites = $selectedModelQuery->execute();
        /* @var $sites Site[] */

        $usedSourceTypesIds = array();
        foreach ($sites as $site) {
            $usedSourceTypesIds[$site->getSourceTypeId()] = true;
        }

        if (count($usedSourceTypesIds) > 1) {
            $this->addFlash('sonata_flash_error', 'Выберите сайты с одним базовым хостом');

            return $this->redirectToList();
        }

        $siteCode = SiteSourceTypeProvider::create(key($usedSourceTypesIds))->getCode();
        $token = $session->get('google_access_token.'.$siteCode);
        $config = $this->container->getParameter('webmaster_clients')[$siteCode];

        if (!$token) {
            $googleClientId = $config['google'];
            if (!$googleClientId) {
                $this->addFlash(
                    'sonata_flash_error',
                    'Не указан google client id webmaster_clients/'.$siteCode.'/google'
                );

                return $this->redirectToList();
            }

            $state = md5('google-state:'.$this->getUser()->getId());
            $session->set('google_access_token_key', $siteCode);
            $session->set('google_access_token_state', $state);

            $queryParams = array(
                'client_id' => $googleClientId,
                'response_type' => 'code',
                'scope' => 'https://www.googleapis.com/auth/siteverification https://www.googleapis.com/auth/webmasters',
                'redirect_uri' => $this->admin->generateUrl('take-token-google', array(), true),
                'state' => $state,
            );

            return $this->redirect('https://accounts.google.com/o/oauth2/auth?'.http_build_query($queryParams));
        }

        $headers = array('Content-Type' => 'application/json');
        foreach ($sites as $site) {
            if ($site->getCity()) {
                $scheme = $site->getCity()->getCountry()->getSecure() ? 'https' : 'http';
            } else {
                list($slug, $host) = explode('.', $site->getHostname(), 2);
                $scheme = 'http';
                if (isset($this->container->getParameter('hostnames_map')[$host])) {
                    $scheme = $this->container->getParameter('hostnames_map')[$host]['host_prefix'];
                }
            }
            $identifier = $scheme.'://'.$site->getHostname();

            $queryParams = array(
                'verificationMethod' => 'META',
                'site' => array('identifier' => $identifier, 'type' => 'SITE'),
            );

            try {
                $result = $browser->post(
                    '/siteVerification/v1/token?'.http_build_query(array('access_token' => $token)),
                    $headers,
                    json_encode($queryParams)
                );
            } catch (ResponseException $e) {
                $this->addFlash('sonata_flash_error', $e->getMessage());

                return $this->redirectToList();
            }

            $responseJson = json_decode($result->getContent(), true);
            if (!isset($responseJson['token'])) {
                $this->addFlash('sonata_flash_error', 'Не удалось получить код для сайта');

                return $this->redirectToList();
            }

            preg_match('#content="(.+)?"#', $responseJson['token'], $matches);
            if (count($matches) > 0) {
                $site->setGoogleCode($matches[1]);
                $em->flush();
            }
            if ($site->getGoogleCode()) {
                try {
                    $result = $browser->post(
                        '/siteVerification/v1/webResource?'.http_build_query(
                            array('verificationMethod' => 'META', 'access_token' => $token)
                        ),
                        $headers,
                        json_encode(array('site' => array('identifier' => $identifier, 'type' => 'SITE')))
                    );
                } catch (ResponseException $e) {
//                    vd($site->getHostname(), $matches[1]);
                    $this->addFlash('sonata_flash_error', $e->getMessage());

                    return $this->redirectToList();
                }

                $responseJson = json_decode($result->getContent(), true);
                if ($responseJson['id']) {
                    $site->setGoogleSiteId($responseJson['id']);
                    $em->flush();
                }
            }
        }

        foreach ($sites as $site) {
            if ($site->getCity()) {
                $scheme = $site->getCity()->getCountry()->getSecure() ? 'https' : 'http';
            } else {
                list($slug, $host) = explode('.', $site->getHostname(), 2);
                $scheme = 'http';
                if (isset($this->container->getParameter('hostnames_map')[$host])) {
                    $scheme = $this->container->getParameter('hostnames_map')[$host]['host_prefix'];
                }
            }
            $identifier = $scheme.'://'.$site->getHostname();

            $url = sprintf(
                '/webmasters/v3/sites/%s?%s',
                urlencode($identifier),
                http_build_query(array('access_token' => $token))
            );

            try {
                $browser->put($url, $headers);
            } catch (ResponseException $e) {
                $this->addFlash('sonata_flash_error', $e->getMessage());

                return $this->redirectToList();
            }
        }

        return $this->redirectToList();
    }

    public function takeGoogleTokenAction(Request $request)
    {
        $session = $this->get('session');
        $browser = $this->get('buzz')->getBrowser('accounts.google');

        $state = $request->query->get('state');
        if ($state != $session->get('google_access_token_state')) {
            $this->addFlash('sonata_flash_error', 'Bad state');

            return $this->redirectToList();
        }

        $siteCode = $session->get('google_access_token_key');
        $config = $this->container->getParameter('webmaster_clients')[$siteCode];

        $params = array(
            'code' => $request->query->get('code'),
            'client_id' => $config['google'],
            'client_secret' => $config['google_secret'],
            'redirect_uri' => $this->admin->generateUrl('take-token-google', array(), true),
            'grant_type' => 'authorization_code',
        );
        $result = $browser->post('/o/oauth2/token', array(), http_build_query($params));
        $responseJson = json_decode($result->getContent(), true);

        if (isset($responseJson['access_token'])) {
            $session->set('google_access_token.'.$siteCode, $responseJson['access_token']);
            $this->addFlash('sonata_flash_success', 'Получен ключ для Google Site Verification API, повторите попытку');
        } else {
            $this->addFlash('sonata_flash_error', 'Не удалось получить ключ для Google Site Verification API');
        }

        return $this->redirectToList();
    }

    public function batchActionSendYandex(ProxyQueryInterface $selectedModelQuery)
    {
        $url = '/api/v2/hosts';
        $browser = $this->get('buzz')->getBrowser('webmaster.yandex');
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();

        $sites = $selectedModelQuery->execute();
        /* @var $sites Site[] */

        $usedSourceTypesIds = array();
        foreach ($sites as $site) {
            $usedSourceTypesIds[$site->getSourceTypeId()] = true;
        }

        if (count($usedSourceTypesIds) > 1) {
            $this->addFlash('sonata_flash_error', 'Выберите сайты с одним базовым хостом');

            return $this->redirectToList();
        }

        $siteCode = SiteSourceTypeProvider::create(key($usedSourceTypesIds))->getCode();
        $token = $session->get('ya_access_token.'.$siteCode);
        $config = $this->container->getParameter('webmaster_clients')[$siteCode];

        if (!$token) {
            $yandexClientId = $config['yandex'];
            if (!$yandexClientId) {
                $this->addFlash(
                    'sonata_flash_error',
                    'Не указан yandex client id webmaster_clients/'.$siteCode.'/yandex'
                );

                return $this->redirectToList();
            }

            $session->set('ya_access_token_key', $siteCode);

            return $this->redirect('https://oauth.yandex.ru/authorize?response_type=token&client_id='.$yandexClientId);
        }

        $headers = array('Authorization' => 'OAuth '.$token);
        foreach ($sites as $site) {
            $content = sprintf('<host><name>%s</name></host>', $site->getHostname());

            $success = true;
            try {
                $result = $browser->post($url, $headers, $content);
            } catch (ResponseException $e) {
                $xml = new \SimpleXMLElement($e->getResponse()->getContent());
                $this->addFlash(
                    'sonata_flash_error',
                    'Ошибка при добавлении сайта '.$site->getHostname().' '.$xml->attributes()->code
                );
                if ($xml->attributes()->code != 'HOST_ALREADY_ADDED') {
                    break;
                }
                $success = false;
            }

            if ($success) {
                $location = $result->getHeader('Location');
                preg_match('#.*\/(\d+)$#', $location, $id);
                $site->setYandexSiteId($id[count($id) - 1]);
                $em->flush();
            }
            $this->confirmSiteOnYandex($site);
        }

        return $this->redirectToList();
    }

    public function takeYandexTokenAction(Request $request)
    {
        if ($request->query->get('access_token')) {
            $session = $this->get('session');
            $siteCode = $session->get('ya_access_token_key');
            $session->set('ya_access_token.'.$siteCode, $request->query->get('access_token'));
            $this->addFlash('sonata_flash_success', 'Получен ключ для yandex webmaster, повторите попытку');

            return $this->redirectToList();
        }

        if ($request->query->get('redirected')) {
            $this->addFlash('sonata_flash_error', 'Token not found');

            return $this->redirectToList();
        }

        return $this->render('MetalProjectBundle:Default:ya_webmaster.html.twig');
    }

    private function confirmSiteOnYandex(Site $site)
    {
        $session = $this->get('session');
        if (!$site->getYandexSiteId()) {
            $this->addFlash('sonata_flash_error', 'Не удалось получить код для выбраного сайта '.$site->getHostname());

            return;
        }

        $browser = $this->get('buzz')->getBrowser('webmaster.yandex');
        $verifyUrl = sprintf('/api/v2/hosts/%d/verify', $site->getYandexSiteId());
        $token = $session->get('ya_access_token.'.$site->getSourceType()->getCode());
        $headers = array('Authorization' => 'OAuth '.$token);

        if (!$site->getYandexCode()) {
            try {
                $result = $browser->get($verifyUrl, $headers);
            } catch (ResponseException $e) {
                $xml = new \SimpleXMLElement($e->getResponse()->getContent());
                $this->addFlash(
                    'sonata_flash_error',
                    'Ошибка при получении кода '.$site->getHostname().' '.$xml->attributes()->code
                );
            }
            $responseXml = simplexml_load_string($result->getContent());
            if ($responseXml->verification && $responseXml->verification->uin) {
                $site->setYandexCode($responseXml->verification->uin);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        try {
            $browser->put($verifyUrl, $headers, '<host><type>META_TAG</type></host>');
        } catch (ResponseException $e) {
            $xml = new \SimpleXMLElement($e->getResponse()->getContent());
            $this->addFlash(
                'sonata_flash_error',
                'Подтверждении сайта '.$site->getHostname().' '.$xml->attributes()->code
            );
        }
    }

    public function importDomainAction()
    {
        $projectFamily = $this->container->getParameter('project.family');
        if ($projectFamily == 'product') {
            $this->importForProductFamily();
        } else {
            $this->importForProkatFamily();
        }

        $this->addFlash('sonata_flash_success', 'Импорт хостов выполнен');

        return $this->redirectToList();
    }

    private function importForProkatFamily()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        // вставляем хосты для спроса, если их ее не было в базе
        $em->getConnection()->executeQuery(
            "
            INSERT IGNORE INTO site (hostname, source_type, city_id)
            SELECT CONCAT(c.Keyword, '.metalspros.ru'), 2, c.Region_ID
            FROM Classificator_Region c
            LEFT JOIN site s
            ON s.city_id = c.Region_ID
            WHERE s.id IS NULL AND c.Keyword IS NOT NULL AND c.Keyword != ''"
        );
        // вставляем хосты для проката
        $em->getConnection()->executeQuery(
            "
            INSERT IGNORE INTO site (hostname, source_type, city_id)
            SELECT CONCAT(ci.Keyword, '.' ,co.base_host), 1, ci.Region_ID
            FROM Classificator_Region ci
            JOIN Classificator_Country co
            ON ci.country_id = co.Country_ID
            WHERE ci.Keyword IS NOT NULL AND ci.Keyword != ''
        "
        );

        // вставляем хост для www спрос
        $em->getConnection()->executeQuery(
            "
            INSERT IGNORE INTO site (hostname, source_type)
            VALUES ('www.metalspros.ru', 2)

        "
        );
        // вставляем хосты для www прокат
        $em->getConnection()->executeQuery(
            "
            INSERT IGNORE INTO site (hostname, source_type)
            SELECT CONCAT('www.', c.base_host), 1
            FROM Classificator_Country c
            WHERE c.base_host IS NOT NULL AND c.base_host != ''
        "
        );
    }

    private function importForProductFamily()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        // вставляем хосты для продукта
        $em->getConnection()->executeQuery(
            "
            INSERT IGNORE INTO site (hostname, source_type, city_id)
            SELECT CONCAT(ci.Keyword, '.' ,co.base_host), 8, ci.Region_ID
            FROM Classificator_Region ci
            JOIN Classificator_Country co
            ON ci.country_id = co.Country_ID
            WHERE ci.Keyword IS NOT NULL AND ci.Keyword != ''
        "
        );

        // вставляем хосты для www продукт
        $em->getConnection()->executeQuery(
            "
            INSERT IGNORE INTO site (hostname, source_type)
            SELECT CONCAT('www.', c.base_host), 8
            FROM Classificator_Country c
            WHERE c.base_host IS NOT NULL AND c.base_host != ''
        "
        );
    }
}
