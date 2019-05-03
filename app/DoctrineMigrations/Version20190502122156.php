<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Query;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Capco\AppBundle\Entity\SiteParameter;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190502122156 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $newParameters = [
            [
                'keyname' => 'cookies-list',
                'value' =>
                    '<h3>Cookies internes nécessaires au site pour fonctionner </h3><table><thead><tr style="text-align:center;"><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Nom du cookie</strong></p></th><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Finalité</strong></p></th><th style="border:solid 1px black;" style="border:solid;" colspan="1" rowspan="1"><p><strong>Durée de conservation</strong></p></th></tr></thead><tbody><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>PHPSESSID</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Utilisé par Cap-collectif pour garantir la session de l&acute;utilisateur</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Session</p></td></tr><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>__cfduid</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Utilisé par le réseau de contenu Cloudflare, pour identifier le trafic web fiable.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>12 mois</p></td></tr><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>hasFullConsent</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Utilisé par Cap-collectif  pour sauvegarder les choix de consentement des cookies tiers.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>13 mois</p></td></tr></tbody></table><h3>Cookies de mesure d&acute; audience</h3><p>Les outils de mesures d&acute;audience sont déployés afin d&acute;obtenir des informations sur la navigation des visiteurs. Ils permettent notamment de comprendre comment les utilisateurs arrivent sur un site et de reconstituer leur parcours.</p><p>URL du site utilise l&acute; outil de mesure d&acute; audience <a href="https://www.google.com/url?q=https://www.google.fr/analytics/terms/fr.html&sa=D&ust=1555580197522000">Google Analytics</a>.</p><table><thead><tr style="text-align:center;"><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Nom du cookie</strong></p></th><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Finalité</strong></p></th><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Durée de conservation</strong></p></th></tr></thead><tbody><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>_ga</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Enregistre un identifiant unique utilisé pour générer des données statistiques sur la façon dont le visiteur utilise le site.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>13 mois</p></td></tr><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>_gat</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Ce cookie est utilisé pour surveiller le taux de requêtes vers les serveurs de Google Analytics.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>10 mn</p></td></tr><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>_gid</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Ce cookie stocke et met à jour une valeur unique pour chaque page visitée.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>24h</p></td></tr></tbody></table><h3>Cookies de communication personnalisée</h3><p>Les cookies de communication personnalisée sont utilisés pour effectuer le suivi des visiteurs et ainsi proposer les messages de communication du grand débat  sur les autres sites internet et/ou applications qu&acute;ils consultent.</p><p>URL du site utilise l‘outil <a href="https://www.google.com/url?q=https://marketingplatform.google.com/intl/fr_ALL/about/&sa=D&ust=1555580197525000">Google Marketing Platform</a>.</p><table><thead><tr style="text-align:center;"><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Nom du cookie</strong></p></th><th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Finalité<strong></p> </th> <th style="border:solid 1px black;" colspan="1" rowspan="1"><p><strong>Durée de conservation</strong></p></th></tr></thead><tbody><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>IDE</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Utilisé par Google Marketing Platform pour enregistrer et signaler les actions de l&acute; utilisateur du site après qu&acute; il ait vu ou cliqué sur un message de communication du grand débat afin de mesurer l&acute; efficacité et présenter des messages de communication adaptés à l&acute; utilisateur.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>13 mois</p></td></tr><tr><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>DSID</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>Utilisé par  Google Marketing Platform  afin de suivre votre activité multi-appareils.</p></td><td style="border:solid 1px black;" colspan="1" rowspan="1"><p>13 mois</p></td></tr></tbody></table>',
                'category' => 'pages.cookies',
                'position' => 3,
                'type' => SiteParameter::$types['rich_text'],
            ],
            [
                'keyname' => 'privacy-policy',
                'value' =>
                    '<p><div style="font-size:11pt; font-family:Arial; color:black; background-color:transparent; font-variant-numeric:normal; font-variant-east-asian:normal; vertical-align:baseline; white-space:pre-wrap;"><p style="line-height:1.2; margin-top:0pt; margin-bottom:0pt; text-align:center;">Dernière mise à jour:<span style="background-color:yellow;">[Date de mise en ligne /de mise à jour]</span></p><p style="line-height:1.2; margin-top:0pt; margin-bottom:0pt; text-align:justify;"> </p><div style="margin-left:-6.8pt;"> <table style="border:none;border-collapse:collapse"><colgroup><col width="614" /></colgroup><tbody><tr style="height:95pt"> <td style="border:black solid 0.5pt;vertical-align:middle;padding:0pt 3.5pt 0pt 3.5pt;"><p style="line-height:1.28;margin-top:0pt;margin-bottom:0pt;text-align:justify;"><span style="font-weight:700;">Bienvenue sur </span><span style="background-color:yellow; font-weight:700;">URL</span>(ci-après la « <span style="font-weight:700;">Plateforme</span> »)<span style="font-weight:700;">.</span></p><p style="line-height:1.28;margin-top:0pt;margin-bottom:0pt;text-align:justify;"><span style="font-weight:700;">Le responsable de traitement de vos données personnelles est </span><span style="background-color:yellow; font-weight:700;">NOM DE LA PERSONNE MORALE</span> (ci-après « </span><span style="font-weight:700;">nous</span> »).</p><p style="line-height:1.28;margin-top:0pt;margin-bottom:0pt;text-indent:-3.05pt;text-align:justify;padding:0pt 0pt 0pt 3.05pt;">Adresse postale:</span><span style="background-color:yellow;">ADRESSE</span></p><p style="line-height:1.28;margin-top:0pt;margin-bottom:0pt;text-indent:-3.05pt;text-align:justify;padding:0pt 0pt 0pt 3.05pt;">Adresse mail:</span><span style="background-color:yellow;">ADRESSE</span></p><p style="line-height:1.2; margin-top:0pt; margin-bottom:0pt; text-align:right;"><a href="/legal">Voir les mentions légales</a></span></p> </td></tr></tbody> </table></div><div style="line-height:1.2;margin-top:0pt;margin-bottom:0pt;text-align:justify;"> <p>Dans le cadre de <span style="background-color:yellow;">NOM DU SITE</span>, vous nous transmettez des informations en vous inscrivant, en participant à des projets participatifs et en communiquant avec nous. Les champs obligatoires sont indiqués comme tels dans les formulaires. Nous nous engageons à ce que la collecte et le traitement de vos données soient effectués de manière licite, loyale et transparente, conformément au <a href="https://www.cnil.fr/fr/reglement-europeen-protection-donnees" style="text-decoration-line:none;"><span style="font-size:11pt; font-family:Arial;  ">Règlement européen général sur la protection des données</span></a> (« RGPD ») et à la<a href="https://www.cnil.fr/fr/loi-78-17-du-6-janvier-1978-modifiee" style="text-decoration-line:none;"><span style="font-size:11pt; font-family:Arial;  ">Loi informatiques et Libertés de 1978 modifiée</span></a> (« LIL »). </p> <p>Cette collecte d&acute;information se limite au nécessaire, conformément au principe de minimisation des données. Les définitions fournies à <a href="https://www.cnil.fr/fr/reglement-europeen-protection-donnees/chapitre1#Article4" style="text-decoration-line:none;"><span style="font-size:11pt; font-family:Arial;  ">l&acute;article 4 du RGPD</span></a> sont applicables aux présentes. En cas de mise à jour, nous n&acute;abaisserons pas le niveau de confidentialité de manière substantielle sans l&acute;information préalable des personnes concernées. </p> <p>Par la présente, nous nous efforcerons de répondre en toute transparence aux questions suivantes:Quelles catégories de données personnelles traitons-nous ? Dans quels buts ? Sur quelles bases légales ? Quels sont vos droits ? Comment les exercer ? Pendant combien de temps les données sont-elles conservées ? Vous trouverez également nos engagements en matière de sous-traitance, de transferts, de communication à des tiers et en cas de faille de sécurité. </p> <p>Pour toute précision ou réclamation, n&acute;hésitez pas à nous contacter. </p> <p><span style="font-weight:700;">Avertissements.</span> <span style="font-weight:700;">Si vous décidez de publier des données relevant de votre vie privée ou sensibles</span> (opinion politiques et philosophiques, appartenance syndicale, information sur votre santé, orientation sexuelle, convictions religieuses…) à votre initiative ou déduites <span style="font-style:italic;">via</span> vos votes, contributions, arguments, commentaires et prises de position de quelque nature que ce soit sur la Plateforme rattachée à votre profil, vous devez être vigilants car celle-ci seront visibles. Nous vous rappelons que les traitements portant sur des données à caractère personnel rendues publiques par la personne concernée ne sont pas soumis à l&acute;interdiction de principe au traitement de données sensibles par la loi (Art. 8, II, 4° de la loi Informatiques et Libertés de 1978). </p> <p><span style="font-weight:700;">QUELLES DONNÉES PERSONNELLES COLLECTONS-NOUS SUR LA PLATEFORME ? </span></p> <p><span style="font-weight:700;">Données d&acute;inscription et informations requises pour participer.</span> Pour vous inscrire, nous vous demandons de fournir un nom ou un pseudonyme, une adresse électronique, et de créer un mot de passe. <span style="background-color:yellow;">Vous pourrez également nous indiquer votre statut (liste des types) et votre code postal, à titre facultatif</span>. Pour vous inscrire, il vous sera enfin demandé de cocher que vous n&acute;êtes pas un robot (technologie reCaptcha). </p> <p><span style="font-weight:700;">Données du profil public. </span>Votre nom / pseudonyme choisi au moment de l&acute;inscription sera visible sur la Plateforme et sur la page de présentation de votre profil, que vous pouvez enrichir. Pour éditer et personnaliser votre profil, et uniquement si vous le souhaitez, vous pouvez ajouter une photo de profil, une biographie, votre localisation, votre site web et les liens vers vos profils sur les réseaux sociaux. </p> <p><span style="font-weight:700;">Contributions.</span> Vous pouvez participer à la vie de la Plateforme en publiant des contributions. Ces contributions permettent de connaître la position des personnes inscrites sur la Plateforme. Leur contenu, date et heure sont rendus publiques – sauf mention contraire – et sont rattachés à votre profil. </p> <p><span style="font-weight:700;">Journaux de connexion. </span>Des données d&acute;utilisation (adresses IP, logs de connexion et d&acute;utilisation) sont collectées automatiquement sur la Plateforme. </p> <p><span style="font-weight:700;">Cookies.</span> Voir notre <a href="/cookies-page">Politique des cookies</a>.</p> <p><span style="font-weight:700;">DANS QUELS BUTS CES DONNEES SONT-ELLES COLLECTEES ? </span></p> <p><span style="font-weight:700;">Les données personnelles collectées ont pour finalité la gestion de la participation et l&acute;analyse quantitative et qualitative des contributions dans le cadre de </span><span style="background-color:yellow; font-weight:700;">NOM DU SITE</span><span style="font-weight:700;">. </span></p> <p>Les données d&acute;inscription confiées nous permettent d&acute;assurer la gestion de votre compte sur la Plateforme, des procédures d&acute;authentification, de mot de passe oublié, mais aussi de traiter vos éventuelles demandes. </p> <p>Votre adresse électronique devra notamment être confirmée pour finaliser l&acute;inscription et ne sera pas rendue publique. Si vous en formulez le souhait, nous vous adresserons également notre newsletter. </p> <p>L&acute;outil Captcha protège la Plateforme contre l&acute;inscription en masse de façon automatisé. </p> <p><span style="font-weight:700;">PENDANT COMBIEN DE TEMPS VOS DONNEES SONT-ELLES CONSERVEES ? </span></p> <p>Les données identifiantes sont conservées pendant toute la durée de l&acute;inscription à la Plateforme et jusqu&acute;à ce que vous supprimiez votre compte ou que la Plateforme ne soit plus en ligne. </p> <p>Nous avons également des obligations de conservation des données de connexion conformément au <a href="https://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000023646013&amp;dateTexte=20190111" style="text-decoration-line:none;"><span style="font-size:11pt; font-family:Arial;  ">décret n° 2011-219 du 25 février 2011 relatif à la conservation et à la communication des données permettant d&acute;identifier toute personne ayant contribué à la création d&acute;un contenu mis en ligne.</span></a> </p> <p><span style="font-weight:700;">Attention, il ne sera plus possible de modifier vos contributions à l&acute;issue de la phase de participation.</span> Si vous supprimez votre compte, vos contributions apparaîtront sous le pseudonyme « <span style="font-style:italic;">utilisateur supprimé ».</span> A l&acute;issue des phases de consultations, si la Plateforme est toujours en ligne, vous avez ainsi la possibilité de supprimer les données identifiantes à tout moment. </p> <p>Votre email pourra être conservé pour vous proposer toute information sur <span style="background-color:yellow;">NOM DU SITE</span> pendant <span style="background-color:yellow;">3 mois</span> à compter de la fermeture de la Plateforme. Un lien de désinscription vous sera proposé dans toute communication. </p> <p>Au-delà de ces durées, vous êtes informé que les données personnelles que nous collectons pourront être anonymisées et conservées à des fins statistiques. Sinon, elles feront l&acute;objet d&acute;une suppression définitive. Nous pouvons publier, divulguer et utiliser les informations agrégées (informations combinées de telle sorte que personne ne puisse plus être identifié ou mentionné) et les informations non personnelles à des fins d&acute;analyse de la participation sur la Plateforme et pour justifier des décisions que nous pourrions prendre à l&acute;issue des phases de participation à un projet donné. </p> <p>Si vous souhaitez conserver la trace de l&acute;ensemble des informations et contributions que vous avez publié sur la Plateforme, vous pouvez demander une copie téléchargeable de ces dernières sous forme de tableau, dans l&acute;onglet « Données » des paramètres, en cliquant sur « Export ». </p> <p><span style="font-weight:700;">QUELS SONT VOS DROITS ? COMMENT LES EXERCER ?</span></p> <p><span style="font-weight:700;">La Plateforme est conçue pour vous permettre de gérer directement vos données personnelles. </span></p> <p>Après vous êtes inscrit sur la Plateforme, <span style="font-weight:700;">vous pouvez déréférencer votre profil public des résultats des moteurs de recherche</span> depuis les paramètres de votre compte (case à cocher dans l&acute;onglet « profil » des paramètres du compte). </p> <p><span style="font-weight:700;">Si vous ne souhaitez pas être identifiable</span> nous vous recommandons de participer sous un pseudonyme, de ne pas afficher votre visage en photo de profil, ni de fournir des liens vers vos comptes personnels sur les réseaux sociaux ou tout élément qui permettrait de vous identifier directement ou indirectement. Enfin, nous vous rappelons que vos codes d&acute;accès à cette plateforme sont personnels, uniques, confidentiels, incessibles et intransmissibles. Ils ne sauraient être partagés, cédés, revendus, ou retransmis. </p> <p>Dans les limites prévues par la loi, vous pouvez accéder et obtenir copie des données vous concernant, vous opposer au traitement de ces données, les faire rectifier ou les faire effacer. Un lien de désinscription vous sera proposé dans toute communication n&acute;ayant pas un caractère purement administratif. Vous disposez également d&acute;un droit à la limitation du traitement de vos données. </p> <p>Les demandes d&acute;exercice de vos droits peuvent être adressées au responsable de traitement par voie postale (<span style="background-color:yellow;">INDIQUER L&acute;ADRESSE</span>) ou par voie électronique à <span style="background-color:yellow;">INDIQUER EMAIL</span>. </p> <p>Notre délégué à la protection des données personnelles est joignable par email à <span style="background-color:yellow;">INDIQUER EMAIL</span> ou par voie postale (<span style="background-color:yellow;">INDIQUER L&acute;ADRESSE</span>). </p> <p>Si vous estimez après nous avoir contacté que les droits sur vos données n&acute;ont pas été respectés, vous pouvez introduire une réclamation auprès de la CNIL. </p> <p><a href="https://www.cnil.fr/fr/les-droits-pour-maitriser-vos-donnees-personnelles" style="text-decoration-line:none;"><span style="font-size:11pt; font-family:Arial;  ">Voir le site de la CNIL pour plus d&acute;informations sur vos droits.</span></a> </p> <p><span style="font-weight:700;">NOS ENGAGEMENTS EN MATIERE DE SOUS-TRAITANCE, COMMUNICATION ET TRANSFERTS DE DONNEES</span></p> <p><span style="font-weight:700;">Vos données nominatives sont à usage interne, elles sont strictement confidentielles et ne peuvent être divulguées à des tiers, sauf en cas d&acute;accord exprès ou si vous avez décidé de les rendre publiques. </span></p> <p>En cas de communication de vos données personnelles à un tiers, quelle que soit sa qualité, nous nous assurerons préalablement que ce dernier est tenu d&acute;appliquer des conditions de confidentialité identiques aux nôtres. </p> <p>Nous nous engageons à (i) ce que tout sous-traitant, dont Cap Collectif propulsant la Plateforme présente des garanties contractuelles suffisantes et appropriées pour respecter vos droits, afin que le traitement réponde aux exigences du RGPD et (ii) à respecter les <a href="https://www.cnil.fr/fr/reglement-europeen-protection-donnees/chapitre5" style="text-decoration-line:none;"><span style="font-size:11pt; font-family:Arial;  ">dispositions</span></a> du RGPD applicables aux transferts des données. </p> <p>Sur la base de nos obligations légales, vos données personnelles pourront être divulguées en application d&acute;une loi, d&acute;un règlement ou en vertu d&acute;une décision d&acute;une autorité réglementaire ou judiciaire compétente. </p> <p><span style="background-color:yellow;">Dans une logique d&acute;ouverture en ligne des informations détenues par les acteurs publics et privés, vos contributions publiques pourront, </span><span style="background-color:yellow; font-weight:700;text-decoration-line:underline; text-decoration-skip-ink:none; vertical-align:baseline; white-space:pre-wrap;">après anonymisation</span><span style="background-color:yellow;">, être consultées par via l&acute;API (URL) et/ou la page « données publiques » (URL).</span></p> <p><span style="font-weight:700;">INDICATIONS EN CAS DE VIOLATION DE DONNÉES</span></p> <p>Nous nous engageons à mettre en œuvre toutes les mesures techniques et organisationnelles appropriées grâce à des moyens de sécurisation physiques et logistiques permettant de garantir un niveau de sécurité adapté au regard des risques d&acute;accès accidentels, non autorisés ou illégaux, de divulgation, d&acute;altération, de perte ou encore de destruction des données personnelles vous concernant.</span> </p> <p>Dans l&acute;éventualité où nous prendrions connaissance d&acute;un accès illégal aux données personnelles vous concernant, stockées sur nos serveurs ou ceux de nos prestataires, ou d&acute;un accès non autorisé ayant pour conséquence la réalisation des risques identifiés ci-dessus, nous nous engageons à :</span><br /> </p> <ul style="list-style-type:disc; font-size:11pt; font-family:Calibri, sans-serif; color:black; vertical-align:baseline; white-space:pre; margin-left:-10.9pt; line-height:1.2; margin-top:0pt; margin-bottom:0pt; text-align:justify;"><li>Vous notifier l&acute;incident dans les plus brefs délais si cela est susceptible d&acute;engendrer un risque élevé pour vos droits et libertés ;</li><li>Examiner les causes de l&acute;incident ;</li><li>Prendre les mesures nécessaires dans la limite du raisonnable afin d&acute;amoindrir les effets négatifs et préjudices pouvant résulter dudit incident.</li> </ul> <p>En aucun cas les engagements définis au point ci-dessus ne peuvent être assimilés à une quelconque reconnaissance de faute ou de responsabilité quant à la survenance de l&acute;incident en question.</p></div></div></p>',
                'category' => 'pages.privacy',
                'position' => 4,
                'type' => SiteParameter::$types['rich_text'],
            ],
            [
                'keyname' => 'legal-mentions',
                'value' =>
                    '<div style="font-size:11pt;font-family:&acute;Open Sans&acute;,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;"><h2 dir="ltr" style="line-height:1.2;margin-top:30pt;margin-bottom:15pt;"><b><span>Éditeur</span></b></h2><p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:10pt;"><span style="font-size:11pt;font-family:&acute;Open Sans&acute;,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;">En attente</span></p><h2 dir="ltr" style="line-height:1.2;margin-top:30pt;margin-bottom:15pt;"><b>Directeur de la publication </b></h2><p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:10pt;"><span style="font-size:11pt;font-family:&acute;Open Sans&acute;,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;">En attente</span></p><h2 dir="ltr" style="line-height:1.2;margin-top:30pt;margin-bottom:15pt;"><b>Conception et réalisation du site </b></h2><p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:10pt;"><a href="https://cap-collectif.com/" style="text-decoration:none;"><span style="font-size:11pt;font-family:&acute;Open Sans&acute;,sans-serif;color:#000000;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;">Cap Collectif</span></a><br /><span >25 rue Claude Tillier</span><br /><span >75012 Paris</span><br /><span >France</span></p><h2 dir="ltr" style="line-height:1.2;margin-top:30pt;margin-bottom:15pt;"><b>Hébergement</b></h2><p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:10pt;"><span >Assuré par </span><a href="https://www.ovh.com/fr/" style="text-decoration:none;"><span style="font-size:11pt;font-family:&acute;Open Sans&acute;,sans-serif;color:#1155cc;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:underline;-webkit-text-decoration-skip:none;text-decoration-skip-ink:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;">OVH</span></a></p><p><span >SAS OVH,</span><br /><span >2 rue Kellermann</span><br /><span >59100 Roubaix</span><br /><span >France</span></p></div>',
                'category' => 'pages.legal',
                'position' => 5,
                'type' => SiteParameter::$types['rich_text'],
            ],
        ];

        foreach ($newParameters as $values) {
            /** @var Query $query */
            $query = $em->createQuery(
                'SELECT sp.id, sp.value FROM Capco\\AppBundle\\Entity\\SiteParameter sp WHERE sp.keyname = :keyname'
            );
            $query->setParameter('keyname', $values['keyname']);
            $param = $query->getOneOrNullResult();

            if (null == $param) {
                $this->connection->insert('site_parameter', $values);
            } elseif ($param && empty($param['value'])) {
                $this->connection->update(
                    'site_parameter',
                    ['value' => $values['value']],
                    ['keyname' => $values['keyname']]
                );
            }
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    public function postDown(Schema $schema)
    {
    }
}
