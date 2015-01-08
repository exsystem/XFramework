<?php
/**
 * \FrameworkDSW\Internationalization
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 107
 */
namespace FrameworkDSW\Internationalization;

use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * Interface IResourceSource
 * @package FrameworkDSW\Internationalization
 */
interface IResourceSource extends IInterface {
    /**
     * @param string $ResourceCode
     * @return mixed
     */
    public function Get($ResourceCode);

    /**
     * @return \FrameworkDSW\Internationalization\TLocale
     */
    public function getLocale();

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Value
     */
    public function setLocale($Value);
}

/**
 * Class EInternationalization
 * @package FrameworkDSW\Internationalization
 */
class EInternationalization extends EException {
    /**
     * @var string
     */
    private $FErrorName = '';

    /**
     * @param integer $ErrorCode
     * @param string $ErrorName
     * @param string $Message
     */
    public function __construct($ErrorCode, $Message, $ErrorName = '') {
        parent::__construct($Message);
        TType::Int($ErrorCode);
        TType::String($ErrorName);
        TType::String($Message);

        if ($ErrorName == '') {
            $ErrorName = intl_error_name($ErrorCode);
        }
        $this->FErrorName = $ErrorName;
        $this->code       = $ErrorCode;
    }

    /**
     * @return string
     */
    public function getErrorName() {
        return $this->FErrorName;
    }
}

/**
 * Class TLocaleBuilder
 * @package FrameworkDSW\Internationalization
 */
class TLocaleBuilder extends TObject {
    /**
     * @var string
     */
    private $FPrimaryLanguage = '';
    /**
     * @var string
     */
    private $FExtendedLanguage = '';
    /**
     * @var string
     */
    private $FScript = '';
    /**
     * @var string
     */
    private $FRegion = '';
    /**
     * @var string[]
     */
    private $FVariants = [];
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: string>
     */
    private $FExtensions = null;

