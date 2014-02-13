<?php
namespace testHMVC;
set_include_path(get_include_path() . PATH_SEPARATOR . '../');
require_once 'FrameworkDSW/Framework.php';
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Controller\TControllerAction;
use FrameworkDSW\Controller\TControllerManager;
use FrameworkDSW\Controller\TModelBinder;
use FrameworkDSW\Controller\TOnSetControllerManagerUpdate;
use FrameworkDSW\Controller\TViewBinder;
use FrameworkDSW\CoreClasses\IView;
use FrameworkDSW\CoreClasses\TComponent;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\TType;
use FrameworkDSW\View\Web\TWebPage;

class TMyModel extends TObject {
    /**
     * @var integer
     */
    public $FData = 10;
    /**
     * @var \FrameworkDSW\Controller\TOnModelNotify
     */
    public $FNotify = null;

    /**
     * @param \FrameworkDSW\Controller\TOnModelNotify $Notify
     */
    public function setNotify($Notify) {
        $this->FNotify = $Notify;
    }
}

class TMyController extends TObject {
    /**
     * @var \FrameworkDSW\Controller\TOnControllerManagerUpdate
     */
    public $FUpdate = null;
    /**
     * @var TMyModel
     */
    public $FModel = null;
    /**
     * @var TMyView
     */
    private $FView = null;
    /**
     * @var \FrameworkDSW\View\Web\TWebPage
     */
    private $FMainView = null;
    /**
     * @var \FrameworkDSW\View\Web\TWebPage
     */
    private $FSubView = null;

    /**
     *
     */
    public function __construct() {
        parent::__construct();
        $this->FModel    = new TMyModel();
        $this->FView     = new TMyView();
        $this->FMainView = new TWebPage();
        $this->FMainView->Config(null, 'Tests/main.php');
        $this->FMainView->setName('main');
        $this->FSubView = new TWebPage($this->FMainView);
        $this->FSubView->Config(null, 'Tests/sub.php');
        $this->FSubView->setName('sub');
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FModel);
        Framework::Free($this->FView);
        Framework::Free($this->FMainView);
        Framework::Free($this->FSubView);
        parent::Destroy();
    }

    /**
     * @param \FrameworkDSW\Controller\TOnControllerManagerUpdate $Value
     */
    public function setUpdate($Value) {
        $this->FUpdate = $Value;
    }

    /**
     * @param \FrameworkDSW\System\IInterface $Model
     * @return \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface>
     */
    public function TestAction($Model) {
        TType::Object($Model, IInterface::class);
        /** @var TMyModel $Model */
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
        $mView             = new TMap();
        $mView['ViewData'] = new TString(sprintf('There are %s little pigs.', (string)$Model->FData));
        $this->FUpdate(Framework::Delegate([$this, 'TestSubAction'], TControllerAction::class));

        return $mView;
    }

    /**
     * @param \FrameworkDSW\System\IInterface $Model
     * @return \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface>
     */
    public function TestSubAction($Model) {
        TType::Object($Model, IInterface::class);
        /** @var TMyModel $Model */
        ++$Model->FData;
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
        $mView             = new TMap();
        $mView['ViewData'] = new TString(sprintf('There are also %s little sheep.', (string)$Model->FData));
        return $mView;
    }

    /**
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\Controller\TOnModelNotify $OnModelNotify
     * @return \FrameworkDSW\System\IInterface $Model
     */
    public function ModelBinder($Action, $OnModelNotify) {
        $this->FModel->FNotify = $OnModelNotify;
        return $this->FModel;
    }

    /**
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @return \FrameworkDSW\CoreClasses\IView[]
     */
    public function MainViewBinder($Action) {
        return [$this->FView, $this->FMainView];
    }

    /**
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @return \FrameworkDSW\CoreClasses\IView[]
     */
    public function SubViewBinder($Action) {
        return [$this->FView, $this->FSubView];
    }

}

/**
 * Class TMyView
 * @package testHMVC
 */
class TMyView extends TComponent implements IView {
    /**
     * descHere
     * @param \FrameworkDSW\Containers\IMap $ViewData <K: string, V: \FrameworkDSW\System\IInterface>
     */
    public function Update($ViewData) {
        $fp = fopen('php://stderr', 'r+');
        fputs($fp, 'Hello! ' . $ViewData['ViewData']->Unbox() . "\n");
    }
}

$mControllerManager = new TControllerManager();
$mController        = new TMyController();
$mAction            = Framework::Delegate([$mController, 'TestAction'], TControllerAction::class);
$mSubAction         = Framework::Delegate([$mController, 'TestSubAction'], TControllerAction::class);
$mControllerManager->RegisterModel($mAction, Framework::Delegate([$mController, 'ModelBinder'], TModelBinder::class), Framework::Delegate([$mController, 'setUpdate'], TOnSetControllerManagerUpdate::class));
$mControllerManager->RegisterModel($mSubAction, Framework::Delegate([$mController, 'ModelBinder'], TModelBinder::class), null, false);
$mControllerManager->RegisterView($mAction, Framework::Delegate([$mController, 'MainViewBinder'], TViewBinder::class));
$mControllerManager->RegisterView($mSubAction, Framework::Delegate([$mController, 'SubViewBinder'], TViewBinder::class));
$fp = fopen('php://stderr', 'r+');
fputs($fp, sprintf("Ready: %s bytes\n", memory_get_usage(true)));
$mController->FModel->FData = 10;
$mController->FModel->FNotify($mController->FModel);
$mControllerManager->Update($mAction);
$mController->FModel->FData = 1000;
$mController->FModel->FNotify($mController->FModel);
//clean-up
fputs($fp, sprintf("Updated: %s bytes\n", memory_get_usage(true)));
Framework::Free($mControllerManager);
Framework::Free($mController);
fputs($fp, sprintf("Ended: %s bytes\n", memory_get_usage(true)));
fputs($fp, sprintf("Peek: %s bytes\n", memory_get_usage(true)));
