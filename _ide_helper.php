<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 12.19.3.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */
namespace Laravel\Socialite\Facades {
    /**
     */
    class Socialite {
        /**
         * Get a driver instance.
         *
         * @param string $driver
         * @return mixed
         * @static
         */
        public static function with($driver)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->with($driver);
        }

        /**
         * Build an OAuth 2 provider instance.
         *
         * @param string $provider
         * @param array $config
         * @return \Laravel\Socialite\Two\AbstractProvider
         * @static
         */
        public static function buildProvider($provider, $config)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->buildProvider($provider, $config);
        }

        /**
         * Format the server configuration.
         *
         * @param array $config
         * @return array
         * @static
         */
        public static function formatConfig($config)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->formatConfig($config);
        }

        /**
         * Forget all of the resolved driver instances.
         *
         * @return \Laravel\Socialite\SocialiteManager
         * @static
         */
        public static function forgetDrivers()
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->forgetDrivers();
        }

        /**
         * Set the container instance used by the manager.
         *
         * @param \Illuminate\Contracts\Container\Container $container
         * @return \Laravel\Socialite\SocialiteManager
         * @static
         */
        public static function setContainer($container)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->setContainer($container);
        }

        /**
         * Get the default driver name.
         *
         * @return string
         * @throws \InvalidArgumentException
         * @static
         */
        public static function getDefaultDriver()
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->getDefaultDriver();
        }

        /**
         * Get a driver instance.
         *
         * @param string|null $driver
         * @return mixed
         * @throws \InvalidArgumentException
         * @static
         */
        public static function driver($driver = null)
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->driver($driver);
        }

        /**
         * Register a custom driver creator Closure.
         *
         * @param string $driver
         * @param \Closure $callback
         * @return \Laravel\Socialite\SocialiteManager
         * @static
         */
        public static function extend($driver, $callback)
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->extend($driver, $callback);
        }

        /**
         * Get all of the created "drivers".
         *
         * @return array
         * @static
         */
        public static function getDrivers()
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->getDrivers();
        }

        /**
         * Get the container instance used by the manager.
         *
         * @return \Illuminate\Contracts\Container\Container
         * @static
         */
        public static function getContainer()
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->getContainer();
        }

            }
    }

namespace Jenssegers\Agent\Facades {
    /**
     */
    class Agent {
        /**
         * Get all detection rules. These rules include the additional
         * platforms and browsers and utilities.
         *
         * @return array
         * @static
         */
        public static function getDetectionRulesExtended()
        {
            return \Jenssegers\Agent\Agent::getDetectionRulesExtended();
        }

        /**
         * @static
         */
        public static function getRules()
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getRules();
        }

