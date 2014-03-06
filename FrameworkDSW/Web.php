<?php
/**
 * \FrameworkDSW\Web
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 52
 */
namespace FrameworkDSW\Web;

use FrameworkDSW\Containers\IList;
use FrameworkDSW\Containers\IMap;
use FrameworkDSW\Containers\TAbstractMap;
use FrameworkDSW\Containers\TLinkedList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\Controller\TControllerAction;
use FrameworkDSW\Controller\TControllerManager;
use FrameworkDSW\Controller\TModelBinder;
use FrameworkDSW\Controller\TViewBinder;
use FrameworkDSW\CoreClasses\IApplication;
use FrameworkDSW\CoreClasses\TComponent;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\Utilities\TType;

/**
 * IUrlRouter
 *
 * @author 许子健
 */
interface IUrlRouter extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\IUrlRouteRule $Rule
     * @param boolean $Append
     */
    public function AddRule($Rule, $Append = true);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Parameters <K: string, V: string>
     * @param string $Equal
     * @param string $Ampersand
     * @return string
     */
    public function CreatePathInfo($Parameters, $Equal = '=', $Ampersand = '&');

    /**
     * descHere
     *
     * @param string $Route
     * @param \FrameworkDSW\Containers\IMap $Parameters <K: string, V: string>
     * @param string $Ampersand
     * @return string
     */
    public function CreateUrl($Route, $Parameters = null, $Ampersand = '&');

    /**
     * descHere
     *
     * @return boolean
     */
    public function getAppendParameters();

    /**
     * descHere
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCaseSensitive();

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCheckParameters();

    /**
     * descHere
     *
     * @return string
     */
    public function getRouteVariableName();

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IMap <K: string, V: IUrlRouteRule>
     */
    public function getRules();

    /**
     * descHere
     *
     * @return boolean
     */
    public function getShowScriptName();

    /**
     * descHere
     *
     * @return \FrameworkDSW\Web\TUrlMode
     */
    public function getUrlMode();

    /**
     * descHere
     *
     * @return string
     */
    public function getUrlSuffix();

    /**
     * descHere
     *
     * @return boolean
     */
    public function getUseStrictParsing();

    /**
     * descHere
     *
     * @param string $PathInfo
     */
    public function ParsePathInfo($PathInfo);

    /**
     * descHere
     *
     * @return string
     */
    public function ParseUrl();

    /**
     * descHere
     *
     * @param string $PathInfo
     * @param string $UrlSuffix
     * @return string
     */
    public function RemoveUrlSuffix($PathInfo, $UrlSuffix);

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setAppendParameters($Value);

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setBaseUrl($Value);

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setCaseSensitive($Value);

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setCheckParameters($Value);

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setRouteVariableName($Value);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Value <K: string, V: \FrameworkDSW\Web\IUrlRouteRule>
     */
    public function setRules($Value);

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setShowScriptName($Value);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\TUrlMode $Value
     */
    public function setUrlMode($Value);

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setUrlSuffix($Value);

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setUseStrictParsing($Value);

    /**
     *
     * @return string
     */
    public function getRequestHostInfo();
}

/**
 * IUrlRouteRule
 *
 * @author 许子健
 */
interface IUrlRouteRule extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\TUrlRouter $Router
     * @param string $Route
     * @param \FrameworkDSW\Containers\TMap $Parameters <K: string, V: string>
     * @param string $Ampersand
     * @return string
     */
    public function CreateUrl($Router, $Route, $Parameters = null, $Ampersand = '&');

    /**
     * descHere
     *
     * @return boolean
     */
    public function getHasHostInfo();

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\TUrlRouter $Router
     * @param \FrameworkDSW\Web\THttpRequest $Request
     * @param string $PathInfo
     * @param string $RawPathInfo
     * @return string
     */
    public function ParseUrl($Router, $Request, $PathInfo, $RawPathInfo);

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setHasHostInfo($Value);
}

/**
 * IHttpSessionStorage
 * @author    许子健
 */
interface IHttpSessionStorage extends IInterface {

    /**
     * descHere
     */
    public function Close();

    /**
     * descHere
     * @param string $SessionId
     */
    public function Delete($SessionId);

    /**
     * descHere
     * @param string $SavePath
     * @param string $Name
     */
    public function Open($SavePath, $Name);

    /**
     * descHere
     * @param string $MaxLifeTime
     */
    public function Purge($MaxLifeTime);

    /**
     * descHere
     * @param string $SessionId
     * @return string
     */
    public function Read($SessionId);

    /**
     * descHere
     * @param string $SessionId
     * @param string $SessionData
     */
    public function Write($SessionId, $SessionData);

}

/**
 * Class ESessionException
 * @package FrameworkDSW\Web
 */
class ESessionException extends EException {
}

/**
 * Class ENoSuchRequestParameter
 * @package FrameworkDSW\Web
 */
class ENoSuchRequestParameter extends EException {
    /**
     * @var string
     */
    private $FName = '';
    /**
     * @var \FrameworkDSW\Web\THttpMethod
     */
    private $FMethod = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $Name
     * @param \FrameworkDSW\Web\THttpMethod $Method
     */
    public function __construct($Message = '', $Previous = null, $Name = '', $Method = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($Name);
        TType::Object($Method, THttpMethod::class);

        $this->FName   = $Name;
        $this->FMethod = $Method;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public function getMethod() {
        return $this->FMethod;
    }
}

/**
 *
 * @author 许子健
 */
class EUnableToResolveScriptUrl extends EException {
}

/**
 *
 * @author 许子健
 */
class EBrowscapNotEnabled extends EException {
}

/**
 *
 * @author 许子健
 */
class EDetermineRequestUriFailed extends EException {
}

/**
 *
 * @author 许子健
 */
class EDeterminePathInfoFailed extends EException {
}

/**
 *
 * @author 许子健
 */
class EParsingOnlyUrlRule extends EException {
}

/**
 *
 * @author 许子健
 */
class ECreateUrlFailed extends EException {
}

/**
 *
 * @author 子健
 */
class EParseUrlFailed extends EException {
}

/**
 *
 * @author 许子健
 */
class EHttpException extends EException {
    /**
     *
     * @var integer
     */
    private $FStatusCode = -1;

    /**
     *
     * @param integer $StatusCode
     */
    public function __construct($StatusCode) {
        parent::__construct();
        TType::Int($StatusCode);
        $this->FStatusCode = $StatusCode;
    }

    /**
     *
     * @return integer
     */
    public function getStatusCode() {
        return $this->FStatusCode;
    }
}

/**
 *
 * @author 许子健
 */
class EResolveRequestFailed extends EHttpException {
}

/**
 * THttpCookie
 *
 * @author 许子健
 */
class THttpCookie extends TRecord {

    /**
     *
     * @var string
     */
    public $Domain = '';
    /**
     *
     * @var integer
     */
    public $Expire = 0;
    /**
     *
     * @var boolean
     */
    public $HttpOnly = false;
    /**
     *
     * @var string
     */
    public $Name = '';
    /**
     *
     * @var string
     */
    public $Path = '';
    /**
     *
     * @var boolean
     */
    public $Secure = false;
    /**
     *
     * @var string
     */
    public $Value = '';
}

/**
 * THttpCookies
 * extends \FrameworkDSW\Containers\TMap<K: string, V: \FrameworkDSW\Web\THttpCookie>
 *
 * @author 许子健
 */
class THttpCookies extends TMap {

    /**
     *
     * @var \FrameworkDSW\Web\THttpRequest
     */
    private $FRequest = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\THttpRequest $Request
     */
    public function __construct($Request) {
        TType::Object($Request, THttpRequest::class);
        self::PrepareGeneric(['K' => Framework::String, 'V' => THttpCookie::class]);
        parent::__construct();

        $this->FRequest = $Request;
        foreach ($_COOKIE as $mName => $mValue) {
            $mCookie        = new THttpCookie();
            $mCookie->Name  = (string)$mName;
            $mCookie->Value = (string)$mValue;
            $this->Put($mCookie->Name, $mCookie);
        }
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Web\THttpRequest
     */
    public function getRequest() {
        return $this->FRequest;
    }

    /**
     * descHere
     *
     * @param K $Key
     */
    protected function DoDelete($Key) {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        $mCookie = $this[$Key];
        setcookie($Key, null, 0, $mCookie->Path, $mCookie->Domain, $mCookie->Secure, $mCookie->HttpOnly);
        parent::DoDelete($Key);
    }

    /**
     * descHere
     *
     * @param K $Key
     * @param V $Value
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    protected function DoPut($Key, $Value) {
        /**@var THttpCookie $Value */
        if ($Value == null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: HTTP cookie must be specified, but value null found.'));
        }
        $Value->Name = $Key;
        setcookie($Key, $Value->Value, $Value->Expire, $Value->Path, $Value->Domain, $Value->Secure, $Value->HttpOnly);
        parent::DoPut($Key, $Value);
    }
}

/**
 * TSessionCookieMode
 * @author 许子健
 */
class TSessionCookieMode extends TEnum {

