<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest;

use function rtrim;
use function str_replace;

class TestInfo
{
    public const PROFILE_BASIC_CODE = 'code-basic';

    public const PROFILE_IMPLICIT_IDTOKEN = 'id_token-implicit';

    public const PROFILE_IMPLICIT_IDTOKEN_TOKEN = 'id_token+token-hybrid';

    public const PROFILE_HYBRID_CODE_IDTOKEN = 'code+id_token-hybrid';

    public const PROFILE_HYBRID_CODE_IDTOKEN_TOKEN = 'code+id_token+token-hybrid';

    public const PROFILE_HYBRID_CODE_TOKEN = 'code+token-hybrid';

    public const PROFILE_CONFIGURATION = 'configuration';

    public const PROFILE_DYNAMIC = 'dynamic';

    /** @var string */
    private $profile;

    /** @var string */
    private $responseType;

    /** @var string */
    private $rpId;

    /** @var string */
    private $root;

    /**
     * TestInfo constructor.
     */
    public function __construct(
        string $profile,
        string $responseType = 'code',
        string $baseRpId = 'tmv_php-openid-client',
        string $root = 'https://rp.certification.openid.net:8080/'
    ) {
        $this->profile = $profile;
        $this->responseType = $responseType;
        $this->rpId = $baseRpId;
        $this->root = rtrim($root, '/');
    }

    public function getProfile(): string
    {
        return $this->profile;
    }

    public function getResponseType(): string
    {
        return $this->responseType;
    }

    public function getRpId(): string
    {
        return $this->rpId . '.' . str_replace(' ', '_', $this->getResponseType());
    }

    public function getRoot(): string
    {
        return $this->root;
    }

    public function getRpUri(): string
    {
        return $this->getRoot() . '/' . $this->getRpId();
    }

    public function getRpLogsUri(): string
    {
        return $this->getRoot() . '/log/' . $this->getRpId();
    }
}