    public function Destroy() {
        Framework::Free($this->FExtensions);

        parent::Destroy();
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     */
    public function Clear() {
        $this->FPrimaryLanguage  = '';
        $this->FExtendedLanguage = '';
        $this->FScript           = '';
        $this->FRegion           = '';
        $this->FVariants         = [];
        $this->ClearExtensions();
        return $this;
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     */
    public function ClearExtensions() {
        if ($this->FExtensions === null) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
            $this->FExtensions = new TMap();
        }
        else {
            $this->FExtensions->Clear();
        }
        return $this;
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocale
     */
    public function Build() {
        $mLocale = '';
        if ($this->FPrimaryLanguage != '') {
            $mLocale = $this->FPrimaryLanguage;
            if ($this->FExtendedLanguage != '') {
                $mLocale .= "-{$this->FExtendedLanguage}";
            }
        }
        if ($this->FScript != '') {
            $mLocale .= "-{$this->FScript}";
        }
        if ($this->FRegion != '') {
            $mLocale .= "-{$this->FRegion}";
        }
        if (count($this->FVariants) > 0) {
            $mLocale .= ('-' . implode('-', $this->FVariants));
        }
        if ($this->FExtensions !== null) {
            foreach ($this->FExtensions as $mKey => $mValue) {
                $mLocale .= "-{$mKey}-{$mValue}";
            }
        }

        return new TLocale($mLocale);
    }

    /**
     * @param string $Value
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function SetPrimaryLanguage($Value) {
        TType::String($Value);
        $this->FPrimaryLanguage = $Value;
        return $this;
    }

    /**
     * @param string $Value
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function SetExtendedLanguage($Value) {
        TType::String($Value);
        $this->FExtendedLanguage = $Value;
        return $this;
    }

    /**
     * @param string $Value
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function SetScript($Value) {
        TType::String($Value);
        $this->FScript = $Value;
        return $this;
    }

    /**
     * @param string $Value
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function SetRegion($Value) {
        TType::String($Value);
        $this->FRegion = $Value;
        return $this;
    }

    /**
     * @param string[] $Value
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function SetVariants($Value) {
        TType::Type($Value, Framework::String . '[]');
        $this->FVariants = $Value;
        return $this;
    }

    /**
     * @param string $Key
     * @param string $Value
     * @return \FrameworkDSW\Internationalization\TLocaleBuilder
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function SetExtension($Key, $Value) {
        TType::String($Key);
        TType::String($Value);
        if ($this->FExtensions === null) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
            $this->FExtensions = new TMap();
        }
        $this->FExtensions[$Key] = $Value;
        return $this;
    }
}

/**
 * Class TLocale
 * http://tools.ietf.org/html/rfc5646
 * {Language: <PrimaryLanguage, ExtendedLanguage[]>, Script, Region, Variant[], Extension[], PrivateUse[]}
 * @package FrameworkDSW\Internationalization
 */
class TLocale extends TObject {
    /**
     * @var string
     */
    private $FLocale = '';

    /**
     * @param string $Locale
     */
    public function __construct($Locale = '') {
        TType::String($Locale);

        $this->FLocale = self::Canonicalize($Locale);
    }

    /**
     * @param string $Locale
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    private static function Canonicalize($Locale) {
        TType::String($Locale);
        return \Locale::canonicalize($Locale);
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocale[]
     */
    public static function getAvailableLocales() {
        return [];
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocale
     */
    public static function getDefault() {
        return new TLocale(\Locale::getDefault());
    }

    /**
     * @return string
     */
    public function ToLanguageTag() {
        return $this->FLocale;
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function getDisplayLanguage($Locale = null) {
        TType::Object($Locale, TLocale::class);
        return \Locale::getDisplayLanguage($this->FLocale, $Locale->FLocale);
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function getDisplayName($Locale = null) {
        TType::Object($Locale, TLocale::class);
        if ($Locale === null) {
            $mRawLocale = null;
        }
        else {
            $mRawLocale = $Locale->FLocale;
        }

        return \Locale::getDisplayName($this->FLocale, $mRawLocale);
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function getDisplayRegion($Locale = null) {
        TType::Object($Locale, TLocale::class);
        return \Locale::getDisplayRegion($this->FLocale, $Locale->FLocale);
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function getDisplayScript($Locale = null) {
        TType::Object($Locale, TLocale::class);
        return \Locale::getDisplayScript($this->FLocale, $Locale->FLocale);
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function getDisplayVariant($Locale = null) {
        TType::Object($Locale, TLocale::class);
        return \Locale::getDisplayVariant($this->FLocale, $Locale->FLocale);
    }

    /**
     * @return string
     */
    public function getLanguage() {
        return \Locale::getPrimaryLanguage($this->FLocale);
    }

    /**
     * @return string
     */
    public function getScript() {
        return \Locale::getScript($this->FLocale);
    }

    /**
     * @return string
     */
    public function getRegion() {
        return \Locale::getRegion($this->FLocale);
    }

    /**
     * @return string
     */
    public function getVariant() {
        return implode('_', \Locale::getAllVariants($this->FLocale));
    }

    /**
     * @param string $Key
     * @return string
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function getExtension($Key) {
        TType::String($Key);
        \Locale::getKeywords($Key);
    }
}

/**
 * Class TMessageFormatter
 * @package FrameworkDSW\Internationalization
 */
class TMessageFormatter extends TObject {
    /**
     * @var \FrameworkDSW\Internationalization\TLocale
     */
    private $FLocale = null;

    /**
     * @var string
     */
    private $FLocaleLanguageTag = '';

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @throws EInvalidParameter
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function __construct($Locale) {
        TType::Object($Locale, TLocale::class);

        if ($Locale === null) {
            throw new EInvalidParameter();
        }

        $this->FLocale            = $Locale;
        $this->FLocaleLanguageTag = $Locale->ToLanguageTag();
    }

    /**
     * @param string $Pattern
     * @param mixed[] $Arguments
     * @return string
     * @throws EInternationalization
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function Format($Pattern, $Arguments) {
        TType::String($Pattern);
        TType::Type($Arguments, Framework::Variant . '[]');

        $mResult = \MessageFormatter::formatMessage($this->FLocaleLanguageTag, $Pattern, $Arguments);
        if ($mResult === false) {
            throw new EInternationalization(intl_get_error_code(), intl_get_error_message());
        }
        else {
            return $mResult;
        }
    }

    /**
     * @param string $Pattern
     * @param string $Message
     * @return mixed[]
     * @throws EInternationalization
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function ParseMessage($Pattern, $Message) {
        TType::String($Pattern);
        TType::String($Message);

        $mResult = \MessageFormatter::parseMessage($this->FLocaleLanguageTag, $Pattern, $Message);
        if ($mResult === false) {
            throw new EInternationalization(intl_get_error_code(), intl_get_error_message());
        }
        else {
            return $mResult;
        }
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocale
     */
    public function getLocale() {
        return $this->FLocale;
    }
}

/**
 * Class TInternationalizationManager
 * @package FrameworkDSW\Internationalization
 */
class TInternationalizationManager extends TObject {
    /**
     * @var \FrameworkDSW\Internationalization\IResourceSource
     */
    private $FResourceSource = null;

    /**
     * @var \FrameworkDSW\Internationalization\TMessageFormatter
     */
    private $FMessageFormatter = null;

    /**
     * @param \FrameworkDSW\Internationalization\IResourceSource $ResourceSource
     */
    public function __construct($ResourceSource) {
        parent::__construct();
        TType::Object($ResourceSource, IResourceSource::class);

        $this->FResourceSource = $ResourceSource;
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FMessageFormatter);

        parent::Destroy();
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocale
     */
    public function getLocale() {
        return $this->FResourceSource->getLocale();
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Value
     */
    public function setLocale($Value) {
        TType::Object($Value, TLocale::class);
        $this->FResourceSource->setLocale($Value);
        Framework::Free($this->FMessageFormatter);
    }

    /**
     * @return \FrameworkDSW\Internationalization\IResourceSource
     */
    public function getResourceSource() {
        return $this->FResourceSource;
    }

    /**
     * @param string $Code
     * @param mixed[] $Arguments
     * @throws EException
     * @throws EInternationalization
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     * @return string
     */
    public function TranslateMessage($Code, $Arguments = []) {
        TType::String($Code);
        TType::Type($Arguments, Framework::Variant . '[]');
        $Pattern = $this->FResourceSource->Get($Code);
        if (!is_string($Pattern)) {
            throw new EException('resource is not a string.');
        }
        return $this->getMessageFormatter()->Format($Pattern, $Arguments);
    }

    /**
     * @return \FrameworkDSW\Internationalization\TMessageFormatter
     */
    public function getMessageFormatter() {
        if ($this->FMessageFormatter === null) {
            $this->FMessageFormatter = new TMessageFormatter($this->FResourceSource->getLocale());
        }
        return $this->FMessageFormatter;
    }

    /**
     * @param string $Code
     * @return mixed
     */
    public function Translate($Code) {
        TType::String($Code);
        return $this->FResourceSource->Get($Code);
    }
}

/**
 * Class TJsonResourceSource
 * @package FrameworkDSW\Internationalization
 */
class TJsonResourceSource extends TObject implements IResourceSource {
    /**
     * @var \FrameworkDSW\Internationalization\TLocale
     */
    private $FLocale = null;
    /**
     * @var string
     */
    private $FResourceFileBasePath = '';

    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: mixed>
     */
    private $FResource = null;

    /**
     * @param string $ResourceFileBasePath
     * @param \FrameworkDSW\Internationalization\TLocale $Locale
     * @throws EInvalidParameter
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function __construct($ResourceFileBasePath = '', $Locale = null) {
        parent::__construct();
        TType::String($ResourceFileBasePath);
        TType::Object($Locale, TLocale::class);

        $this->FResourceFileBasePath = $ResourceFileBasePath;
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::Variant]);
        $this->FResource = new TMap(true);
        if ($Locale !== null) {
            $this->setLocale($Locale);
        }
    }

    /**
     * @param \FrameworkDSW\Internationalization\TLocale $Value
     * @throws EInvalidParameter
     */
    public function setLocale($Value) {
        TType::Object($Value, TLocale::class);

        if ($Value === null) {
            throw new EInvalidParameter();
        }

        $this->FLocale = $Value;
        $mLanguageTag  = $Value->ToLanguageTag();

        $mRaw = json_decode(file_get_contents("{$this->FResourceFileBasePath}{$mLanguageTag}.json"), true);
        $this->FResource->Clear();
        foreach ($mRaw as $mKey => $mValue) {
            $this->FResource->Put($mKey, $mValue);
        }
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FResource);
        parent::Destroy();
    }

    /**
     * @param string $ResourceCode
     * @return mixed
     */
    public function Get($ResourceCode) {
        TType::String($ResourceCode);
        return $this->FResource[$ResourceCode];
    }

    /**
     * @return \FrameworkDSW\Internationalization\TLocale
     */
    public function getLocale() {
        return $this->FLocale;
    }
}