    /**
     * @var integer
     */
    const eNone = 0;
    /**
     * @var integer
     */
    const eAllow = 1;
    /**
     * @var integer
     */
    const eOnly = 2;

}

/**
 * THttpSession
 * extends TAbstractMap<K: string, V: mixed>
 * @author 许子健
 */
class THttpSession extends TAbstractMap {

    /**
     *
     * @var \FrameworkDSW\Web\IHttpSessionStorage
     */
    private $FStorage = null;

    /**
     * descHere
     * @param boolean $AutoStart
     * @param \FrameworkDSW\Web\IHttpSessionStorage $Storage
     */
    public function __construct($AutoStart = true, $Storage = null) {
        TType::Bool($AutoStart);
        TType::Object($Storage, IHttpSessionStorage::class);
        $this->PrepareMethodGeneric(['K' => Framework::String, 'V' => null, 'T' => [TPair::class => ['K' => Framework::String, 'V' => null]]]);
        parent::__construct(false);
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 100);
        $this->FStorage = $Storage;
        if ($AutoStart) {
            $this->Open();
        }
    }

    /**
     * descHere
     */
    public function Open() {
        if ($this->FStorage !== null) {
            $mSelf = $this;
            session_set_save_handler(function ($SavePath, $Name) use ($mSelf) {
                try {
                    $mSelf->FStorage->Open($SavePath, $Name);

                    return true;
                }
                catch (EException $Ex) {
                    return false;
                }
            }, function () use ($mSelf) {
                try {
                    $mSelf->FStorage->Close();

                    return true;
                }
                catch (EException $Ex) {
                    return false;
                }
            }, function ($SessionId) use ($mSelf) {
                try {
                    return $mSelf->FStorage->Read($SessionId);
                }
                catch (EException $Ex) {
                    return '';
                }
            }, function ($SessionId, $SessionData) use ($mSelf) {
                try {
                    $mSelf->FStorage->Write($SessionId, $SessionData);

                    return true;
                }
                catch (EException $Ex) {
                    return false;
                }
            }, function ($SessionId) use ($mSelf) {
                try {
                    $mSelf->FStorage->Delete($SessionId);

                    return true;
                }
                catch (EException $Ex) {
                    return false;
                }
            }, function ($MaxLifeTime) use ($mSelf) {
                try {
                    $mSelf->FStorage->Purge($MaxLifeTime);

                    return true;
                }
                catch (EException $Ex) {
                    return false;
                }
            });
        }
        if (session_start() == false) {
            throw new ESessionException(sprintf('Session exception: session start failed.'));
        }
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractCollection::Destroy()
     */
    public function Destroy() {
        $this->Close();
        parent::Destroy();
    }

    /**
     * descHere
     */
    public function Close() {
        if (session_id() !== '') {
            session_write_close();
        }
    }

    /**
     * descHere
     */
    public function Clear() {
        session_unset();
    }

    /**
     * descHere
     * @return string
     */
    public function getCookieDomain() {
        return session_get_cookie_params()['domain'];
    }

    /**
     * descHere
     * @return boolean
     */
    public function getCookieHttpOnly() {
        return session_get_cookie_params()['httponly'];
    }

    /**
     * descHere
     * @return integer
     */
    public function getCookieLifeTime() {
        return session_get_cookie_params()['lifetime'];
    }

    /**
     * descHere
     * @return TSessionCookieMode
     */
    public function getCookieMode() {
        if (ini_get('session.use_cookies') === '0') {
            return TSessionCookieMode::eNone();
        }
        elseif (ini_get('session.use_only_cookies') === '0') {
            return TSessionCookieMode::eAllow();
        }
        else {
            return TSessionCookieMode::eOnly();
        }
    }

    /**
     * descHere
     * @return string
     */
    public function getCookiePath() {
        return session_get_cookie_params()['path'];
    }

    /**
     * descHere
     * @return float
     */
    public function getGcProbability() {
        return (float)(ini_get('session.gc_probability') / ini_get('session.gc_divisor') * 100);
    }

    /**
     * descHere
     * @return string
     */
    public function getSavePath() {
        return session_save_path();
    }

    /**
     * descHere
     * @return string
     */
    public function getSessionId() {
        return session_id();
    }

    /**
     * descHere
     * @return string
     */
    public function getSessionName() {
        return session_name();
    }

    /**
     * descHere
     * @return \FrameworkDSW\Web\IHttpSessionStorage
     */
    public function getStorage() {
        return $this->FStorage;
    }

    /**
     * descHere
     * @return integer
     */
    public function getTimeout() {
        return (integer)ini_get('session.gc_maxlifetime');
    }

    /**
     * descHere
     * @return boolean
     */
    public function getUseTransparentSessionId() {
        return ini_get('session.use_trans_sid') === '1';
    }

    /**
     * descHere
     */
    public function Purge() {
        if (session_id() !== '') {
            session_unset();
            session_destroy();
        }
    }

    /**
     * descHere
     * @param boolean $DeleteOldSession
     */
    public function RegenerateId($DeleteOldSession = false) {
        TType::Bool($DeleteOldSession);
        session_regenerate_id($DeleteOldSession);
    }

    /**
     * descHere
     * @param string $Value
     */
    public function setCookieDomain($Value) {
        TType::String($Value);
        $mParams = session_get_cookie_params();
        session_set_cookie_params($mParams['lifetime'], $mParams['path'], $Value);
    }

    /**
     * descHere
     * @param boolean $Value
     */
    public function setCookieHttpOnly($Value) {
        TType::Bool($Value);
        $mParams = session_get_cookie_params();
        session_set_cookie_params($mParams['lifetime'], $mParams['path'], $mParams['domain'], $mParams['secure'], $Value);
    }

    /**
     * descHere
     * @param integer $Value
     */
    public function setCookieLifeTime($Value) {
        TType::Int($Value);
        $mParams = session_get_cookie_params();
        session_set_cookie_params($Value, $mParams['path'], $mParams['domain'], $mParams['secure'], $mParams['httponly']);
    }

    /**
     * descHere
     * @param \FrameworkDSW\Web\TSessionCookieMode $Value
     */
    public function setCookieMode($Value) {
        TType::Object($Value, TSessionCookieMode::class);

        switch ($Value) {
            case TSessionCookieMode::eNone():
                ini_set('session.use_cookies', '0');
                ini_set('session.use_only_cookies', '0');
                break;
            case TSessionCookieMode::eAllow():
                ini_set('session.use_cookies', '1');
                ini_set('session.use_only_cookies', '0');
                break;
            case TSessionCookieMode::eOnly():
                ini_set('session.use_cookies', '1');
                ini_set('session.use_only_cookies', '1');
                break;
        }
    }

    /**
     * descHere
     * @param string $Value
     */
    public function setCookiePath($Value) {
        TType::Int($Value);
        $mParams = session_get_cookie_params();
        session_set_cookie_params($Value, $mParams['path'], $mParams['domain'], $mParams['secure'], $mParams['httponly']);
    }

    /**
     * descHere
     * @param float $Value
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function setGcProbability($Value) {
        TType::Float($Value);
        if ($Value >= 0 && $Value <= 100) {
            // percent * 21474837 / 2147483647 ≈ percent * 0.01
            ini_set('session.gc_probability', floor($Value * 21474836.47));
            ini_set('session.gc_divisor', 2147483647);
        }
        else {
            throw new EInvalidParameter(sprintf('Invalid parameter: GC probability must be between 0 and 100, but %s was given.', $Value));
        }
    }

    /**
     * descHere
     * @param string $Value
     * @throws ESessionException
     */
    public function setSavePath($Value) {
        TType::String($Value);
        if ($this->FStorage === null && !is_dir($Value)) {
            if ($this->FStorage !== null) {
                throw new ESessionException(sprintf('No such directory: "%s", setting session save path failed.', $Value));
            }
            else {
                throw new ESessionException(sprintf('Empty session storage: setting session save path as "%s" failed.', $Value));
            }
        }
        else {
            session_save_path($Value);
        }
    }

    /**
     * descHere
     * @param string $Value
     */
    public function setSessionId($Value) {
        TType::String($Value);
        session_id($Value);
    }

    /**
     * descHere
     * @param string $Value
     */
    public function setSessionName($Value) {
        TType::String($Value);
        session_name($Value);
    }

    /**
     * descHere
     * @param \FrameworkDSW\Web\IHttpSessionStorage $Storage
     * @throws ESessionException
     */
    public function setStorage($Storage) {
        TType::Object($Storage, IHttpSessionStorage::class);
        if ($this->getIsStarted()) {
            throw new ESessionException(sprintf('Session already started: setting session storage is not allowed.'));
        }
        else {
            $this->FStorage = $Storage;
        }
    }

    /**
     * descHere
     * @return boolean
     */
    public function getIsStarted() {
        return session_id() !== '';
    }

    /**
     * descHere
     * @param integer $Value
     */
    public function setTimeout($Value) {
        TType::Int($Value);
        ini_set('session.gc_maxlifetime', $Value);
    }

    /**
     * descHere
     * @param boolean $Value
     */
    public function setUseTransparentSessionId($Value) {
        TType::Bool($Value);
        ini_set('session.use_trans_sid', $Value);
    }

    /**
     * descHere
     * @param K $Key
     * @return boolean
     */
    protected function DoContainsKey($Key) {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return isset($_SESSION[$Key]);
    }

    /**
     * descHere
     * @param K $Key
     */
    protected function DoDelete($Key) {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        unset($_SESSION[$Key]);
    }

    /**
     * descHere
     * @param K $Key
     * @return V
     */
    protected function DoGet($Key) {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return $_SESSION[$Key];
    }

    /**
     * descHere
     * @param K $Key
     * @param V $Value
     */
    protected function DoPut($Key, $Value) {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        $_SESSION[$Key] = $Value;
    }

}

/**
 * THttpRequest
 *
 * @author 许子健
 */
class THttpRequest extends TObject {
    /**
     *
     * @var string[]
     */
    private $FPutParameters = [];
    /**
     *
     * @var string[]
     */
    private $FDeleteParameters = [];
    /**
     *
     * @var string
     */
    private $FCsrfTokenName = '__FDSW_CSRF_TOKEN';
    /**
     *
     * @var string
     */
    private $FScriptUrl = '';
    /**
     *
     * @var string
     */
    private $FBaseUrl = '';
    /**
     *
     * @var string
     */
    private $FCsrfToken = '';
    /**
     *
     * @var \FrameworkDSW\Web\THttpCookies
     */
    private $FCookies = null;
    /**
     *
     * @var string[]
     */
    private $FRestParameters = [];
    /**
     *
     * @var string
     */
    private $FHostInfo = '';
    /**
     *
     * @var integer
     */
    private $FPort = -1;
    /**
     *
     * @var integer
     */
    private $FSecurePort = -1;
    /**
     *
     * @var string
     */
    private $FRequestUri = '';
    /**
     *
     * @var string
     */
    private $FPathInfo = '';
    /**
     *
     * @var string
     */
    private $FScriptFile = '';

