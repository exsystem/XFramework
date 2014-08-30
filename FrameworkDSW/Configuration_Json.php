<?php
/**
 * \FrameworkDSW\Configuration\PhpArray
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 91
 */
namespace FrameworkDSW\Configuration\PhpArray;

use FrameworkDSW\Configuration\IConfigurationStorage;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * Class TJsonStorage
 * @package FrameworkDSW\Configuration\PhpArray
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
        $this->FFileName  =$FileName;
    }

    /**
     * @throws \FrameworkDSW\System\EException
     * @return \FrameworkDSW\Containers\TMap <K: string, V: mixed>
     */
    public function Load() {
        $mContent = file_get_contents($this->FFileName);
        if ($mContent === false) {
            throw new EException(sprintf('Unable read file: "%s".', $this->FFileName));
        }
        else {
            $mArr = json_decode($mContent, true);
            if (is_array($mArr) === false) {
                throw new EException(sprintf('Wrong structure: "%s".', $this->FFileName));
            }
            TMap::PrepareGeneric(['K' => Framework::String, 'V'=>Framework::Variant]);
            $mResult = new TMap();
            return $mResult;
        }
    }

    /**
     * @param \FrameworkDSW\Containers\TMap <K: string, V: mixed> $Config
     */
    public function Flush($Config) {
        // TODO: Implement Flush() method.
    }
}