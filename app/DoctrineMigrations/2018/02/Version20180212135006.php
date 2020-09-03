<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Metal\ServicesBundle\Entity\Package;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180212135006 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf(
            $this->container->getParameter('project.family') !== 'metalloprokat',
            'Миграция только для металлороката.'
        );

        $this->addSql(
            "INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
                  VALUES (
                  33,
                  'Только до конца февраля -20% на любой срок!',
                  '2018-02-12 16:34:51',
                  '2018-02-12 16:34:51',
                  '2018-02-12 16:34:51',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_12_02_2018_discount.html.twig'
                  )"
        );

        $companiesIds = $this->getCompaniesIds();

        $this->addSql('
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                  JOIN User AS user ON user.User_ID = subscribers.user_id
                  JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                WHERE subscribers.is_invalid = false
                AND subscribers.deleted = false
                AND user.Checked = true
                AND subscribers.bounced_at IS NULL
                AND (company.code_access = :package OR company.Message_ID IS NULL)
                AND company.Message_ID NOT IN (:companiesIds)
                ',
                array(
                    'newsletter_id' => 33,
                    'package' => Package::BASE_PACKAGE,
                    'companiesIds' => $companiesIds
                ),
                array(
                    'companiesIds' => Connection::PARAM_INT_ARRAY
                )
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    private function getCompaniesIds()
    {
        return array(
            2047313,
            2047411,
            2047559,
            2047924,
            2047983,
            2048108,
            2048167,
            2048241,
            2048306,
            2048475,
            2048486,
            2048568,
            2048627,
            2048692,
            2048784,
            2049061,
            2049066,
            2049075,
            2049109,
            2049140,
            2049268,
            2049580,
            2049584,
            2049624,
            2049691,
            2050071,
            2050134,
            2050267,
            2050817,
            2051018,
            2051052,
            2051220,
            2051528,
            2051585,
            2051620,
            2051710,
            2052007,
            2052035,
            2052052,
            2052143,
            2052214,
            2052216,
            2052241,
            2052357,
            2052388,
            2052408,
            2052418,
            2052530,
            2052567,
            2052597,
            2052781,
            2052976,
            2053516,
            2055351,
            490,
            861,
            1611,
            2202,
            2733,
            3153,
            3259,
            3329,
            3389,
            3705,
            3907,
            4114,
            4117,
            4350,
            4463,
            4619,
            4941,
            4958,
            5698,
            5711,
            6269,
            6302,
            6430,
            6529,
            6531,
            6634,
            6801,
            6827,
            6828,
            6940,
            7256,
            7273,
            7283,
            7308,
            7386,
            7513,
            7708,
            7744,
            7858,
            7992,
            8102,
            8442,
            8491,
            8517,
            8589,
            8595,
            8616,
            8630,
            8835,
            8868,
            8908,
            9108,
            9223,
            9247,
            9255,
            9281,
            9307,
            9449,
            9486,
            9596,
            9625,
            9626,
            9697,
            9705,
            9745,
            9754,
            9757,
            9771,
            9802,
            9848,
            9866,
            10220,
            10384,
            10432,
            10434,
            10581,
            10615,
            10784,
            10794,
            10841,
            11178,
            11215,
            11372,
            11403,
            11510,
            11587,
            11611,
            11644,
            11973,
            12068,
            12111,
            12260,
            12296,
            12497,
            12537,
            13076,
            13114,
            13375,
            13378,
            13461,
            13534,
            13703,
            13719,
            13800,
            13856,
            13941,
            14189,
            14211,
            14216,
            14345,
            14408,
            14538,
            14716,
            14845,
            15172,
            15501,
            15592,
            15610,
            16222,
            16356,
            16685,
            16867,
            17666,
            17777,
            17803,
            17962,
            18046,
            18122,
            18448,
            18496,
            18677,
            18736,
            18793,
            19184,
            19193,
            19196,
            19277,
            19279,
            19324,
            19378,
            19394,
            19476,
            19581,
            19770,
            19984,
            20027,
            20067,
            20452,
            20462,
            20472,
            20523,
            20593,
            20625,
            20692,
            20995,
            21023,
            21079,
            21188,
            21422,
            21424,
            21455,
            21574,
            21617,
            21697,
            21723,
            21839,
            21884,
            21937,
            21943,
            21954,
            21980,
            22014,
            22042,
            22087,
            22340,
            22379,
            22420,
            22440,
            22505,
            22517,
            22531,
            22585,
            22700,
            22752,
            22813,
            22830,
            22863,
            22890,
            22968,
            23030,
            23106,
            23362,
            23457,
            23467,
            23506,
            23561,
            23579,
            23629,
            23962,
            24076,
            24268,
            24278,
            24282,
            24345,
            24384,
            24413,
            24506,
            24586,
            24623,
            24642,
            24847,
            24867,
            24955,
            24980,
            25000,
            25037,
            25089,
            25162,
            25176,
            25278,
            25309,
            25484,
            25533,
            25595,
            25726,
            26033,
            26039,
            26133,
            26151,
            26323,
            26348,
            26480,
            26496,
            26519,
            26623,
            26624,
            26726,
            26856,
            26861,
            26918,
            26938,
            26953,
            26978,
            26986,
            26990,
            27006,
            27067,
            27079,
            27133,
            27157,
            27184,
            27232,
            27264,
            27321,
            27377,
            27400,
            27455,
            27583,
            27587,
            27697,
            27716,
            27782,
            27791,
            27820,
            27830,
            27884,
            27960,
            28023,
            28043,
            28047,
            28132,
            28152,
            28157,
            28270,
            28274,
            28299,
            28331,
            28336,
            28391,
            28471,
            28484,
            28555,
            28557,
            28598,
            28733,
            28747,
            28910,
            28914,
            28975,
            28976,
            28994,
            29102,
            29121,
            29222,
            29242,
            29250,
            29367,
            29394,
            29404,
            29446,
            29646,
            29715,
            29718,
            29774,
            29917,
            29942,
            29959,
            29979,
            30021,
            30079,
            30209,
            30236,
            30243,
            30271,
            30273,
            30280,
            30301,
            30328,
            30329,
            30338,
            30342,
            30443,
            30449,
            30456,
            30464,
            30470,
            30540,
            30549,
            30556,
            30615,
            30622,
            30628,
            30639,
            30729,
            30811,
            30845,
            30876,
            30879,
            30913,
            30939,
            31001,
            31091,
            31135,
            31319,
            31326,
            31333,
            31347,
            31356,
            31413,
            31422,
            31505,
            31507,
            31517,
            31731,
            31881,
            32090,
            32565,
            32644,
            32794,
            32799,
            32801,
            32810,
            33087,
            33095,
            33171,
            33181,
            33187,
            33398,
            33448,
            33577,
            33588,
            33612,
            33665,
            33684,
            33871,
            34052,
            34212,
            34296,
            34298,
            34484,
            34598,
            34639,
            34746,
            34838,
            34841,
            34992,
            35157,
            35417,
            35500,
            35658,
            35692,
            35883,
            35981,
            36024,
            36056,
            36148,
            36167,
            36309,
            36388,
            36422,
            36595,
            36610,
            36726,
            36767,
            36990,
            37082,
            37185,
            37301,
            37339,
            37391,
            37754,
            37760,
            37778,
            38159,
            38214,
            38220,
            38278,
            38295,
            38389,
            38409,
            38462,
            38493,
            38523,
            38623,
            38636,
            38713,
            38759,
            38866,
            39089,
            39103,
            39120,
            39241,
            39272,
            39430,
            39488,
            39597,
            39685,
            39856,
            39906,
            39913,
            39949,
            40130,
            40206,
            40235,
            40278,
            40416,
            40599,
            40657,
            40818,
            40850,
            41014,
            41036,
            41154,
            41339,
            41356,
            41385,
            41451,
            41510,
            41767,
            41794,
            41823,
            41835,
            41903,
            41925,
            41960,
            42366,
            42371,
            42436,
            42632,
            42642,
            42718,
            42783,
            42789,
            42841,
            43056,
            43167,
            43176,
            43471,
            43496,
            43555,
            43664,
            43805,
            43851,
            44118,
            44163,
            44170,
            44246,
            44267,
            44270,
            44295,
            44321,
            44323,
            44324,
            44585,
            44608,
            44626,
            44671,
            44755,
            44783,
            44840,
            45020,
            45066,
            45100,
            45226,
            45247,
            45322,
            45388,
            45424,
            45453,
            45498,
            45865,
            45883,
            46125,
            46423,
            46471,
            46593,
            46629,
            46715,
            46771,
            46775,
            46801,
            46910,
            47081,
            47255,
            47305,
            47308,
            47332,
            47385,
            47478,
            47552,
            47553,
            47640,
            47664,
            47681,
            47786,
            2008044,
            2011213,
            2015112,
            2018426,
            2020388,
            2020776,
            2020822,
            2020832,
            2020850,
            2020858,
            2020941,
            2020944,
            2021054,
            2021104,
            2021124,
            2021165,
            2021187,
            2021230,
            2021314,
            2021340,
            2021360,
            2021390,
            2021529,
            2021551,
            2021699,
            2021813,
            2021816,
            2021870,
            2021923,
            2021925,
            2021983,
            2022017,
            2022055,
            2022085,
            2022186,
            2022219,
            2022223,
            2022268,
            2022312,
            2022331,
            2022511,
            2022530,
            2022560,
            2022615,
            2022642,
            2022733,
            2022882,
            2022887,
            2022929,
            2023022,
            2023045,
            2023058,
            2023074,
            2023199,
            2023226,
            2023268,
            2023316,
            2023342,
            2023364,
            2023392,
            2023480,
            2023482,
            2023599,
            2023849,
            2023881,
            2023951,
            2024046,
            2024057,
            2024094,
            2024108,
            2024156,
            2024159,
            2024180,
            2024206,
            2024323,
            2024371,
            2024441,
            2024481,
            2024502,
            2024542,
            2024567,
            2024588,
            2024649,
            2024978,
            2025021,
            2025099,
            2025118,
            2025185,
            2025217,
            2025242,
            2025298,
            2025420,
            2025457,
            2025475,
            2025499,
            2025507,
            2025618,
            2025624,
            2025640,
            2025657,
            2025662,
            2025697,
            2025916,
            2025928,
            2025991,
            2026054,
            2026182,
            2026226,
            2026313,
            2026339,
            2026372,
            2026400,
            2026473,
            2026476,
            2026478,
            2026586,
            2026679,
            2026692,
            2026693,
            2026695,
            2026739,
            2026779,
            2026841,
            2026880,
            2026911,
            2026930,
            2027008,
            2027101,
            2027111,
            2027193,
            2027197,
            2027207,
            2027253,
            2027322,
            2027330,
            2027347,
            2027395,
            2027515,
            2027516,
            2027523,
            2027602,
            2027695,
            2027721,
            2027740,
            2027763,
            2027783,
            2027789,
            2027822,
            2027866,
            2027935,
            2027941,
            2027946,
            2027962,
            2028092,
            2028180,
            2028228,
            2028272,
            2028281,
            2028290,
            2028423,
            2028506,
            2028511,
            2028565,
            2028607,
            2028635,
            2028642,
            2028696,
            2028723,
            2028737,
            2028741,
            2028794,
            2028818,
            2028869,
            2028909,
            2029003,
            2029039,
            2029096,
            2029152,
            2029198,
            2029225,
            2029235,
            2029242,
            2029274,
            2029308,
            2029315,
            2029338,
            2029409,
            2029443,
            2029483,
            2029537,
            2029593,
            2029613,
            2029631,
            2029650,
            2029654,
            2029686,
            2029698,
            2029713,
            2029748,
            2029780,
            2029784,
            2029791,
            2029807,
            2029871,
            2029878,
            2029975,
            2029982,
            2030010,
            2030127,
            2030233,
            2030273,
            2030284,
            2030309,
            2030378,
            2030401,
            2030480,
            2030516,
            2030517,
            2030548,
            2030560,
            2030571,
            2030579,
            2030603,
            2030615,
            2030616,
            2030730,
            2030760,
            2030850,
            2030856,
            2030862,
            2030864,
            2030894,
            2030898,
            2030937,
            2030953,
            2031025,
            2031089,
            2031117,
            2031141,
            2031155,
            2031168,
            2031190,
            2031203,
            2031272,
            2031298,
            2031427,
            2031503,
            2031535,
            2031538,
            2031674,
            2031694,
            2031696,
            2031722,
            2031779,
            2031873,
            2031876,
            2032012,
            2032125,
            2032169,
            2032251,
            2032281,
            2032419,
            2032453,
            2032461,
            2032462,
            2032533,
            2032569,
            2032582,
            2032622,
            2032675,
            2032749,
            2032779,
            2032803,
            2032809,
            2032891,
            2032932,
            2033009,
            2033045,
            2033069,
            2033101,
            2033154,
            2033171,
            2033174,
            2033251,
            2033252,
            2033270,
            2033395,
            2033530,
            2033537,
            2033547,
            2033667,
            2033703,
            2033731,
            2033762,
            2033836,
            2033851,
            2033943,
            2033981,
            2033997,
            2034038,
            2034106,
            2034227,
            2034311,
            2034389,
            2034400,
            2034402,
            2034454,
            2034507,
            2034510,
            2034562,
            2034585,
            2034616,
            2034620,
            2034675,
            2034680,
            2034740,
            2034772,
            2034781,
            2034786,
            2034787,
            2034810,
            2034860,
            2034906,
            2035035,
            2035115,
            2035118,
            2035174,
            2035221,
            2035302,
            2035317,
            2035327,
            2035333,
            2035372,
            2035546,
            2035585,
            2035612,
            2035702,
            2035712,
            2035715,
            2035725,
            2036208,
            2036216,
            2036286,
            2036317,
            2036374,
            2036464,
            2036490,
            2036567,
            2036648,
            2036692,
            2036708,
            2036808,
            2036811,
            2036841,
            2036855,
            2036937,
            2036986,
            2037020,
            2037214,
            2037405,
            2037410,
            2037530,
            2037573,
            2037610,
            2037637,
            2037665,
            2037692,
            2037693,
            2037714,
            2037809,
            2037875,
            2037889,
            2037892,
            2037956,
            2038010,
            2038091,
            2038115,
            2038138,
            2038195,
            2038369,
            2038370,
            2038373,
            2038460,
            2038606,
            2038682,
            2038747,
            2038849,
            2038852,
            2038988,
            2039082,
            2039407,
            2039449,
            2039454,
            2039749,
            2039781,
            2039784,
            2039843,
            2040001,
            2040021,
            2040188,
            2040426,
            2040469,
            2040496,
            2040633,
            2040873,
            2041017,
            2041026,
            2041038,
            2041231,
            2041240,
            2041462,
            2041467,
            2041497,
            2041641,
            2041704,
            2041813,
            2041933,
            2041948,
            2042037,
            2042122,
            2042348,
            2042425,
            2042492,
            2042499,
            2042553,
            2042600,
            2042637,
            2042917,
            2043247,
            2043396,
            2043477,
            2043546,
            2043718,
            2043742,
            2043770,
            2043790,
            2043803,
            2043821,
            2043922,
            2043966,
            2044015,
            2044073,
            2044116,
            2044151,
            2044259,
            2044458,
            2044492,
            2044539,
            2044552,
            2044663,
            2044826,
            2044827,
            2045049,
            2045378,
            2045514,
            2046112,
            2046249,
            2046265,
            2046392,
            2046477,
            2046478,
            2047040,
            2047080,
            2047107,
            2047128,
        );
    }
}
