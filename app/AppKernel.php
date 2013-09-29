<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Taskul\UserBundle\UserBundle(),
            new FOS\FacebookBundle\FOSFacebookBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Taskul\TaskBundle\TaskBundle(),
            new PunkAve\FileUploaderBundle\PunkAveFileUploaderBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new FOS\MessageBundle\FOSMessageBundle(),
            new Taskul\MessageBundle\MessageBundle(),
            new Taskul\MainBundle\MainBundle(),
            new Taskul\FriendBundle\FriendBundle(),
            new FPN\TagBundle\FPNTagBundle(),
            new Taskul\TagBundle\TagBundle(),
            new Taskul\FileBundle\FileBundle(),
            new Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),
            new Fresh\Bundle\DoctrineEnumBundle\FreshDoctrineEnumBundle(),
            new APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Taskul\CommentBundle\TaskulCommentBundle(),
            new Spy\TimelineBundle\SpyTimelineBundle(),
            new Taskul\TimelineBundle\TimelineBundle(),
            new Ornicar\GravatarBundle\OrnicarGravatarBundle(),
            new Ornicar\AkismetBundle\OrnicarAkismetBundle(),
            new Lexik\Bundle\MailerBundle\LexikMailerBundle(),
            // Sonata
            // new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            // Locales
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),

            // not required, but recommended for better extraction
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Ornicar\ApcBundle\OrnicarApcBundle(),
            new Taskul\FacebookBundle\TaskulFacebookBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
