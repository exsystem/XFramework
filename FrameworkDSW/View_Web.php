<?php
/**
 * View_Web.php
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 52
 */

require_once 'FrameworkDSW/CoreClasses.php';
require_once 'FrameworkDSW/Containers.php';

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

        $this->FName = $Name;
        $this->FThemePath = $ThemePath;
        $this->FThemeUrl = $ThemeUrl;
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
    private static $FThemes = array ();
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
    private $FMasterPage = '';
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
     * @var TMap <K: string, V: IInterface>
     */
    private $FViewData = null;
    /**
     *
     * @var IUrlRouter
     */
    private $FRouter = null;
    /**
     *
     * @var string
     */
    private $FThemeUrl = '';

    /**
     * descHere
     *
     * @param IUrlRouter $Router
     * @param string $Template
     * @param TWebTheme $Theme
     * @param string $MasterPage
     */
    public function Config($Router, $Template, $Theme = null, $MasterPage = '') {
        TType::Object($Router, 'IUrlRouter');
        TType::String($Template);
        TType::Object($Theme, 'TWebTheme');
        TType::String($MasterPage);

        $this->FTemplate = $Template;
        $this->FTheme = $Theme;
        $this->FMasterPage = $MasterPage;
        $this->FRouter = $Router;

        if ($Theme != null) {
            $this->FThemeUrl = $this->FTheme->getThemeUrl();
        }

        // TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'IInterface'));
        // $this->FViewData = new TMap(true);


        // $mRaw = array_map(array ('Framework', 'Unserialize'), $_POST);
        // foreach ($mRaw as $mName => $mObject) {
        // TType::Object($mObject, 'IInterface');
        // $this->FViewData->Put($mName, $mObject);
        // }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     *            callback array (Controller, Action).
     * @param mixed $Parameters
     *            array('key0' => 'value0', ...)
     * @return string
     */
    public function Action($Action, $Parameters = array()) {
        TType::Arr($Action);

        $mParameters = null;
        if ($Parameters != array ()) {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
            $mParameters = new TMap();
            foreach ($Parameters as $mKey => &$mValue) {
                $mParameters->Put((string) $mKey, (string) $mValue);
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
     * @return IIntterface
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
    public function getMasterPage() {
        return $this->FMasterPage;
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
     * @param IMap $ViewData
     *            <K: string, V: IInterface>
     */
    public function Update($ViewData) {
        TType::Object($ViewData, array ('IMap' => array ('K' => 'string', 'V' => 'IInterface')));
        $this->FViewData = $ViewData;

        require_once $this->FTemplate;
        ob_end_flush();
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
     * @param IMap $ViewData <K: string, V: IInterface>
     */
    public function Update($ViewData) {
        TType::Object($ViewData, array ('IMap' => array ('K' => 'string', 'V' => 'IInterface')));

        $mResult = null;
        $mStatusStack = array ();
        $mStatusStack[] = array (self::CData => $ViewData, self::CInsertionPoint => &$mResult);

        while (count($mStatusStack) > 0) {
            unset($mCurrentStatus);
            $mCurrentStatus = array_pop($mStatusStack);

            $mCurrentData = &$mCurrentStatus[self::CData];
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
                $mStatusStack[] = array (self::CData => $mCurrentData->Value, self::CInsertionPoint => &$mInsertionPoint);
            }
            elseif ($mCurrentData instanceof IList || is_array($mCurrentData)) {
                unset($mArray);
                $mArray = array ();
                foreach ($mCurrentData as $Item) {
                    unset($mInsertionPoint);
                    $mInsertionPoint = null;
                    $mArray[] = &$mInsertionPoint;
                    $mStatusStack[] = array (self::CData => $Item, self::CInsertionPoint => &$mInsertionPoint);
                }
                $mCurrentInsertionPoint = $mArray;
            }
            elseif (($mCurrentData instanceof IMap && in_array($mCurrentData->GenericArg('K'), array ('string', 'TString'))) || $mCurrentData instanceof TRecord) {
                unset($mArray);
                $mArray = array ();
                foreach ($mCurrentData as $mItemKey => $mItemData) {
                    unset($mInsertionPoint);
                    $mInsertionPoint = null;
                    $mArray[$mItemKey] = &$mInsertionPoint;
                    $mStatusStack[] = array (self::CData => $mItemData, self::CInsertionPoint => &$mInsertionPoint);
                }
                if (count($mArray) == 0) {
                    $mCurrentInsertionPoint = new stdClass();
                }
                else {
                    $mCurrentInsertionPoint = $mArray;
                }
            }
            else {
                throw new EInvalidParameter();
            }
        }

        if ($this->FJsonpCallback == '') {
            echo json_encode($mResult);
        }
        else {
            echo $this->FJsonpCallback, '(', json_encode($mResult), ')';
        }
        ob_end_flush();
    }
}