<?php
echo memory_get_usage(true)."\n\n";

set_include_path(get_include_path().';D:\\ExSystem\\Documents\\ZendStudio9Workspace\\FrameworkDSW');

require_once 'FrameworkDSW/CoreClasses.php';
require_once 'FrameworkDSW/Controller.php';

class TMyController extends TObject implements IController {
    /**
     *
     * @var TOnControllerUpdate
     */
    private static $FOnUpdate = null;

    /**
     *
     * @param TOnControllerUpdate $Value
     */
    public static function setUpdate($Value) {
        self::$FOnUpdate = $Value;
    }

    /**
     *
     * @param TMyModel $Model
     * @return TMap
     */
    public static function ActionTest($Model) {
        TType::Object($Model, 'TMyModel');

        TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'integer'));
        $ViewData = new TMap();
        $ViewData->Put('data', strlen($Model->FMyData));
        return $ViewData;
    }
}

class TMyView extends TComponent implements IView {

    /**
     * (non-PHPdoc)
     * @param IMap $ViewData <K: string, V: IInterface>
     * @see IView::Update()
     */
    public function Update($ViewData) {
        echo "ViewData is {$ViewData['data']}\n";
    }
}

class TMyModel extends TObject implements IModel {
    /**
     *
     * @var TOnModelNotify
     */
    private $FOnNotify = null;

    /**
     *
     * @param TOnModelNotify $Value
     */
    public function setNotify($Value) {
        $this->FOnNotify = $Value;
    }

    /**
     *
     * @var string
     */
    public $FMyData = 'this is the data.';
}
//prepare
$mControllerManager = new TControllerManager();
$mView = new TMyView();
$mModel = new TMyModel();
$mControllerManager->Bind(array ('TMyController', 'Test'), $mModel);
$mControllerManager->Register(array ('TMyController', 'Test'), $mView);
//invoke
for ($i=0; $i<1; ++$i)
$mControllerManager->Update(array ('TMyController', 'Test'));
//clean-up
echo memory_get_usage(true)."\n\n";
Framework::Free($mControllerManager);
Framework::Free($mModel);
Framework::Free($mView);
echo "\nENDED\n";
echo memory_get_usage(true)."\n\n";
