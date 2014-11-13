<?php
/**
 * \FrameworkDSW\Configuration\Json
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 91
 */
namespace FrameworkDSW\Configuration\Json;

use FrameworkDSW\Configuration\IConfigurationStorage;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\ERuntimeException;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

//FIXME exception type
/**
 * Class TJsonStorage
 * @package FrameworkDSW\Configuration\Json
 */
class TJsonStorage extends TObject implements IConfigurationStorage {
    /**
     * @var string
     */
    private $FFileName = '';

    /**
     * @param string $FileName
     */
    public function __construct($FileName) {
        parent::__construct();
        TType::String($FileName);
        $this->FFileName = $FileName;
    }

    /**
     * @throws \FrameworkDSW\System\EException
     * @return \FrameworkDSW\Containers\TMap <K: string, V: mixed>
     */
    public function Load() {
        $mContent = file_get_contents($this->FFileName);
        if ($mContent === false) {
            throw new ERuntimeException(sprintf('Unable read file: "%s".', $this->FFileName));
        }
        else {
            $mArray = json_decode($mContent, true);
            if (is_array($mArray) && array_diff_key($mArray, array_keys(array_keys($mArray))) === false) {
                throw new ERuntimeException(sprintf('Wrong structure: "%s".', $this->FFileName));
            }

            $mStack = [];

            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::Variant]);
            $mResult = new TMap();

            /** @var string $mKey */
            foreach ($mArray as $mKey => &$mValue) {
                if (is_array($mValue) && array_diff_key($mValue, array_keys(array_keys($mValue)))) {
                    $mStack[] = [$mKey, $mValue];
                }
                else {
                    $mResult->Put($mKey, $mValue);
                }
            }

            while (count($mStack) > 0) {
                $mCurrentPair = array_pop($mStack);
                /** @var string $mCurrentPath */
                $mCurrentPath = $mCurrentPair[0];
                $mCurrentData = $mCurrentPair[1];
                /** @var string $mKey */
                foreach ($mCurrentData as $mKey => &$mValue) {
                    if (is_array($mValue) && array_diff_key($mValue, array_keys(array_keys($mValue)))) {
                        $mStack[] = ["{$mCurrentPath}.{$mKey}", $mValue];
                    }
                    else {
                        $mResult->Put("{$mCurrentPath}.{$mKey}", $mValue);
                    }
                }
            }

            return $mResult;
        }
    }

    /**
     * @param  \FrameworkDSW\Containers\TMap <K: string, V: mixed> $Config
     * @throws EException
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function Flush($Config) {
        TType::Object($Config, [TMap::class => ['K' => Framework::String, 'V' => Framework::Variant]]);

        try {
            $mTempArray = [];
            $mTemp      = &$mTempArray;
            foreach ($Config as $mKey => $mValue) {
                $mKeys = explode('.', $mKey);
                foreach ($mKeys as $k) {
                    $mTemp = &$mTemp[$k];
                }
                $mTemp = $mValue;
                $mTemp = &$mTempArray;
            }

            $mResult = json_encode($mTempArray);
            file_put_contents($this->FFileName, $mResult);
        }
        catch (ERuntimeException $Ex) {
            throw new ERuntimeException('Failed to set file contents.');
        }
    }
}