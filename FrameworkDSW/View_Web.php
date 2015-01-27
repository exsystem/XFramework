<?php
/**
 * \FrameworkDSW\View\Web
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 52
 */
namespace FrameworkDSW\View\Web;

use FrameworkDSW\Containers\IList;
use FrameworkDSW\Containers\IMap;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\CoreClasses\IView;
use FrameworkDSW\CoreClasses\TComponent;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\IPrimitive;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\TType;
use FrameworkDSW\Web\IUrlRouter;
use FrameworkDSW\Web\THttpResponse;
use FrameworkDSW\Web\TWebApplication;

/**
 * TWebTheme
 *
 * @author 许子健
 */
class TWebTheme extends TObject {
    /**
     *
     * @var string
     */
    private $FName = '';
    /**
     *
     * @var string
     */
    private $FThemePath = '';
    /**
     *
     * @var string
     */
    private $FThemeUrl = '';

    /**
     * descHere
     *
     * @param string $Name
     * @param string $ThemePath
     * @param string $ThemeUrl
     */
    public function __construct($Name, $ThemePath = '', $ThemeUrl = '') {
        parent::__construct();
        TType::String($Name);
        TType::String($ThemePath);
        TType::String($ThemeUrl);

        $this->FName      = $Name;
        $this->FThemePath = $ThemePath;
        $this->FThemeUrl  = $ThemeUrl;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getThemePath() {
        return $this->FThemePath;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getThemeUrl() {
        return $this->FThemeUrl;
    }
}

/**
 * TWebThemeManager
 *
 * @author 许子健
 */
class TWebThemeManager extends TObject {

    /**
     *
     * @var string[]
     */
    private static $FThemes = [];
    /**
     *
     * @var string
     */
    private static $FThemesPath = '';
    /**
     *
     * @var string
     */
    private static $FThemesUrl = '';

    /**
     * descHere
     *
     * @param string $Name
     * @return TWebTheme
     */
    public static function getTheme($Name) {
        TType::String($Name);

        //if (in_array($Name, self::$FThemes, true)) {
        return new TWebTheme($Name, self::$FThemesPath . $Name . '/', self::$FThemesUrl . $Name . '/');
        //}
        //else {
        //    throw new EInvalidParameter();
        //}
    }

    /**
     * descHere
     *
     * @return string[]
     */
    public static function getThemes() {
        return self::$FThemes;
    }

    /**
     * descHere
     *
     * @return string
     */
    public static function getThemesPath() {
        return self::$FThemesPath;
    }

    /**
     * descHere
     *
     * @return string
     */
    public static function getThemesUrl() {
        return self::$FThemesUrl;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public static function setThemesPath($Value) {
        TType::String($Value);
        self::$FThemesPath = $Value;
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public static function setThemesUrl($Value) {
        TType::String($Value);
        self::$FThemesUrl = $Value;
    }
}

/**
 * TWebPage
 *
 * @author 许子健
 */
class TWebPage extends TComponent implements IView {
    /**
     *
     * @var string
     */
    private $FTemplate = '';
    /**
     *
     * @var TWebTheme
     */
    private $FTheme = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface>
     */
    private $FViewData = null;
    /**
     *
     * @var \FrameworkDSW\Web\IUrlRouter
     */
    private $FRouter = null;

    /**
     * @var \FrameworkDSW\Web\THttpResponse
     */
    private $FResponse = null;
    /**
     *
     * @var string
     */
    private $FThemeUrl = '';

    /**
     * @param \FrameworkDSW\View\Web\TWebPage $WebPage
     * @param \FrameworkDSW\Containers\IMap $ViewData <K: string, V: \FrameworkDSW\System\IInterface>
     * @param boolean $CancelUpdate
     */
    public function signalBeforeUpdate($WebPage, $ViewData, &$CancelUpdate) {
    }

    /**
     * @param \FrameworkDSW\View\Web\TWebPage $WebPage
     * @param \FrameworkDSW\Containers\IMap $ViewData <K: string, V: \FrameworkDSW\System\IInterface>
     * @param boolean $UpdateCancelled
     */
    public function signalAfterUpdate($WebPage, $ViewData, $UpdateCancelled) {
    }

    /**
     * descHere
     *
     * @param string $Template
     * @param \FrameworkDSW\View\Web\TWebTheme $Theme
     * @param \FrameworkDSW\Web\IUrlRouter $Router
     * @param \FrameworkDSW\Web\THttpResponse $Response
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function Config($Template, $Theme = null, $Router = null, $Response = null) {
        TType::String($Template);
        TType::Object($Theme, TWebTheme::class);
        TType::Object($Router, IUrlRouter::class);
        TType::Object($Response, THttpResponse::class);

        $this->FTemplate = $Template;
        $this->FTheme    = $Theme;

        if ($Router === null) {
            $this->FRouter = TWebApplication::$FApplication->getUrlRouter();
        }
        else {
            $this->FRouter = $Router;
        }

        if ($Response === null) {
            $this->FResponse = TWebApplication::$FApplication->getHttpResponse();
        }
        else {
            $this->FResponse = $Response;
        }

        if ($Theme != null) {
            $this->FThemeUrl = $this->FTheme->getThemeUrl();
        }

        $this->FResponse->getHeaders()->Clear();
    }

    /**
     * descHere
     *
     * @param string[] $Action
     *            array (Controller, Action).
     * @param mixed $Parameters
     *            array('key0' => 'value0', ...)
     * @return string
     */
    public function Action($Action, $Parameters = []) {
        TType::Arr($Action);

        $mParameters = null;
        if ($Parameters != []) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
            $mParameters = new TMap();
            foreach ($Parameters as $mKey => &$mValue) {
                $mParameters->Put((string)$mKey, (string)$mValue);
            }
        }

        return $this->FRouter->CreateUrl("{$Action[0]}/{$Action[1]}", $mParameters);
    }

    /**
     * descHere
     *
     * @param string $Name
     * @return string
     */
    public function Css($Name) {
        TType::String($Name);

        return "{$this->FThemeUrl}Style/{$Name}.css";
    }

    /**
     * descHere
     *
     * @param string $Name
     * @return mixed
     */
    public function Data($Name) {
        TType::String($Name);

        $mRaw = $this->FViewData[$Name];
        if ($mRaw instanceof IPrimitive) {
            return $mRaw->UnboxToString();
        }
        else {
            return $mRaw;
        }
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
     * @return TWebTheme
     */
    public function getTheme() {
        return $this->FTheme;
    }

    /**
     * descHere
     *
     * @param string $Name
     * @return string
     */
    public function Js($Name) {
        TType::String($Name);

        return "{$this->FThemeUrl}Script/{$Name}.js";
    }

    /**
     * descHere //render.
     *
     * @param \FrameworkDSW\Containers\IMap $ViewData <K: string, V: \FrameworkDSW\System\IInterface>
     */
    public function Update($ViewData) {
        TType::Object($ViewData, [IMap::class => ['K' => Framework::String, 'V' => IInterface::class]]);
        $this->FViewData = $ViewData;

        $mCancelUpdate = false;
        TObject::Dispatch([$this, 'BeforeUpdate'], [$this, $this->FViewData, &$mCancelUpdate]);

        if (!$mCancelUpdate) {
            if (!($this->getOwner() instanceof TWebPage)) {
                $this->FResponse->setContent('');
                /** @noinspection PhpIncludeInspection */
                require_once $this->FTemplate;
            }
        }

        TObject::Dispatch([$this, 'AfterUpdate'], [$this, $this->FViewData, $mCancelUpdate]);
    }

    /**
     * @param string $Name
     */
    public function View($Name) {
        TType::String($Name);
        $mView = $this->FindComponent($Name);
        if ($mView instanceof TWebPage) {
            /** @noinspection PhpIncludeInspection */
            require_once $mView->FTemplate;
        }
    }

    /**
     * @param string $Code
     * @param mixed[] $Arguments
     * @return string
     */
    public function Translate($Code, $Arguments = []) {
        TType::String($Code);
        TType::Type($Arguments, Framework::Variant . '[]');

        return TWebApplication::$FApplication->getInternationalizationManager()->TranslateMessage($Code, $Arguments);
    }
}

/**
 *
 * @author 许子健
 */
class TJsonView extends TComponent implements IView {
    /**
     *
     * @var integer
     */
    const CData = 0;
    /**
     *
     * @var integer
     */
    const CInsertionPoint = 1;
    /**
     *
     * @var string
     */
    private $FJsonpCallback = '';

    /**
     * @var \FrameworkDSW\Web\THttpResponse
     */
    private $FResponse = null;

    /**
     * @param \FrameworkDSW\Web\THttpResponse $Response
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function Config($Response = null) {
        TType::Object($Response, THttpResponse::class);
        if ($Response === null) {
            $this->FResponse = TWebApplication::$FApplication->getHttpResponse();
        }
        else {
            $this->FResponse = $Response;
        }
    }

    /**
     * @return string
     */
    public function getJsonpCallback() {
        return $this->FJsonpCallback;
    }

    /**
     *
     * @param string $Value
     */
    public function setJsonpCallback($Value) {
        TType::String($Value);
        $this->FJsonpCallback = $Value;
    }

    /**
     *  (non-PHPdoc)
     * @see IView::Update()
     * @param \FrameworkDSW\Containers\IMap $ViewData <K: string, V: \FrameworkDSW\System\IInterface>
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function Update($ViewData) {
        TType::Object($ViewData, [IMap::class => ['K' => Framework::String, 'V' => IInterface::class]]);

        $mResult        = null;
        $mStatusStack   = [];
        $mStatusStack[] = [self::CData => $ViewData, self::CInsertionPoint => &$mResult];

        while (count($mStatusStack) > 0) {
            unset($mCurrentStatus);
            $mCurrentStatus = array_pop($mStatusStack);

            $mCurrentData           = &$mCurrentStatus[self::CData];
            $mCurrentInsertionPoint = &$mCurrentStatus[self::CInsertionPoint];

            if (is_scalar($mCurrentData) || $mCurrentData === null) {
                $mCurrentInsertionPoint = $mCurrentData;
            }
            elseif ($mCurrentData instanceof IPrimitive) {
                $mCurrentInsertionPoint = $mCurrentData->Unbox();
            }
            elseif ($mCurrentData instanceof TPair) {
                if (is_string($mCurrentData->Key)) {
                    $mCurrentInsertionPoint[$mCurrentData->Key] = null;
                }
                elseif ($mCurrentData->Key instanceof TString) {
                    $mCurrentInsertionPoint[$mCurrentData->Key->Unbox()] = null;
                }
                else {
                    throw new EInvalidParameter();
                }
                unset($mInsertionPoint);
                $mInsertionPoint = &$mCurrentInsertionPoint[$mCurrentData->Key];
                $mStatusStack[]  = [self::CData => $mCurrentData->Value, self::CInsertionPoint => &$mInsertionPoint];
            }
            elseif ($mCurrentData instanceof IList || is_array($mCurrentData)) {
                unset($mArray);
                $mArray = [];
                foreach ($mCurrentData as $Item) {
                    unset($mInsertionPoint);
                    $mInsertionPoint = null;
                    $mArray[]        = &$mInsertionPoint;
                    $mStatusStack[]  = [self::CData => $Item, self::CInsertionPoint => &$mInsertionPoint];
                }
                $mCurrentInsertionPoint = $mArray;
            }
            elseif (($mCurrentData instanceof IMap && in_array($mCurrentData->GenericArg('K'), [Framework::String, TString::class])) || $mCurrentData instanceof TRecord) {
                unset($mArray);
                $mArray = [];
                foreach ($mCurrentData as $mItemKey => $mItemData) {
                    unset($mInsertionPoint);
                    $mInsertionPoint   = null;
                    $mArray[$mItemKey] = &$mInsertionPoint;
                    $mStatusStack[]    = [self::CData => $mItemData, self::CInsertionPoint => &$mInsertionPoint];
                }
                if (count($mArray) == 0) {
                    $mCurrentInsertionPoint = new \stdClass();
                }
                else {
                    $mCurrentInsertionPoint = $mArray;
                }
            }
            else {
                throw new EInvalidParameter();
            }
        }

        if ($this->FResponse === null) {
            $this->FResponse = TWebApplication::$FApplication->getHttpResponse();
        }

        if ($this->FJsonpCallback == '') {
            $this->FResponse->setContent(json_encode($mResult));
        }
        else {
            $this->FResponse->setContent($this->FJsonpCallback . '(' . json_encode($mResult) . ')');
        }
    }
}