    /**
     * descHere
     *
     * @return string
     */
    public function getAcceptTypes() {
        return (string)$_SERVER['HTTP_ACCEPT'];
    }

    /**
     * descHere
     *
     * @param string $UserAgent
     * @throws EBrowscapNotEnabled
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    public function GetBrowser($UserAgent = '') {
        TType::String($UserAgent);

        if ($UserAgent == '') {
            $mRaw = get_browser(null, true);
        }
        else {
            $mRaw = get_browser($UserAgent, true);
        }

        if (!is_array($mRaw)) {
            throw new EBrowscapNotEnabled(sprintf('Get browser failed: Browscap is not enabled.'));
        }

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
        $mResult = new TMap();
        foreach ($mRaw as $mKey => &$mValue) {
            if (is_bool($mValue)) {
                $mValue = $mValue ? 'true' : 'false';
            }
            $mResult->Put($mKey, (string)$mValue);
        }

        return $mResult;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getCsrfToken() {
        if ($this->FCsrfToken == '') {
            $mCookies = $this->getCookies();
            if (!$mCookies->ContainsKey($this->FCsrfTokenName)) {
                $this->CreateCsrfCookie();
            }
            $this->FCsrfToken = $mCookies[$this->FCsrfTokenName]->Value;
        }

        return $this->FCsrfToken;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Web\THttpCookies
     */
    public function getCookies() {
        if ($this->FCookies == null) {
            $this->FCookies = new THttpCookies($this);
        }

        return $this->FCookies;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpCookie
     */
    protected function CreateCsrfCookie() {
        $mCsrfTokenCookie        = new THttpCookie();
        $mCsrfTokenCookie->Value = sha1(uniqid((string)mt_rand(), true));
        $this->getCookies()->Put($this->FCsrfTokenName, $mCsrfTokenCookie);
        $this->FCsrfToken = $mCsrfTokenCookie->Value;

        return $mCsrfTokenCookie;
    }

    /**
     * descHere
     *
     * @param string $Name
     * @throws ENoSuchRequestParameter
     * @return string
     */
    public function GetDelete($Name) {
        TType::String($Name);

        if ($this->getIsDeleteRequestViaPostRequest()) {
            try {
                return $this->GetPost($Name);
            }
            catch (ENoSuchRequestParameter $Ex) {
                throw new ENoSuchRequestParameter(sprintf('No such DELETE parameter: %s.', $Name), $Ex, $Name, THttpMethod::Delete());
            }
        }

        if ($this->FDeleteParameters == []) {
            $this->FDeleteParameters = $this->getIsDeleteRequest() ? $this->getRestParameters() : [];
        }
        if (isset($this->FDeleteParameters[$Name])) {
            return $this->FDeleteParameters[$Name];
        }
        else {
            throw new ENoSuchRequestParameter(sprintf('No such DELETE parameter: %s.', $Name), null, $Name, THttpMethod::Delete());
        }
    }

    /**
     * @return boolean
     */
    public function getIsDeleteRequestViaPostRequest() {
        return isset($_POST['_method']) && strtoupper($_POST['_method']) == 'DELETE';
    }

    /**
     *
     * @param string $Name
     * @throws ENoSuchRequestParameter
     * @return string
     */
    public function GetPost($Name) {
        TType::String($Name);

        if (isset($_POST[$Name])) {
            return (string)$_POST[$Name];
        }
        else {
            throw new ENoSuchRequestParameter(sprintf('No such POST parameter: %s.', $Name), null, $Name, THttpMethod::Post());
        }
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsDeleteRequest() {
        return (isset($_SERVER['REQUEST_METHOD']) && (strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE')) || $this->getIsDeleteRequestViaPostRequest();
    }

    /**
     * @return string[]
     */
    protected function getRestParameters() {
        if ($this->FRestParameters == []) {
            $mResult = [];
            if (function_exists('mb_parse_str')) {
                mb_parse_str(file_get_contents('php://input'), $mResult);
            }
            else {
                parse_str(file_get_contents('php://input'), $mResult);
            }
            $this->FRestParameters = $mResult;
        }

        return $this->FRestParameters;
    }

    /**
     * descHere
     *
     * @param string $Name
     * @throws ENoSuchRequestParameter
     * @return string
     */
    public function GetPut($Name) {
        TType::String($Name);

        if ($this->getIsPutRequestViaPostRequest()) {
            try {
                return $this->GetPost($Name);
            }
            catch (ENoSuchRequestParameter $Ex) {
                throw new ENoSuchRequestParameter(sprintf('No such PUT parameter: %s.', $Name), $Ex, $Name, THttpMethod::Put());
            }
        }

        if ($this->FPutParameters == []) {
            $this->FPutParameters = $this->getIsPutRequest() ? $this->getRestParameters() : [];
        }
        if (isset($this->FPutParameters[$Name])) {
            return $this->FPutParameters[$Name];
        }
        else {
            throw new ENoSuchRequestParameter(sprintf('No such PUT parameter: %s.', $Name), null, $Name, THttpMethod::Put());
        }
    }

    /**
     * @return boolean
     */
    public function getIsPutRequestViaPostRequest() {
        return isset($_POST['_method']) && strtoupper($_POST['_method']) == 'PUT';
    }

    /**
     *
     * @return boolean
     */
    public function getIsPutRequest() {
        return isset($_SERVER['REQUEST_METHOD']) && (strtoupper($_SERVER['REQUEST_METHOD'] == 'PUT'));
    }

    /**
     *
     * @param string $Name
     * @throws ENoSuchRequestParameter
     * @return string
     */
    public function GetParameter($Name) {
        TType::String($Name);
        if (isset($_GET[$Name])) {
            return (string)$_GET[$Name];
        }
        elseif (isset($_POST[$Name])) {
            return (string)$_POST[$Name];
        }
        else {
            throw new ENoSuchRequestParameter(sprintf('No such request parameter: %s', $Name), null, $Name);
        }
    }

    /**
     *
     * @throws EDeterminePathInfoFailed
     * @return string
     */
    public function getPathInfo() {
        if ($this->FPathInfo == '') {
            $mPathInfo        = $this->getRequestUri();
            $mQuestionMarkPos = strpos($mPathInfo, '?');
            if ($mQuestionMarkPos !== false) {
                $mPathInfo = substr($mPathInfo, 0, $mQuestionMarkPos);
            }

            $mPathInfo = $this->DecodePathInfo($mPathInfo);

            $mScriptUrl = $this->getScriptUrl();
            $mBaseUrl   = $this->GetBaseUrl();
            if (strpos($mPathInfo, $mScriptUrl) === 0) {
                $mPathInfo = substr($mPathInfo, strlen($mScriptUrl));
            }
            elseif ($mBaseUrl == '' || strpos($mPathInfo, $mBaseUrl) === 0) {
                $mPathInfo = substr($mPathInfo, strlen($mBaseUrl));
            }
            elseif (strpos($_SERVER['PHP_SELF'], $mScriptUrl) === 0) {
                $mPathInfo = substr($_SERVER['PHP_SELF'], strlen($mScriptUrl));
            }
            else {
                throw new EDeterminePathInfoFailed(sprintf('Determine path info failed.'));
            }
            $this->FPathInfo = trim($mPathInfo, '/');
        }

        return $this->FPathInfo;
    }

    /**
     *
     * @throws EDetermineRequestUriFailed
     * @return string
     */
    public function getRequestUri() {
        if ($this->FRequestUri == '') {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                $this->FRequestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            }
            elseif (isset($_SERVER['REQUEST_URI'])) {
                $this->FRequestUri = $_SERVER['REQUEST_URI'];
                if (!empty($_SERVER['HTTP_HOST'])) {
                    if (strpos($this->FRequestUri, $_SERVER['HTTP_HOST']) !== false) {
                        $this->FRequestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->FRequestUri);
                    }
                }
                else {
                    $this->FRequestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->FRequestUri);
                }
            }
            elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $this->FRequestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $this->FRequestUri .= "?{$_SERVER['QUERY_STRING']}";
                }
            }
            else {
                throw new EDetermineRequestUriFailed(sprintf('Determine request URI failed.'));
            }
        }