        /**
         * @return \Jaybizzle\CrawlerDetect\CrawlerDetect
         * @static
         */
        public static function getCrawlerDetect()
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getCrawlerDetect();
        }

        /**
         * @static
         */
        public static function getBrowsers()
        {
            return \Jenssegers\Agent\Agent::getBrowsers();
        }

        /**
         * @static
         */
        public static function getOperatingSystems()
        {
            return \Jenssegers\Agent\Agent::getOperatingSystems();
        }

        /**
         * @static
         */
        public static function getPlatforms()
        {
            return \Jenssegers\Agent\Agent::getPlatforms();
        }

        /**
         * @static
         */
        public static function getDesktopDevices()
        {
            return \Jenssegers\Agent\Agent::getDesktopDevices();
        }

        /**
         * @static
         */
        public static function getProperties()
        {
            return \Jenssegers\Agent\Agent::getProperties();
        }

        /**
         * Get accept languages.
         *
         * @param string $acceptLanguage
         * @return array
         * @static
         */
        public static function languages($acceptLanguage = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->languages($acceptLanguage);
        }

        /**
         * Get the browser name.
         *
         * @param string|null $userAgent
         * @return string|bool
         * @static
         */
        public static function browser($userAgent = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->browser($userAgent);
        }

        /**
         * Get the platform name.
         *
         * @param string|null $userAgent
         * @return string|bool
         * @static
         */
        public static function platform($userAgent = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->platform($userAgent);
        }

        /**
         * Get the device name.
         *
         * @param string|null $userAgent
         * @return string|bool
         * @static
         */
        public static function device($userAgent = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->device($userAgent);
        }

        /**
         * Check if the device is a desktop computer.
         *
         * @param string|null $userAgent deprecated
         * @param array $httpHeaders deprecated
         * @return bool
         * @static
         */
        public static function isDesktop($userAgent = null, $httpHeaders = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->isDesktop($userAgent, $httpHeaders);
        }

        /**
         * Check if the device is a mobile phone.
         *
         * @param string|null $userAgent deprecated
         * @param array $httpHeaders deprecated
         * @return bool
         * @static
         */
        public static function isPhone($userAgent = null, $httpHeaders = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->isPhone($userAgent, $httpHeaders);
        }

        /**
         * Get the robot name.
         *
         * @param string|null $userAgent
         * @return string|bool
         * @static
         */
        public static function robot($userAgent = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->robot($userAgent);
        }

        /**
         * Check if device is a robot.
         *
         * @param string|null $userAgent
         * @return bool
         * @static
         */
        public static function isRobot($userAgent = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->isRobot($userAgent);
        }

        /**
         * Get the device type
         *
         * @param null $userAgent
         * @param null $httpHeaders
         * @return string
         * @static
         */
        public static function deviceType($userAgent = null, $httpHeaders = null)
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->deviceType($userAgent, $httpHeaders);
        }

        /**
         * @static
         */
        public static function version($propertyName, $type = 'text')
        {
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->version($propertyName, $type);
        }

        /**
         * Get the current script version.
         *
         * This is useful for the demo.php file,
         * so people can check on what version they are testing
         * for mobile devices.
         *
         * @return string The version number in semantic version format.
         * @static
         */
        public static function getScriptVersion()
        {
            //Method inherited from \Mobile_Detect 
            return \Jenssegers\Agent\Agent::getScriptVersion();
        }

        /**
         * Set the HTTP Headers. Must be PHP-flavored. This method will reset existing headers.
         *
         * @param array $httpHeaders The headers to set. If null, then using PHP's _SERVER to extract
         *                           the headers. The default null is left for backwards compatibility.
         * @static
         */
        public static function setHttpHeaders($httpHeaders = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->setHttpHeaders($httpHeaders);
        }

        /**
         * Retrieves the HTTP headers.
         *
         * @return array
         * @static
         */
        public static function getHttpHeaders()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getHttpHeaders();
        }

        /**
         * Retrieves a particular header. If it doesn't exist, no exception/error is caused.
         *
         * Simply null is returned.
         *
         * @param string $header The name of the header to retrieve. Can be HTTP compliant such as
         *                       "User-Agent" or "X-Device-User-Agent" or can be php-esque with the
         *                       all-caps, HTTP_ prefixed, underscore separated awesomeness.
         * @return string|null The value of the header.
         * @static
         */
        public static function getHttpHeader($header)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getHttpHeader($header);
        }

        /**
         * @static
         */
        public static function getMobileHeaders()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getMobileHeaders();
        }

        /**
         * Get all possible HTTP headers that
         * can contain the User-Agent string.
         *
         * @return array List of HTTP headers.
         * @static
         */
        public static function getUaHttpHeaders()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getUaHttpHeaders();
        }

        /**
         * Set CloudFront headers
         * http://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/header-caching.html#header-caching-web-device
         *
         * @param array $cfHeaders List of HTTP headers
         * @return boolean If there were CloudFront headers to be set
         * @static
         */
        public static function setCfHeaders($cfHeaders = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->setCfHeaders($cfHeaders);
        }

        /**
         * Retrieves the cloudfront headers.
         *
         * @return array
         * @static
         */
        public static function getCfHeaders()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getCfHeaders();
        }

        /**
         * Set the User-Agent to be used.
         *
         * @param string $userAgent The user agent string to set.
         * @return string|null
         * @static
         */
        public static function setUserAgent($userAgent = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->setUserAgent($userAgent);
        }

        /**
         * Retrieve the User-Agent.
         *
         * @return string|null The user agent if it's set.
         * @static
         */
        public static function getUserAgent()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getUserAgent();
        }

        /**
         * Set the detection type. Must be one of self::DETECTION_TYPE_MOBILE or
         * self::DETECTION_TYPE_EXTENDED. Otherwise, nothing is set.
         *
         * @deprecated since version 2.6.9
         * @param string $type The type. Must be a self::DETECTION_TYPE_* constant. The default
         *                     parameter is null which will default to self::DETECTION_TYPE_MOBILE.
         * @static
         */
        public static function setDetectionType($type = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->setDetectionType($type);
        }

        /**
         * @static
         */
        public static function getMatchingRegex()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getMatchingRegex();
        }

        /**
         * @static
         */
        public static function getMatchesArray()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getMatchesArray();
        }

        /**
         * Retrieve the list of known phone devices.
         *
         * @return array List of phone devices.
         * @static
         */
        public static function getPhoneDevices()
        {
            //Method inherited from \Mobile_Detect 
            return \Jenssegers\Agent\Agent::getPhoneDevices();
        }

        /**
         * Retrieve the list of known tablet devices.
         *
         * @return array List of tablet devices.
         * @static
         */
        public static function getTabletDevices()
        {
            //Method inherited from \Mobile_Detect 
            return \Jenssegers\Agent\Agent::getTabletDevices();
        }

        /**
         * Alias for getBrowsers() method.
         *
         * @return array List of user agents.
         * @static
         */
        public static function getUserAgents()
        {
            //Method inherited from \Mobile_Detect 
            return \Jenssegers\Agent\Agent::getUserAgents();
        }

        /**
         * Retrieve the list of known utilities.
         *
         * @return array List of utilities.
         * @static
         */
        public static function getUtilities()
        {
            //Method inherited from \Mobile_Detect 
            return \Jenssegers\Agent\Agent::getUtilities();
        }

        /**
         * Method gets the mobile detection rules. This method is used for the magic methods $detect->is*().
         *
         * @deprecated since version 2.6.9
         * @return array All the rules (but not extended).
         * @static
         */
        public static function getMobileDetectionRules()
        {
            //Method inherited from \Mobile_Detect 
            return \Jenssegers\Agent\Agent::getMobileDetectionRules();
        }

        /**
         * Method gets the mobile detection rules + utilities.
         *
         * The reason this is separate is because utilities rules
         * don't necessary imply mobile. This method is used inside
         * the new $detect->is('stuff') method.
         *
         * @deprecated since version 2.6.9
         * @return array All the rules + extended.
         * @static
         */
        public static function getMobileDetectionRulesExtended()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->getMobileDetectionRulesExtended();
        }

        /**
         * Check the HTTP headers for signs of mobile.
         *
         * This is the fastest mobile check possible; it's used
         * inside isMobile() method.
         *
         * @return bool
         * @static
         */
        public static function checkHttpHeadersForMobile()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->checkHttpHeadersForMobile();
        }

        /**
         * Check if the device is mobile.
         *
         * Returns true if any type of mobile device detected, including special ones
         *
         * @param null $userAgent deprecated
         * @param null $httpHeaders deprecated
         * @return bool
         * @static
         */
        public static function isMobile($userAgent = null, $httpHeaders = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->isMobile($userAgent, $httpHeaders);
        }

        /**
         * Check if the device is a tablet.
         *
         * Return true if any type of tablet device is detected.
         *
         * @param string $userAgent deprecated
         * @param array $httpHeaders deprecated
         * @return bool
         * @static
         */
        public static function isTablet($userAgent = null, $httpHeaders = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->isTablet($userAgent, $httpHeaders);
        }

        /**
         * This method checks for a certain property in the
         * userAgent.
         *
         * @todo : The httpHeaders part is not yet used.
         * @param string $key
         * @param string $userAgent deprecated
         * @param string $httpHeaders deprecated
         * @return bool|int|null
         * @static
         */
        public static function is($key, $userAgent = null, $httpHeaders = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->is($key, $userAgent, $httpHeaders);
        }

        /**
         * Some detection rules are relative (not standard),
         * because of the diversity of devices, vendors and
         * their conventions in representing the User-Agent or
         * the HTTP headers.
         *
         * This method will be used to check custom regexes against
         * the User-Agent string.
         *
         * @param $regex
         * @param string $userAgent
         * @return bool
         * @todo : search in the HTTP headers too.
         * @static
         */
        public static function match($regex, $userAgent = null)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->match($regex, $userAgent);
        }

        /**
         * Prepare the version number.
         *
         * @todo Remove the error supression from str_replace() call.
         * @param string $ver The string version, like "2.6.21.2152";
         * @return float
         * @static
         */
        public static function prepareVersionNo($ver)
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->prepareVersionNo($ver);
        }

        /**
         * Retrieve the mobile grading, using self::MOBILE_GRADE_* constants.
         *
         * @deprecated This is no longer being maintained, it was an experiment at the time.
         * @return string One of the self::MOBILE_GRADE_* constants.
         * @static
         */
        public static function mobileGrade()
        {
            //Method inherited from \Mobile_Detect 
            /** @var \Jenssegers\Agent\Agent $instance */
            return $instance->mobileGrade();
        }

            }
    }

namespace Illuminate\Http {
    /**
     */
    class Request extends \Symfony\Component\HttpFoundation\Request {
        /**
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param array $rules
         * @param mixed $params
         * @static
         */
        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        /**
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param string $errorBag
         * @param array $rules
         * @param mixed $params
         * @static
         */
        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        /**
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $absolute
         * @static
         */
        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        /**
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @static
         */
        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        /**
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @param mixed $absolute
         * @static
         */
        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        /**
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @static
         */
        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

            }
    }


namespace  {
    class Socialite extends \Laravel\Socialite\Facades\Socialite {}
    class Agent extends \Jenssegers\Agent\Facades\Agent {}
}





