<?php
namespace testHMVC;
require_once 'FrameworkDSW/Framework.php';
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Controller\TControllerAction;
use FrameworkDSW\Controller\TControllerManager;
use FrameworkDSW\Controller\TOnSetControllerManagerUpdate;
use FrameworkDSW\Controller\TOnSetModelNotify;
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

    public function TestSubAction($Model) {
        TType::Object($Model, IInterface::class);
        /** @var TMyModel $Model */
        ++$Model->FData;
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
        $mView             = new TMap();
        $mView['ViewData'] = new TString(sprintf('There are also %s little sheep.', (string)$Model->FData));
        return $mView;
    }
}

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
$mModel             = new TMyModel();
$mView              = new TMyView();
$mMainView          = new TWebPage();
$mMainView->Config(null, 'Tests/main.php');
$mMainView->setName('main');
$mSubView = new TWebPage($mMainView);
$mSubView->Config(null, 'Tests/sub.php');
$mSubView->setName('sub');

$mControllerManager->RegisterModel($mAction, $mModel, Framework::Delegate([$mController, 'setUpdate'], TOnSetControllerManagerUpdate::class), Framework::Delegate([$mModel, 'setNotify'], TOnSetModelNotify::class));
$mControllerManager->RegisterModel($mSubAction, $mModel, null, null, false);
$mControllerManager->RegisterView($mAction, $mView);
$mControllerManager->RegisterView($mAction, $mMainView);
$mControllerManager->RegisterView($mSubAction, $mView);
$mControllerManager->RegisterView($mSubAction, $mSubView);
echo 'Ready: ', memory_get_usage(true), "bytes\n";
$mModel->FData = 10;
$mModel->FNotify($mModel);
$mControllerManager->Update($mAction);
$mModel->FData = 1000;
$mModel->FNotify($mModel);
//clean-up
echo 'Updated: ', memory_get_usage(true), " bytes\n";
Framework::Free($mControllerManager);
Framework::Free($mController);
Framework::Free($mModel);
Framework::Free($mView);
Framework::Free($mMainView);
Framework::Free($mSubView);
echo 'Ended: ', memory_get_usage(true), "bytes\n";
echo 'Peek: ', memory_get_peak_usage(true), "bytes\n";