        return $this->FRequestUri;
    }

    /**
     *
     * @param string $PathInfo
     * @return string
     */
    protected function DecodePathInfo($PathInfo) {
        $PathInfo = urldecode($PathInfo);

        // is it UTF-8?
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if (preg_match('%^(?:
       [\x09\x0A\x0D\x20-\x7E]            # ASCII
     | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
     | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
     | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
     | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
     | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
     | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
     | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )*$%xs', $PathInfo)
        ) {
            return $PathInfo;
        }
        else {
            return utf8_encode($PathInfo);
        }
    }

    /**
     *
     * @throws EUnableToResolveScriptUrl
     * @return string
     */
    public function getScriptUrl() {
        if ($this->FScriptUrl == '') {
            $mScriptName = basename($_SERVER['SCRIPT_FILENAME']);
            if (basename($_SERVER['SCRIPT_NAME']) === $mScriptName) {
                $this->FScriptUrl = $_SERVER['SCRIPT_NAME'];
            }
            elseif (basename($_SERVER['PHP_SELF']) === $mScriptName) {
                $this->FScriptUrl = $_SERVER['PHP_SELF'];
            }
            elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $mScriptName) {
                $this->FScriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            }
            elseif (($mPos = strpos($_SERVER['PHP_SELF'], "/{$mScriptName}")) !== false) {
                $this->FScriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $mPos) . "/{$mScriptName}";
            }
            elseif (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
                $this->FScriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            }
            else {
                throw new EUnableToResolveScriptUrl(sprintf('Unable to resolve script URL.'));
            }
        }

        return $this->FScriptUrl;
    }

    /**
     * descHere
     *
     * @param boolean $Absolute
     * @return string
     */
    public function GetBaseUrl($Absolute = false) {
        if ($this->FBaseUrl == '') {
            $this->FBaseUrl = rtrim(dirname($this->getScriptUrl()), '/\\');
        }
        if ($Absolute) {
            return $this->getHostInfo() . $this->FBaseUrl;
        }
        else {
            return $this->FBaseUrl;
        }
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getHostInfo() {
        if ($this->FHostInfo == '') {
            $mIsSecureConnection = $this->getIsSecureConnection();
            if ($mIsSecureConnection) {
                $mSchema = 'https';
            }
            else {
                $mSchema = 'http';
            }
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->FHostInfo = "{$mSchema}://{$_SERVER['HTTP_HOST']}";
            }
            else {
                $this->FHostInfo = "{$mSchema}://{$_SERVER['SERVER_NAME']}";
                $mPort           = $mIsSecureConnection ? $this->getSecurePort() : $this->getPort();
                if (($mPort !== 80 && !$mIsSecureConnection) || ($mPort !== 443 && $mIsSecureConnection)) {
                    $this->FHostInfo .= ":{$mPort}";
                }
            }
        }

        return $this->FHostInfo;
    }

    /**
     *
     * @return boolean
     */
    public function getIsSecureConnection() {
        return isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on');
    }

    /**
     *
     * @return integer
     */
    public function getSecurePort() {
        if ($this->FSecurePort == -1) {
            $this->FSecurePort = $this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
        }

        return $this->FSecurePort;
    }

    /**
     *
     * @return integer
     */
    public function getPort() {
        if ($this->FPort == -1) {
            $this->FPort = !$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
        }

        return $this->FPort;
    }

    /**
     *
     * @param integer $Value
     * @throws EInvalidParameter
     */
    public function setPort($Value) {
        TType::Int($Value);

        if ($Value < 0 || $Value > 65535) {
            throw new EInvalidParameter(sprintf('Invalid port number: port number should between 1 and 65535, but %s found.', $Value));
        }
        $this->FPort = $Value;
    }

    /**
     *
     * @param integer $Value
     * @throws EInvalidParameter
     */
    public function setSecurePort($Value) {
        TType::Int($Value);

        if ($Value < 0 || $Value > 65535) {
            throw new EInvalidParameter(sprintf('Invalid secure port number: port number should between 1 and 65535, but %s found.', $Value));
        }
        $this->FSecurePort = $Value;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * descHere
     */
    public function ValidateCsrfToken() {
        $mValid = false;
        if ($this->getIsPostRequest()) {
            $mCookies = $this->getCookies();
            if ($mCookies->ContainsKey($this->FCsrfTokenName) && isset($_POST[$this->FCsrfTokenName])) {
                $mValid = ($mCookies[$this->FCsrfToken] == (string)$_POST[$this->FCsrfTokenName]);
            }
        }
        if (!$mValid) {
            throw new EHttpException(400);
        }
    }

    /**
     *
     * @return boolean
     */
    public function getIsPostRequest() {
        return isset($_SERVER['REQUEST_METHOD']) && (strtoupper($_SERVER['REQUEST_METHOD'] == 'POST'));
    }

    /**
     * descHere
     *
     * @param string $FilePath
     * @param string $SaveName
     * @param string $MimeType
     * @param string $XHeader
     * @param boolean $Terminate
     * @param boolean $ForceDownload
     * @param \FrameworkDSW\Containers\IMap $AddHeaders <K: string, V: string>
     */
    public function XSendFile($FilePath, $SaveName = '', $MimeType = '', $XHeader = 'X-Sendfile', $Terminate = false, $ForceDownload = true, $AddHeaders = null) {
        TType::String($FilePath);
        TType::String($SaveName);
        TType::String($MimeType);
        TType::String($XHeader);
        TType::Bool($Terminate);
        TType::Bool($ForceDownload);
        TType::Object($AddHeaders, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);

        if ($ForceDownload) {
            $mDisposition = 'attachment';
        }
        else {
            $mDisposition = 'inline';
        }

        if ($SaveName == '') {
            $SaveName = basename($FilePath);
        }

        if ($MimeType == '') { // TODO FIXME should replace with the correct way
            // of detecting mime type of the file.
            $MimeType = 'text/plain';
        }

        ob_end_clean();
        ob_start();

        header("Content-type: {$MimeType}");
        header("Content-Disposition: {$mDisposition}; filename=\"{$SaveName}\"");
        if ($AddHeaders != null) {
            foreach ($AddHeaders as $mHeader => $mValue) {
                header("{$mHeader}: {$mValue}");
            }
        }
        header("{$XHeader}: {$FilePath}");

        if ($Terminate) {
            // TODO how to deal with terminating.
            ob_end_flush();
        }
    }

    /**
     *
     * @param string $Name
     * @throws ENoSuchRequestParameter
     * @return string
     */
    public function GetQuery($Name) {
        TType::String($Name);

        if (isset($_GET[$Name])) {
            return (string)$_GET[$Name];
        }
        else {
            throw new ENoSuchRequestParameter(sprintf('No such GET parameter: %s.', $Name), null, $Name, THttpMethod::Get());
        }
    }

    /**
     *
     * @return string $Name
     */
    public function getQueryString() {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    /**
     *
     * @return string
     */
    public function getScriptFile() {
        if ($this->FScriptFile == '') {
            $this->FScriptFile = realpath($_SERVER['SCRIPT_FILENAME']);
        }

        return $this->FScriptFile;
    }

    /**
     *
     * @return string
     */
    public function getServerName() {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     *
     * @return string
     */
    public function getServerPort() {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     *
     * @return string
     */
    public function getUrlReferrer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    /**
     *
     * @return string
     */
    public function getUserAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    /**
     *
     * @return string
     */
    public function getUserHost() {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '';
    }

    /**
     *
     * @return string
     */
    public function getUserHostAddress() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    /**
     *
     * @param string $Url
     * @param integer $StatusCode
     */
    public function Redirect($Url, $StatusCode = 302) {
        TType::String($Url);
        TType::Int($StatusCode);

        if (strpos($Url, '/') === 0) {
            $Url = $this->getHostInfo() . $Url;
        }

        ob_end_clean();
        ob_start();
        header("Location: {$Url}", true, $StatusCode);
        ob_flush();
    }

    /**
     *
     * @param string $FileName
     * @param string $Content
     * @param string $MimeType
     */
    public function SendFile($FileName, $Content, $MimeType = '') {
        if ($MimeType == '') {
            $MimeType = 'text/plain';
        }
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-type: {$MimeType}");
        if (ob_get_length() === false) {
            header('Content-Length: ' . (function_exists('mb_strlen') ? mb_strlen($Content, '8bit') : strlen($Content)));
        }
        header("Content-Disposition: attachment; filename=\"{$FileName}\"");
        header('Content-Transfer-Encoding: binary');
        echo $Content;
    }

    /**
     *
     * @param string $Value
     */
    public function setBaseUrl($Value) {
        TType::String($Value);

        $this->FBaseUrl = $Value;
    }

    /**
     *
     * @param string $Value
     */
    public function setHostInfo($Value) {
        TType::String($Value);
        $this->FHostInfo = rtrim($Value, '/');
    }

    /**
     *
     * @param string $Value
     */
    public function setScriptUrl($Value) {
        TType::String($Value);
        $this->FScriptUrl = '/' . trim($Value, '/');
    }

    /**
     */
    protected function NormalizeRequest() {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() === 1) {
            if (isset($_GET)) {
                $_GET = array_map('stripslashes', $_GET);
            }
            if (isset($_POST)) {
                $_POST = array_map('stripslashes', $_POST);
            }
            if (isset($_REQUEST)) {
                $_REQUEST = array_map('stripslashes', $_REQUEST);
            }
            if (isset($_COOKIE)) {
                $_COOKIE = array_map('stripslashes', $_COOKIE);
            }
        }
    }
}

/**
 * THttpMethod
 *
 * @author 许子健
 */
class THttpMethod extends TRecord {
    /**
     *
     * @var string
     */
    public $Method = '';

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Delete() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'DELETE';

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Get() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'GET';

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Head() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'HEAD';

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Options() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'OPTIONS';

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Post() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'POST';

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Put() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'PUT';

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Web\THttpMethod
     */
    public static function Trace() {
        $mResult         = new THttpMethod();
        $mResult->Method = 'TRACE';

        return $mResult;
    }
}

/**
 * TUrlMode
 *
 * @author 许子健
 */
final class TUrlMode extends TEnum {

    /**
     *
     * @var integer
     */
    const eGet = 1;
    /**
     *
     * @var integer
     */
    const ePath = 0;
}

/**
 * TUrlRouter
 *
 * @author 许子健
 */
class TUrlRouter extends TObject implements IUrlRouter {

    /**
     *
     * @var string
     */
    private $FBaseUrl = '';
    /**
     *
     * @var boolean
     */
    private $FCaseSensitive = false;
    /**
     *
     * @var boolean
     */
    private $FCheckParameters = false;
    /**
     *
     * @var string
     */
    private $FRouteVariableName = '';
    /**
     *
     * @var \FrameworkDSW\Containers\TLinkedList <T: \FrameworkDSW\Web\IUrlRouteRule>
     */
    private $FRules = null;
    /**
     *
     * @var boolean
     */
    private $FShowScriptName = false;
    /**
     *
     * @var string
     */
    private $FSuffix = '';
    /**
     *
     * @var \FrameworkDSW\Web\TUrlMode
     */
    private $FUrlMode = null;
    /**
     *
     * @var boolean
     */
    private $FUseStrictParsing = false;
    /**
     *
     * @var boolean
     */
    private $FAppendParameters = true;
    /**
     *
     * @var \FrameworkDSW\Web\THttpRequest
     */
    private $FRequest = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\THttpRequest $HttpRequest
     */
    public function __construct($HttpRequest) {
        parent::__construct();

        TType::Object($HttpRequest, THttpRequest::class);

        $this->FUrlMode = TUrlMode::ePath();

        TLinkedList::PrepareGeneric(['T' => IUrlRouteRule::class]);
        $this->FRules   = new TLinkedList(true);
        $this->FRequest = $HttpRequest;
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FRules);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\IUrlRouteRule $Rule
     * @param boolean $Append
     */
    public function AddRule($Rule, $Append = true) {
        TType::Object($Rule, IUrlRouteRule::class);
        TType::Bool($Append);

        if ($Append) {
            $this->FRules[] = $Rule;
        }
        else {
            $this->FRules->Insert(0, $Rule);
        }
    }

    /**
     * descHere
     *
     * @param string $Route
     * @param \FrameworkDSW\Containers\IMap $Parameters <K: string, V: string>
     * @param string $Ampersand
     * @return string
     */
    public function CreateUrl($Route, $Parameters = null, $Ampersand = '&') {
        TType::String($Route);
        TType::Object($Parameters, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        TType::String($Ampersand);

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
        $mParameters = new TMap();
        if ($Parameters != null) {
            $mParameters->PutAll($Parameters);
        }

        if ($mParameters->ContainsKey($this->FRouteVariableName)) {
            $mParameters->Delete($this->FRouteVariableName);
        }

        if ($mParameters->ContainsKey('#')) {
            $mAnchor = "#{$Parameters['#']}";
            $mParameters->Delete('#');
        }
        else {
            $mAnchor = '';
        }
        $Route = trim($Route, '/');
        /** @var IUrlRouteRule $mRule */
        foreach ($this->FRules as $mRule) {
            $mUrl = $mRule->CreateUrl($this, $Route, $Parameters, $Ampersand);
            if ($mUrl != '') {
                if ($mRule->getHasHostInfo()) {
                    return $mUrl == '/' ? "/{$mAnchor}" : "{$mUrl}{$mAnchor}";
                }
                else {
                    return $this->getBaseUrl() . "/{$mUrl}{$mAnchor}";
                }
            }
        }

        $mResult = $this->CreateDefaultUrl($Route, $mParameters, $Ampersand) . $mAnchor;
        Framework::Free($mParameters);

        return $mResult;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getBaseUrl() {
        if ($this->FBaseUrl == '') {
            if ($this->FShowScriptName) {
                $this->FBaseUrl = $this->FRequest->getScriptUrl();
            }
            else {
                $this->FBaseUrl = $this->FRequest->GetBaseUrl();
            }
        }

        return $this->FBaseUrl;
    }

    /**
     *
     * @param string $Route
     * @param \FrameworkDSW\Containers\IMap $Parameters <K: string, V: string>
     * @param string $Ampersand
     * @return string
     */
    protected function CreateDefaultUrl($Route, $Parameters = null, $Ampersand = '&') {
        TType::String($Route);
        TType::Object($Parameters, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        TType::String($Ampersand);

        $mQueryString = $this->CreatePathInfo($Parameters, '=', $Ampersand);
        switch ($this->getUrlMode()) {
            case TUrlMode::ePath():
                $mUrl = rtrim($this->getBaseUrl() . "/{$Route}");
                if ($this->FAppendParameters) {
                    $mUrl = rtrim("{$mUrl}/" . $this->CreatePathInfo($Parameters, '/', '/'), '/');
                    if ($Route != '') {
                        $mUrl .= $this->getUrlSuffix();
                    }
                }
                else {
                    if ($Route != '') {
                        $mUrl .= $this->getUrlSuffix();
                    }
                    if ($mQueryString != '') {
                        $mUrl .= "?{$mQueryString}";
                    }
                }

                break;
            case TUrlMode::eGet():
                $mUrl = $this->getBaseUrl();
                if (!$this->getShowScriptName()) {
                    $mUrl .= '/';
                }
                if ($Route != '') {
                    $mUrl .= "?{$this->FRouteVariableName}={$Route}";

                    if ($mQueryString != '') {
                        $mUrl .= "{$Ampersand}{$mQueryString}";
                    }
                }
                elseif ($mQueryString != '') {
                    $mUrl .= "?{$mQueryString}";
                }
                break;
        }

        /** @noinspection PhpUndefinedVariableInspection */

        return $mUrl;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Parameters <K: string, V: string>
     * @param string $Equal
     * @param string $Ampersand
     * @return string
     */
    public function CreatePathInfo($Parameters, $Equal = '=', $Ampersand = '&') {
        TType::Object($Parameters, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        TType::String($Equal);
        TType::String($Ampersand);

        $mPairs = [];
        foreach ($Parameters as $mKey => $mValue) {
            $mPairs[] = urlencode($mKey) . $Equal . urlencode($mValue);
        }

        return implode($Ampersand, $mPairs);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Web\TUrlMode
     */
    public function getUrlMode() {
        return $this->FUrlMode;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getUrlSuffix() {
        return $this->FSuffix;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getShowScriptName() {
        return $this->FShowScriptName;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getAppendParameters() {
        return $this->FAppendParameters;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCaseSensitive() {
        return $this->FCaseSensitive;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCheckParameters() {
        // matchValue
        return $this->FCheckParameters;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getRouteVariableName() {
        return $this->FRouteVariableName;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IList <T: IUrlRouteRule>
     */
    public function getRules() {
        return $this->FRules;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getUseStrictParsing() {
        return $this->FUseStrictParsing;
    }

    /**
     * descHere
     *
     * @param string $PathInfo
     */
    public function ParsePathInfo($PathInfo) {
        TType::String($PathInfo);
        // pathInfo: Controller/Action/keyN/dataN/... without Controller and
        // Action segments.
        if ($PathInfo == '') {
            return;
        }
        $mItems     = explode('/', "{$PathInfo}/");
        $mBoundHigh = count($mItems) - 1;
        for ($i = 0; $i < $mBoundHigh; $i += 2) {
            $mKey = $mItems[$i];
            if ($mKey == '') {
                continue;
            }
            $mValue          = $mItems[$i + 1];
            $_REQUEST[$mKey] = $mValue;
            $_GET[$mKey]     = $mValue;
        }
    }

    /**
     * descHere
     *
     * @throws EResolveRequestFailed
     * @return string
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function ParseUrl() {
        $mRequest = $this->FRequest;
        switch ($this->FUrlMode) {
            case TUrlMode::ePath():
                $mRawPathInfo = $mRequest->getPathInfo();
                $mPathInfo    = $this->RemoveUrlSuffix($mRawPathInfo, $this->getUrlSuffix());
                /** @var IUrlRouteRule $mRule */
                foreach ($this->FRules as $mRule) {
                    try {
                        $mRoute = $mRule->ParseUrl($this, $mRequest, $mPathInfo, $mRawPathInfo);
                    }
                    catch (EParseUrlFailed $Ex) {
                        continue;
                    }

                    return $mRoute;
                }
                if ($this->FUseStrictParsing) {
                    throw new EResolveRequestFailed(404);
                }
                else {
                    return $mPathInfo;
                }
                break;
            case TUrlMode::eGet():
                if (isset($_GET[$this->FRouteVariableName])) {
                    return $_GET[$this->FRouteVariableName];
                }
                elseif (isset($_POST[$this->FRouteVariableName])) {
                    return $_POST[$this->FRouteVariableName];
                }
                else {
                    throw new EResolveRequestFailed(404);
                }
                break;
        }
    }

    /**
     * descHere
     *
     * @param string $PathInfo
     * @param string $UrlSuffix
     * @return string
     */
    public function RemoveUrlSuffix($PathInfo, $UrlSuffix) {
        TType::String($PathInfo);
        TType::String($UrlSuffix);

        if ($UrlSuffix != '' && substr($PathInfo, -strlen($UrlSuffix)) === $UrlSuffix) {
            return substr($PathInfo, 0, -strlen($UrlSuffix));
        }
        else {
            return $PathInfo;
        }
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setAppendParameters($Value) {
        TType::Bool($Value);
        $this->FAppendParameters = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setBaseUrl($Value) {
        TType::String($Value);
        $this->FBaseUrl = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setCaseSensitive($Value) {
        TType::Bool($Value);
        $this->FCaseSensitive = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setCheckParameters($Value) {
        TType::Bool($Value);
        $this->FCheckParameters = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setRouteVariableName($Value) {
        TType::String($Value);
        $this->FRouteVariableName = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IList $Value <T: \FrameworkDSW\Web\IUrlRouteRule>
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function setRules($Value) {
        TType::Object($Value, [IList::class => ['T' => IUrlRouteRule::class]]);
        if ($Value == null) {
            throw new EInvalidParameter(sprintf('Invalid rules: null are not allowed.'));
        }
        else {
            $this->FRules->AddAll($Value);
        }
        Framework::Free($this->FRules);
        $this->FRules = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setShowScriptName($Value) {
        TType::Bool($Value);
        $this->FShowScriptName = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\TUrlMode $Value
     */
    public function setUrlMode($Value) {
        TType::Object($Value, TUrlMode::class);
        $this->FUrlMode = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setUrlSuffix($Value) {
        TType::String($Value);
        $this->FSuffix = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setUseStrictParsing($Value) {
        TType::Bool($Value);
        $this->FUseStrictParsing = $Value;
    }

    /**
     *
     * @return string
     */
    public function getRequestHostInfo() {
        return $this->FRequest->getHostInfo();
    }
}

/**
 * TUrlRouteRule
 *
 * @author 许子健
 */
class TUrlRouteRule extends TObject implements IUrlRouteRule {

    /**
     *
     * @var boolean
     */
    private $FAppend = false;
    /**
     *
     * @var boolean
     */
    private $FCaseSensitive = false;
    /**
     *
     * @var boolean
     */
    private $FCheckParameters = false;
    /**
     *
     * @var \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    private $FDefaultParameters = null;
    /**
     *
     * @var boolean
     */
    private $FHasHostInfo = false;
    /**
     *
     * @var \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    private $FParameters = null;
    /**
     *
     * @var boolean
     */
    private $FParsingOnly = false;
    /**
     *
     * @var string
     */
    private $FPattern = '';
    /**
     *
     * @var \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    private $FReferences = null;
    /**
     *
     * @var string
     */
    private $FRoute = '';
    /**
     *
     * @var string
     */
    private $FRoutePattern = '';
    /**
     *
     * @var string
     */
    private $FTemplate = '';
    /**
     *
     * @var string
     */
    private $FUrlSuffix = '';
    /**
     *
     * @var boolean
     */
    private $FUseInheritedAppend = false;
    /**
     *
     * @var boolean
     */
    private $FUseInheritedCaseSensitive = false;
    /**
     *
     * @var boolean
     */
    private $FUseInheritedCheckParameters = false;
    /**
     *
     * @var boolean
     */
    private $FUseInheritedSuffix = false;
    /**
     *
     * @var \FrameworkDSW\Web\THttpMethod[]
     */
    private $FVerb = [];

    /**
     * descHere
     *
     * @param string $Route
     * @param string $Pattern
     */
    public function __construct($Route, $Pattern) {
        parent::__construct();
        TType::String($Route);
        TType::String($Pattern);

        // array( PATTERN => ROUTE
        // 'posts'=>'post/list',
        // 'post/<id:\d+>'=>'post/read',
        // 'post/<year:\d{4}>/<title>'=>'post/read',
        // '<controller:(c1|c2)>/foo/<action:\w+>' => '<controller>/<action>'
        // )
        $this->FRoute = trim($Route, '/');

        $mTr['/']  = '\\/';
        $mTr2['/'] = '\\/';

        if (strpos($Route, '<') !== false && preg_match_all('/<(\w+)>/', $Route, $mReferences)) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
            $this->FReferences = new TMap();
            foreach ($mReferences[1] as $mReference) {
                $this->FReferences->Put($mReference, "<{$mReference}>");
            }
        }

        $this->FHasHostInfo = !strncasecmp($Pattern, 'http://', 7) || !strncasecmp($Pattern, 'https://', 8);

        if (preg_match_all('/<(\w+):?(.*?)?>/', $Pattern, $mParameters)) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
            $this->FParameters = new TMap();

            $mTokens = array_combine($mParameters[1], $mParameters[2]);
            foreach ($mTokens as $mName => $mValue) {
                if ($mValue == '') {
                    $mValue = '[^\/]+';
                }
                $mTr["<{$mName}>"] = "(?P<{$mName}>{$mValue})";
                if ($this->FReferences->ContainsKey($mName)) {
                    $mTr2["<{$mName}>"] = $mTr["<{$mName}>"];
                }
                else {
                    $this->FParameters->Put($mName, $mValue);
                }
            }
        }

        $mPattern        = rtrim($Pattern, '*');
        $this->FAppend   = ($mPattern != $Pattern);
        $mPattern        = trim($mPattern, '/');
        $this->FTemplate = preg_replace('/<(\w+):?.*?>/', '<$1>', $mPattern);
        $this->FPattern  = '/^' . strtr($this->FTemplate, $mTr) . '\/';
        if ($this->FAppend) {
            $this->FPattern .= '/u';
        }
        else {
            $this->FPattern .= '$/u';
        }

        if ($this->FReferences != null) {
            $this->FRoutePattern = '/^' . strtr($this->FRoute, $mTr2) . '$/u';
        }
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FDefaultParameters);
        Framework::Free($this->FParameters);
        Framework::Free($this->FReferences);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\TUrlRouter $Router
     * @param string $Route
     * @param \FrameworkDSW\Containers\TMap $Parameters <K: string, V: string>
     * @param string $Ampersand
     * @throws EParsingOnlyUrlRule
     * @throws ECreateUrlFailed
     * @return string
     */
    public function CreateUrl($Router, $Route, $Parameters = null, $Ampersand = '&') {
        TType::Object($Router, TUrlRouter::class);
        TType::String($Route);
        TType::Object($Parameters, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        TType::String($Ampersand);

        if ($this->FParsingOnly) {
            throw new EParsingOnlyUrlRule(sprintf('Create URL failed: parsing only URL rule provided.'));
        }

        if (($this->FUseInheritedCaseSensitive && $Router->getCaseSensitive()) || (!$this->FUseInheritedCaseSensitive && $this->FCaseSensitive)) {
            $mCaseSensitive = '';
        }
        else {
            $mCaseSensitive = 'i';
        }
        $mTr = [];
        if ($Route != $this->FRoute) {
            if ($this->FRoutePattern != '' && preg_match("{$this->FRoutePattern}{$mCaseSensitive}", $Route, $mMatches) && $this->FReferences != null) {
                foreach ($this->FReferences as $mKey => $mName) {
                    $mTr[$mName] = $mMatches[$mKey];
                }
            }
            else {
                throw new ECreateUrlFailed(sprintf('Create URL failed.'));
            }
        }

        if ($this->FDefaultParameters != null) {
            foreach ($this->FDefaultParameters as $mKey => $mValue) {
                if ($Parameters->ContainsKey($mKey)) {
                    if ($Parameters[$mKey] == $mValue) {
                        $Parameters->Delete($mKey);
                    }
                    else {
                        throw new ECreateUrlFailed(sprintf('Create URL failed.'));
                    }
                }
            }

            foreach ($this->FDefaultParameters as $mKey => $mValue) {
                if (!$Parameters->ContainsKey($mKey)) {
                    throw new ECreateUrlFailed(sprintf('Create URL failed.'));
                }
            }
        }

        if ((($this->FUseInheritedCheckParameters && $Router->getCheckParameters()) || (!$this->FUseInheritedCheckParameters && $this->FCheckParameters)) && $this->FParameters != null) {
            foreach ($this->FParameters as $mKey => $mValue) {
                if (!preg_match("/\\A{$mValue}\\z/u{$mCaseSensitive}", $this->FParameters[$mKey])) {
                    throw new ECreateUrlFailed(sprintf('Create URL failed.'));
                }
            }
        }

        if ($this->FParameters != null) {
            foreach ($this->FParameters as $mKey => $mValue) {
                $mTr["<{$mKey}>"] = urlencode($mValue);
                $this->FParameters->Delete($mKey);
            }
        }
        $mSuffix = $this->FUseInheritedSuffix ? $Router->getUrlSuffix() : $this->FUrlSuffix;

        $mUrl = strtr($this->FTemplate, $mTr);

        if ($this->FHasHostInfo) {
            $mHostInfo = $Router->getRequestHostInfo();
            if (stripos($mUrl, $mHostInfo) === 0) {
                $mUrl = substr($mUrl, strlen($mHostInfo));
            }
        }

        if ($Parameters == null || $Parameters->IsEmpty()) {
            return $mUrl != '' ? "{$mUrl}{$mSuffix}" : $mUrl;
        }

        if ($this->FAppend) {
            $mUrl .= '/' . $Router->CreatePathInfo($Parameters, '/', '/') . $mSuffix;
        }
        else {
            if ($mUrl != '') {
                $mUrl .= $mSuffix;
            }
            $mUrl .= '?' . $Router->CreatePathInfo($Parameters, '=', $Ampersand);
        }

        return $mUrl;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getAppend() {
        return $this->FAppend;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCaseSensitive() {
        return $this->FCaseSensitive;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCheckParameters() {
        return $this->FCheckParameters;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    public function getDefaultParameters() {
        return $this->FDefaultParameters;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getHasHostInfo() {
        return $this->FHasHostInfo;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    public function getParameters() {
        return $this->FParameters;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getParsingOnly() {
        return $this->FParsingOnly;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getPattern() {
        return $this->FPattern;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    public function getReferences() {
        return $this->FReferences;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getRoute() {
        return $this->FRoute;
    }

    /**
     *
     * @return string
     */
    public function getRoutePattern() {
        return $this->FRoutePattern;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getTemplate() {
        return $this->FTemplate;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getUrlSuffix() {
        return $this->FUrlSuffix;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getUseInheritedAppend() {
        return $this->FUseInheritedAppend;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getUseInheritedCaseSensitive() {
        return $this->FUseInheritedCaseSensitive;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getUseInheritedCheckParameters() {
        return $this->FUseInheritedCheckParameters;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getUseInheritedSuffix() {
        return $this->FUseInheritedSuffix;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Web\THttpMethod[]
     */
    public function getVerb() {
        return $this->FVerb;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Web\IUrlRouter $Router
     * @param \FrameworkDSW\Web\THttpRequest $Request
     * @param string $PathInfo
     * @param string $RawPathInfo
     * @throws EParseUrlFailed
     * @return string
     */
    public function ParseUrl($Router, $Request, $PathInfo, $RawPathInfo) {
        TType::Object($Router, IUrlRouter::class);
        TType::Object($Request, THttpRequest::class);
        TType::String($PathInfo);
        TType::String($RawPathInfo);

        if (($this->FUseInheritedCaseSensitive && $Router->getCaseSensitive()) || (!$this->FUseInheritedCaseSensitive && $this->FCaseSensitive)) {
            $mCaseSensitive = '';
        }
        else {
            $mCaseSensitive = 'i';
        }

        if ($this->FUrlSuffix != '') {
            $PathInfo = $Router->RemoveUrlSuffix($RawPathInfo, $this->FUrlSuffix);
        }

        // URL suffix required, but not found in the requested URL
        if ($Router->getUseStrictParsing() && $PathInfo == $RawPathInfo) {
            $mUrlSuffix = $this->FUseInheritedSuffix ? $Router->getUrlSuffix() : $this->FUrlSuffix;
            if ($mUrlSuffix != '' && $mUrlSuffix != '/') {
                throw new EParseUrlFailed(sprintf('Parse URL failed.'));
            }
        }

        if ($this->FHasHostInfo) {
            $PathInfo = strtolower($Request->getHostInfo()) . rtrim("/{$PathInfo}", '/');
        }

        $PathInfo .= '/';

        if (preg_match("{$this->FPattern}{$mCaseSensitive}", $PathInfo, $mMatches)) {
            if ($this->FDefaultParameters != null) {
                foreach ($this->FDefaultParameters as $mName => $mValue) {
                    if (!isset($_GET[$mName])) {
                        $_REQUEST[$mName] = $mValue;
                        $_GET[$mName]     = $mValue;
                    }
                }
            }
            $mTr = [];

            if ($this->FReferences == null) {
                TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
                $this->FReferences = new TMap();
            }
            if ($this->FParameters == null) {
                TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
                $this->FParameters = new TMap();
            }

            foreach ($mMatches as $mKey => $mValue) {
                if ($this->FReferences->ContainsKey($mKey)) {
                    $mTr[$this->FReferences[$mKey]] = $mValue;
                }
                elseif ($this->FParameters->ContainsKey($mKey)) {
                    $_REQUEST[$mKey] = $mValue;
                    $_GET[$mKey]     = $mValue;
                }
            }
            if ($PathInfo !== $mMatches[0]) { // there're additional GET params
                $Router->ParsePathInfo(ltrim(substr($PathInfo, strlen($mMatches[0])), '/'));
            }
            if ($this->FRoutePattern != '') {
                return strtr($this->FRoute, $mTr);
            }
            else {
                return $this->FRoute;
            }
        }
        else {
            throw new EParseUrlFailed(sprintf('Parse URL failed.'));
        }
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setAppend($Value) {
        TType::Bool($Value);
        $this->FAppend = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setCaseSensitive($Value) {
        TType::Bool($Value);
        $this->FCaseSensitive = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setCheckParameters($Value) {
        TType::Bool($Value);
        $this->FCheckParameters = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Value <K: string, V: string>
     */
    public function setDefaultParameters($Value) {
        TType::Object($Value, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        Framework::Free($this->FDefaultParameters);
        $this->FDefaultParameters = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setHasHostInfo($Value) {
        TType::Bool($Value);
        $this->FHasHostInfo = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Value <K: string, V: string>
     */
    public function setParameters($Value) {
        TType::Object($Value, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        Framework::Free($this->FParameters);
        $this->FParameters = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setParsingOnly($Value) {
        TType::Bool($Value);
        $this->FParsingOnly = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setPattern($Value) {
        TType::String($Value);
        $this->FPattern = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Value <K: string, V: string>
     */
    public function setReferences($Value) {
        TType::Object($Value, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        Framework::Free($this->FReferences);
        $this->FReferences = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setRoute($Value) {
        TType::String($Value);
        $this->FRoute = $Value;
    }

    /**
     *
     * @param string $Value
     */
    public function setRoutePattern($Value) {
        TType::String($Value);
        $this->FRoutePattern = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setTemplate($Value) {
        TType::String($Value);
        $this->FTemplate = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function setUrlSuffix($Value) {
        TType::String($Value);
        $this->FUrlSuffix = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setUseInheritedAppend($Value) {
        TType::Bool($Value);
        $this->FUseInheritedAppend = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setUseInheritedCaseSensitive($Value) {
        TType::Bool($Value);
        $this->FUseInheritedCaseSensitive = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setUseInheritedCheckParameters($Value) {
        TType::Bool($Value);
        $this->FUseInheritedCheckParameters = $Value;
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function setUseInheritedSuffix($Value) {
        TType::Bool($Value);
        $this->FUseInheritedSuffix = $Value;
    }

    /**
     * descHere
     *
     * @param THttpMethod[] $Value
     */
    public function setVerb($Value) {
        TType::Arr($Value);
        $this->FVerb = $Value;
    }
}

/**
 * Class TWebApplication
 * @package FrameworkDSW\Web
 */
class TWebApplication extends TComponent implements IApplication {
    /**
     * @var \FrameworkDSW\Controller\TControllerManager
     */
    private $FControllerManager = null;
    /**
     * @var \FrameworkDSW\Web\TUrlRouter
     */
    private $FRouter = null;
    /**
     * @var \FrameworkDSW\Web\THttpRequest
     */
    private $FRequest = null;
    /**
     * @var \FrameworkDSW\System\IInterface[]
     */
    private $FControllers = [];

    /**
     * @param \FrameworkDSW\CoreClasses\TComponent $Owner
     */
    public function __construct($Owner = null) {
        parent::__construct($Owner);
        TType::Object($Owner, TComponent::class);

        $this->FControllerManager = new TControllerManager();
        $this->FRequest           = new THttpRequest();
        $this->FRouter            = new TUrlRouter($this->FRequest);
    }

    /**
     * @return \FrameworkDSW\Controller\IControllerManager
     */
    public function getControllerManager() {
        return $this->FControllerManager;
    }

    /**
     * @return \FrameworkDSW\Web\THttpRequest
     */
    public function getHttpRequest() {
        return $this->FRequest;
    }

    /**
     * @return \FrameworkDSW\Web\TUrlRouter
     */
    public function getUrlRouter() {
        return $this->FRouter;
    }

    /**
     * Run
     */
    public function Run() {
        //fixme config reading for rules. BEGIN
        $mRouterRule = new TUrlRouteRule('T<controller>/<action>', '<controller>/<action>');
        $this->FRouter->AddRule($mRouterRule);
        //fixme config reading for rules. END

        $this->FRouter->setUseStrictParsing(true);
        $mPathInfo = $this->FRouter->ParseUrl();
        list($mControllerName, $mActionName) = explode('/', $mPathInfo);
        $this->FRouter->ParsePathInfo($mPathInfo);

        //TODO config reading.

        TClass::PrepareGeneric(['T' => $mControllerName]);
        $mControllerClass     = new TClass();
        $mController          = $mControllerClass->NewInstance([]);
        $this->FControllers[] = $mController;
        $mAction              = Framework::Delegate([$mController, $mActionName . 'Action'], TControllerAction::class);
        $mModelBinder         = Framework::Delegate([$mController, $mActionName . 'ModelBinder'], TModelBinder::class);
        $mViewBinder          = Framework::Delegate([$mController, $mActionName . 'ViewBinder'], TViewBinder::class);
        /** @var TModelBinder $mModelBinder */
        /** @var TControllerAction $mAction */
        /** @var TViewBinder $mViewBinder */
        $this->FControllerManager->RegisterModel($mAction, $mModelBinder);
        $this->FControllerManager->RegisterView($mAction, $mViewBinder);
        //...sub actions

        $this->FControllerManager->Update($mAction);

        $this->Quit();
    }

    /**
     * Quit
     */
    public function Quit() {
        Framework::Free($this->FControllerManager);
        Framework::Free($this->FRouter);
        Framework::Free($this->FRequest);

        foreach ($this->FControllers as $mItem) {
            Framework::Free($mItem);
        }
        //... free model
        //... free view
        //... and sub actions, models, views.
    }

    /**
     * @return \FrameworkDSW\Web\TWebApplication
     */
    public static function Application() {
        $mApp = Framework::Application();
        if ($mApp instanceof TWebApplication) {
            return $mApp;
        }
        else {
            return null;
        }
    }
}