<?php declare(strict_types=1);

namespace Frosh\MailAddressTester\Service;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Util\Hasher;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shyim\CheckIfEmailExists\DNS;
use Shyim\CheckIfEmailExists\SMTP;
use Shyim\CheckIfEmailExists\Syntax;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class Tester
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        #[Autowire('cache.app')]
        private readonly CacheItemPoolInterface $cache,
        private readonly LoggerInterface $froshMailAddressTesterLogger,
    ) {
    }

    public function validateEmail(string $email): bool
    {
        $email = \strtolower($email);

        $mailValidCache = $this->getCacheItem($email);
        $mailValidCacheResult = $mailValidCache->get();
        if (\is_bool($mailValidCacheResult)) {
            return $mailValidCacheResult;
        }

        $syntaxCheck = new Syntax($email);
        if ($syntaxCheck->isValid() === false) {
            return false;
        }

        $domain = $syntaxCheck->domain;

        $domainValidCache = $this->getCacheItem($domain);
        // first check if the domain is already marked as invalid
        if ($domainValidCache->get() === false) {
            return false;
        }

        $mxRecords = (new DNS())->getMxRecords($domain);

        if (empty($mxRecords)) {
            $this->saveCache($domainValidCache, false);

            $this->froshMailAddressTesterLogger->error(\sprintf('Domain %s has no mx records', $domain));

            return false;
        }

        $verifyEmail = $this->systemConfigService->getString('FroshMailAddressTester.config.verifyEmail');

        $smtpCheck = (new SMTP($verifyEmail))->check($domain, $mxRecords, $email);
        $this->saveCache($domainValidCache, $smtpCheck->canConnect);

        if ($smtpCheck->canConnect === false) {
            $this->froshMailAddressTesterLogger->error($smtpCheck->error);

            return false;
        }

        $isValid = $smtpCheck->isDeliverable === true && $smtpCheck->isDisabled === false && $smtpCheck->hasFullInbox === false;

        $this->saveCache($mailValidCache, $isValid);

        if ($isValid === false) {
            $this->froshMailAddressTesterLogger->error(
                \sprintf('Email address "%s" test failed', $email),
                json_decode(json_encode($smtpCheck, \JSON_THROW_ON_ERROR), true, 1, \JSON_THROW_ON_ERROR)
            );
        }

        return $isValid;
    }

    private function getCacheItem(string $value): CacheItemInterface
    {
        $cacheKey = 'frosh_mail_tester_' . Hasher::hash($value);

        return $this->cache->getItem($cacheKey);
    }

    private function saveCache(CacheItemInterface $item, bool $value): void
    {
        $cacheTime = 3600;

        if ($value === true) {
            $cacheTime = 86400; // one day for valid emails
        }

        $item->expiresAfter($cacheTime);
        $item->set($value);
        $this->cache->save($item);
    }
}
