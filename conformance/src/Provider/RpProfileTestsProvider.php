<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\Provider;

use function array_keys;
use function array_map;
use Facile\OpenIDClient\ConformanceTest\RpTest;
use Facile\OpenIDClient\ConformanceTest\RpTest\RpTestInterface;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class RpProfileTestsProvider
{
    /** @var ContainerInterface */
    private $container;

    /** @var array<string, string> */
    private static $responseTypeMap = [
        TestInfo::PROFILE_BASIC_CODE => 'code',
        TestInfo::PROFILE_IMPLICIT_IDTOKEN => 'id_token',
        TestInfo::PROFILE_IMPLICIT_IDTOKEN_TOKEN => 'id_token token',
        TestInfo::PROFILE_HYBRID_CODE_IDTOKEN => 'code id_token',
        TestInfo::PROFILE_HYBRID_CODE_TOKEN => 'code token',
        TestInfo::PROFILE_HYBRID_CODE_IDTOKEN_TOKEN => 'code id_token token',
        TestInfo::PROFILE_CONFIGURATION => 'code',
        TestInfo::PROFILE_DYNAMIC => 'code',
    ];

    private static $testMap = [
        TestInfo::PROFILE_BASIC_CODE => [
            RpTest\ResponseTypeAndResponseMode\RPResponseTypeCodeTest::class,
            RpTest\ScopeRequestParameter\RpScopeUserinfoClaimsTest::class,
            RpTest\NonceRequestParameter\RpNonceInvalidTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretBasicTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentSingleJwksTest::class,
            RpTest\IdToken\RpIdTokenIatTest::class,
            RpTest\IdToken\RpIdTokenAudTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentMultipleJwksTest::class,
            RpTest\IdToken\RpIdTokenSigNoneTest::class,
            RpTest\IdToken\RpIdTokenSigRS256Test::class,
            RpTest\IdToken\RpIdTokenSubTest::class,
            RpTest\IdToken\RpIdTokenBadSigRS256Test::class,
            RpTest\IdToken\RpIdTokenIssuerMismatchTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBadSubClaimTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerHeaderTest::class,
            // optional
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            RpTest\Discovery\RPDiscoveryWebFingerUnknownMember::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // Response Type And Response Mode
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostErrorTest::class,
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostTest::class,
            // Client Authentication
            RpTest\ClientAuthentication\RpTokenEndpointPrivateKeyJwtTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretJwtTest::class,
            // ID Token
            RpTest\IdToken\RpIdTokenSigEncTest::class,
            RpTest\IdToken\RpIdTokenSigHS256Test::class,
            RpTest\IdToken\RpIdTokenSigES256Test::class,
            RpTest\IdToken\RpIdTokenSigEncA128KWTest::class,
            RpTest\IdToken\RpIdTokenBadSigHS256Test::class,
            RpTest\IdToken\RpIdTokenBadSigES256Test::class,
            // UserInfo Endpoint
            RpTest\UserInfoEndpoint\RPUserInfoSigTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerBodyTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoSigEncTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoEncTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
            RpTest\KeyRotation\RPKeyRotationOPEncKeyTest::class,
            // Claims Types
            RpTest\ClaimsTypes\RPClaimsDistributedTest::class,
            RpTest\ClaimsTypes\RPClaimsAggregatedTest::class,
        ],
        TestInfo::PROFILE_HYBRID_CODE_IDTOKEN => [
            RpTest\ResponseTypeAndResponseMode\RPResponseTypeCodeIdTokenTest::class,
            RpTest\ScopeRequestParameter\RpScopeUserinfoClaimsTest::class,
            RpTest\NonceRequestParameter\RpNonceInvalidTest::class,
            RpTest\NonceRequestParameter\RpNonceUnlessCodeFlowTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretBasicTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentSingleJwksTest::class,
            RpTest\IdToken\RpIdTokenIatTest::class,
            RpTest\IdToken\RpIdTokenAudTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentMultipleJwksTest::class,
            RpTest\IdToken\RpIdTokenMissingCHashTest::class,
            RpTest\IdToken\RpIdTokenSigRS256Test::class,
            RpTest\IdToken\RpIdTokenSubTest::class,
            RpTest\IdToken\RpIdTokenBadCHashTest::class,
            RpTest\IdToken\RpIdTokenBadSigRS256Test::class,
            RpTest\IdToken\RpIdTokenIssuerMismatchTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBadSubClaimTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerHeaderTest::class,
            // optional
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            RpTest\Discovery\RPDiscoveryWebFingerUnknownMember::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // Response Type And Response Mode
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostErrorTest::class,
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostTest::class,
            // request_uri Request Parameter
            // - rp-request_uri-enc
            // - rp-request_uri-sig
            // - rp-request_uri-sig+enc
            // - rp-request_uri-unsigned
            // Client Authentication
            // - rp-token_endpoint-client_secret_post
            RpTest\ClientAuthentication\RpTokenEndpointPrivateKeyJwtTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretJwtTest::class,
            // ID Token
            RpTest\IdToken\RpIdTokenSigEncTest::class,
            RpTest\IdToken\RpIdTokenSigHS256Test::class,
            RpTest\IdToken\RpIdTokenSigES256Test::class,
            RpTest\IdToken\RpIdTokenSigEncA128KWTest::class,
            RpTest\IdToken\RpIdTokenBadSigHS256Test::class,
            RpTest\IdToken\RpIdTokenBadSigES256Test::class,
            // UserInfo Endpoint
            RpTest\UserInfoEndpoint\RPUserInfoSigTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerBodyTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoSigEncTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoEncTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
            RpTest\KeyRotation\RPKeyRotationOPEncKeyTest::class,
            // Claims Types
            RpTest\ClaimsTypes\RPClaimsDistributedTest::class,
            RpTest\ClaimsTypes\RPClaimsAggregatedTest::class,
        ],
        TestInfo::PROFILE_HYBRID_CODE_IDTOKEN_TOKEN => [
            RpTest\ResponseTypeAndResponseMode\RPResponseTypeCodeIdTokenTokenTest::class,
            RpTest\ScopeRequestParameter\RpScopeUserinfoClaimsTest::class,
            RpTest\NonceRequestParameter\RpNonceInvalidTest::class,
            RpTest\NonceRequestParameter\RpNonceUnlessCodeFlowTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretBasicTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentSingleJwksTest::class,
            RpTest\IdToken\RpIdTokenIatTest::class,
            RpTest\IdToken\RpIdTokenAudTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentMultipleJwksTest::class,
            RpTest\IdToken\RpIdTokenMissingCHashTest::class,
            RpTest\IdToken\RpIdTokenMissingAtHashTest::class,
            RpTest\IdToken\RpIdTokenSigRS256Test::class,
            RpTest\IdToken\RPIdTokenBadAtHashTest::class,
            RpTest\IdToken\RpIdTokenSubTest::class,
            RpTest\IdToken\RpIdTokenBadCHashTest::class,
            RpTest\IdToken\RpIdTokenBadSigRS256Test::class,
            RpTest\IdToken\RpIdTokenIssuerMismatchTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBadSubClaimTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerHeaderTest::class,
            // optional
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            RpTest\Discovery\RPDiscoveryWebFingerUnknownMember::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // Response Type And Response Mode
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostErrorTest::class,
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostTest::class,
            // request_uri Request Parameter
            // - rp-request_uri-enc
            // - rp-request_uri-sig
            // - rp-request_uri-sig+enc
            // - rp-request_uri-unsigned
            // Client Authentication
            // - rp-token_endpoint-client_secret_post
            RpTest\ClientAuthentication\RpTokenEndpointPrivateKeyJwtTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretJwtTest::class,
            // ID Token
            RpTest\IdToken\RpIdTokenSigEncTest::class,
            RpTest\IdToken\RpIdTokenSigHS256Test::class,
            RpTest\IdToken\RpIdTokenSigES256Test::class,
            RpTest\IdToken\RpIdTokenSigEncA128KWTest::class,
            RpTest\IdToken\RpIdTokenBadSigHS256Test::class,
            RpTest\IdToken\RpIdTokenBadSigES256Test::class,
            // UserInfo Endpoint
            RpTest\UserInfoEndpoint\RPUserInfoSigTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerBodyTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoSigEncTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoEncTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
            RpTest\KeyRotation\RPKeyRotationOPEncKeyTest::class,
            // Claims Types
            RpTest\ClaimsTypes\RPClaimsDistributedTest::class,
            RpTest\ClaimsTypes\RPClaimsAggregatedTest::class,
        ],
        TestInfo::PROFILE_HYBRID_CODE_TOKEN => [
            RpTest\ResponseTypeAndResponseMode\RPResponseTypeCodeTokenTest::class,
            RpTest\ScopeRequestParameter\RpScopeUserinfoClaimsTest::class,
            RpTest\NonceRequestParameter\RpNonceInvalidTest::class,
            RpTest\NonceRequestParameter\RpNonceUnlessCodeFlowTest::class,
            RpTest\ClientAuthentication\RpTokenEndpointClientSecretBasicTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentSingleJwksTest::class,
            RpTest\IdToken\RpIdTokenIatTest::class,
            RpTest\IdToken\RpIdTokenAudTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentMultipleJwksTest::class,
            RpTest\IdToken\RpIdTokenSigRS256Test::class,
            RpTest\IdToken\RpIdTokenSubTest::class,
            RpTest\IdToken\RpIdTokenBadSigRS256Test::class,
            RpTest\IdToken\RpIdTokenIssuerMismatchTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBadSubClaimTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerHeaderTest::class,
            // optional
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            RpTest\Discovery\RPDiscoveryWebFingerUnknownMember::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // Response Type And Response Mode
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostErrorTest::class,
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostTest::class,
            // request_uri Request Parameter
            // - rp-request_uri-enc
            // - rp-request_uri-sig
            // - rp-request_uri-sig+enc
            // - rp-request_uri-unsigned
            // Client Authentication
            // - rp-token_endpoint-client_secret_post
            // ID Token
            RpTest\IdToken\RpIdTokenSigEncTest::class,
            RpTest\IdToken\RpIdTokenSigHS256Test::class,
            RpTest\IdToken\RpIdTokenSigES256Test::class,
            RpTest\IdToken\RpIdTokenSigEncA128KWTest::class,
            RpTest\IdToken\RpIdTokenBadSigHS256Test::class,
            RpTest\IdToken\RpIdTokenBadSigES256Test::class,
            // UserInfo Endpoint
            RpTest\UserInfoEndpoint\RPUserInfoSigTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerBodyTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoSigEncTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoEncTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
            RpTest\KeyRotation\RPKeyRotationOPEncKeyTest::class,
            // Claims Types
            RpTest\ClaimsTypes\RPClaimsDistributedTest::class,
            RpTest\ClaimsTypes\RPClaimsAggregatedTest::class,
        ],
        TestInfo::PROFILE_IMPLICIT_IDTOKEN => [
            RpTest\ResponseTypeAndResponseMode\RPResponseTypeIdTokenTest::class,
            RpTest\ScopeRequestParameter\RpScopeUserinfoClaimsTest::class,
            RpTest\NonceRequestParameter\RpNonceInvalidTest::class,
            RpTest\NonceRequestParameter\RpNonceUnlessCodeFlowTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentSingleJwksTest::class,
            RpTest\IdToken\RpIdTokenIatTest::class,
            RpTest\IdToken\RpIdTokenAudTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentMultipleJwksTest::class,
            RpTest\IdToken\RpIdTokenSigRS256Test::class,
            RpTest\IdToken\RpIdTokenSubTest::class,
            RpTest\IdToken\RpIdTokenBadSigRS256Test::class,
            RpTest\IdToken\RpIdTokenIssuerMismatchTest::class,
            // optional
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            RpTest\Discovery\RPDiscoveryWebFingerUnknownMember::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // Response Type And Response Mode
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostErrorTest::class,
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostTest::class,
            // request_uri Request Parameter
            // - rp-request_uri-enc
            // - rp-request_uri-sig
            // - rp-request_uri-sig+enc
            // - rp-request_uri-unsigned
            // Client Authentication
            // - rp-token_endpoint-client_secret_post
            // ID Token
            RpTest\IdToken\RpIdTokenSigEncTest::class,
            RpTest\IdToken\RpIdTokenSigHS256Test::class,
            RpTest\IdToken\RpIdTokenSigES256Test::class,
            RpTest\IdToken\RpIdTokenSigEncA128KWTest::class,
            RpTest\IdToken\RpIdTokenBadSigHS256Test::class,
            RpTest\IdToken\RpIdTokenBadSigES256Test::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
            RpTest\KeyRotation\RPKeyRotationOPEncKeyTest::class,
        ],
        TestInfo::PROFILE_IMPLICIT_IDTOKEN_TOKEN => [
            RpTest\ResponseTypeAndResponseMode\RPResponseTypeIdTokenTokenTest::class,
            RpTest\ScopeRequestParameter\RpScopeUserinfoClaimsTest::class,
            RpTest\NonceRequestParameter\RpNonceInvalidTest::class,
            RpTest\NonceRequestParameter\RpNonceUnlessCodeFlowTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentSingleJwksTest::class,
            RpTest\IdToken\RpIdTokenIatTest::class,
            RpTest\IdToken\RpIdTokenAudTest::class,
            RpTest\IdToken\RpIdTokenKidAbsentMultipleJwksTest::class,
            RpTest\IdToken\RpIdTokenMissingAtHashTest::class,
            RpTest\IdToken\RpIdTokenSigRS256Test::class,
            RpTest\IdToken\RPIdTokenBadAtHashTest::class,
            RpTest\IdToken\RpIdTokenSubTest::class,
            RpTest\IdToken\RpIdTokenBadSigRS256Test::class,
            RpTest\IdToken\RpIdTokenIssuerMismatchTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBadSubClaimTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerHeaderTest::class,
            // optional
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            RpTest\Discovery\RPDiscoveryWebFingerUnknownMember::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // Response Type And Response Mode
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostErrorTest::class,
            RpTest\ResponseTypeAndResponseMode\RPResponseModeFormPostTest::class,
            // request_uri Request Parameter
            // - rp-request_uri-enc
            // - rp-request_uri-sig
            // - rp-request_uri-sig+enc
            // - rp-request_uri-unsigned
            // Client Authentication
            // - rp-token_endpoint-client_secret_post
            // ID Token
            RpTest\IdToken\RpIdTokenSigEncTest::class,
            RpTest\IdToken\RpIdTokenSigHS256Test::class,
            RpTest\IdToken\RpIdTokenSigES256Test::class,
            RpTest\IdToken\RpIdTokenSigEncA128KWTest::class,
            RpTest\IdToken\RpIdTokenBadSigHS256Test::class,
            RpTest\IdToken\RpIdTokenBadSigES256Test::class,
            // UserInfo Endpoint
            RpTest\UserInfoEndpoint\RPUserInfoSigTest::class,
            RpTest\UserInfoEndpoint\RpUserInfoBearerBodyTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoSigEncTest::class,
            RpTest\UserInfoEndpoint\RPUserInfoEncTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
            RpTest\KeyRotation\RPKeyRotationOPEncKeyTest::class,
            // Claims Types
            RpTest\ClaimsTypes\RPClaimsDistributedTest::class,
            RpTest\ClaimsTypes\RPClaimsAggregatedTest::class,
        ],
        TestInfo::PROFILE_CONFIGURATION => [
            // Discovery
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            // ID Token
            RpTest\IdToken\RpIdTokenSigNoneTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
        ],
        TestInfo::PROFILE_DYNAMIC => [
            // Discovery
            RpTest\Discovery\RPDiscoveryWebFingerAcct::class,
            RpTest\Discovery\RPDiscoveryWebFingerUrl::class,
            RpTest\Discovery\RPDiscoveryOpenIdConfiguration::class,
            RpTest\Discovery\RPDiscoveryJwksUriKeys::class,
            RpTest\Discovery\RPDiscoveryIssuerNotMatchingConfig::class,
            // Dynamic client registration
            RpTest\DynamicClientRegistration\RPRegistrationDynamic::class,
            // ID Token
            RpTest\IdToken\RpIdTokenSigNoneTest::class,
            // UserInfo Endpoint
            RpTest\UserInfoEndpoint\RPUserInfoSigTest::class,
            // Key Rotation
            RpTest\KeyRotation\RPKeyRotationOPSignKeyNativeTest::class,
            RpTest\KeyRotation\RPKeyRotationOPSignKeyTest::class,
        ],
    ];

    /**
     * ProfileTestsProvider constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAvailableProfiles(): array
    {
        return array_keys(static::$testMap);
    }

    public function getResponseTypeForProfile(string $profile): string
    {
        $responseType = static::$responseTypeMap[$profile] ?? null;

        if (null === $responseType) {
            throw new InvalidArgumentException('No response type for profile ' . $profile);
        }

        return $responseType;
    }

    /**
     * @return RpTestInterface[]
     */
    public function getTests(string $profile): array
    {
        return array_map([$this->container, 'get'], static::$testMap[$profile] ?? []);
    }
}
